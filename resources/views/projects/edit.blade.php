@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Edit Project') }}</div>
            <div class="card-body">
               <div class="alert alert-success" style="display:none;"></div>
               <div class="alert alert-danger" style="display:none;"></div>
               <form id="projectForm" action="{{ route('projects.update', $project->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="form-group">
                     <label for="name">Name:</label>
                     <input type="text" class="form-control" id="name" name="name" value="{{ $project->name }}" maxlength="255" required>
                  </div>
                  <div class="form-group">
                     <label for="description">Description:</label>
                     <textarea class="form-control" id="description" name="description">{{ $project->description }}</textarea>
                  </div>
                  <div class="form-group">
                     <label for="start_date">Start Date:</label>
                     <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $project->start_date }}"  min="{{date('Y-m-d')}}" required>
                  </div>
                  <div class="form-group">
                     <label for="end_date">End Date:</label>
                     <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $project->end_date }}"  min="{{date('Y-m-d')}}" required>
                  </div>
                  <div class="form-group">
                     <label for="team_member">Team Members:</label>
                     <select class="form-control select2" multiple name="team_member[]" id="team_member" required>
                        @foreach($teamMembers as $value)
                           <option value="{{$value->id}}" @if(in_array($value->id, explode(',',$project->team_member))){{'selected'}}@endif>{{$value->name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <button type="submit" class="btn btn-success" id="submitBtn">Update</button>
                  <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back</a>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')

<script>
   $(document).ready(function(){
   $("#submitBtn").on('click',function() {
      $.validator.addMethod("endDateAfterStartDate", function(value, element) {
         var startDateValue = $('#start_date').val();
         return Date.parse(value) >= Date.parse(startDateValue);
      }, "End date must be greater than or equal to start date");

      $('#projectForm').validate({
         rules: {
            name: {
               required: true
            },
            description: {
               required: true
            },
            start_date: {
               required: true
            },
            end_date: {
               required: true,
               endDateAfterStartDate: true
            },
            'team_member[]': {
               required: true,
               minlength: 1
            }
         },
         messages: {
            name: {
               required: "Please enter a name"
            },
            description: {
               required: "Please enter a description"
            },
            start_date: {
               required: "Please select a start date"
            },
            end_date: {
               required: "Please select an end date"
            },
            'team_member[]': {
               required: "Please select at least one team member"
            }
         },
         errorElement: 'span',
         errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
         },
         highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
         },
         unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
         }
      });

      if ($('#projectForm').valid()) {
            $('.alert').hide();
            $("#submitBtn").text("Please wait..");
            $("#submitBtn").prop("disabled", true);
            $.ajax({
                url: $('#projectForm').attr('action'),
                type: 'POST',
                data: $('#projectForm').serialize(),
                success: function(response) {
                    if (response.success) {
                        $('.alert-success').show().text(response.success);
                        setTimeout(function() {
                            window.location.href = '{{ route("projects.index") }}';
                        }, 3000);
                    }
                },
                error: function(xhr, status, error) {
                    $('#submitBtn').text("Update");
                    $('#submitBtn').prop("disabled", false);
                    var errors = JSON.parse(xhr.responseText);
                    $.each(errors, function(key, value) {
                        $('.alert-danger').show().append('<li>' + value + '</li>');
                    });
                }
            });
        }

   });
});
</script>
@endpush

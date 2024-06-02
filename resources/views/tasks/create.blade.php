@extends('layouts.app')
@section('content')

<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Add Task') }}</div>
            <div class="card-body">
               <div class="alert alert-success" style="display:none;"></div>
               <div class="alert alert-danger" style="display:none;"></div>
               <form id="taskForm" action="{{ route('tasks.store') }}" method="POST">
                  @csrf
                  <div class="form-group">
                     <label for="project_id">Project:</label>
                     <select class="form-control" name="project_id" id="project_id" required>
                        <option value="">Select</option>
                        @foreach($projects as $value)
                           <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="task">Task:</label>
                     <textarea class="form-control" id="task" name="task" required></textarea>
                  </div>
                  <div class="form-group">
                     <label for="assigned_to">Assigned To:</label>
                     <select class="form-control select2" name="assigned_to" id="assigned_to" required>
                        <option value="">Select</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="status">Status:</label>
                     <select class="form-control" name="status" id="status" required>
                        @foreach($task_status as $value)
                           <option value="{{$value['key']}}">{{$value['value']}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="form-group">
                     <button type="submit" class="btn btn-success" id="submitBtn">Submit</button>
                     <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back</a>
                  </div>
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

      $('#taskForm').validate({
         rules: {
            project_id: {
               required: true
            },
            task: {
               required: true
            },
            assigned_to: {
               required: true
            },
            status: {
               required: true
            }
         },
         messages: {
            project_id: {
               required: "Please select a project"
            },
            task: {
               required: "Please enter a task"
            },
            assigned_to: {
               required: "Please select a person to assign"
            },
            status: {
               required: "Please select a status"
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

      if ($('#taskForm').valid()) {
         $('.alert').hide();
         $("#submitBtn").text("Please wait..");
         $("#submitBtn").prop("disabled",true);
         $.ajax({
            url: '{{ route("tasks.store") }}',
            type: 'POST',
            data: $('#taskForm').serialize(),
            success: function(response) {
               if (response.success) {
                  $('.alert-success').show();
                  $('.alert-success').text(response.success).show();
                  setTimeout(function(){
                     window.location.href = '{{ route("tasks.index") }}';
                  },3000);
               }
            },
            error: function(xhr, status, error) {
               $("#submitBtn").text("Submit");
               $("#submitBtn").prop("disabled",false);
               var errors = JSON.parse(xhr.responseText);
               $.each(errors, function(key, value) {
                  $('.alert-danger').show();
                  $('.alert-danger').append('<li>' + value + '</li>').show();
               });
            }
         });
      }

   });

   $("#project_id").on('change',function(){
      $.ajax({
         url: '{{ route("get-team-members") }}',
         type: 'POST',
         data: {'project_id' : $("#project_id").val(),'_token':'{{csrf_token()}}'},
         success: function(response) {
            var html = '<option value="">Select</option>';
            for (var i = 0; i < response.data.length; i++) 
            {
               var data = response.data[i];
               html     += '<option value="'+data.id+'">'+data.name+'</option>';
            }
            $("#assigned_to").html(html);
         },
         error: function(xhr, status, error) {
            
         }
      });
   });
});
</script>
@endpush

@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Add User') }}</div>
            <div class="card-body">
               <div class="alert alert-success" style="display:none;"></div>
               <div class="alert alert-danger" style="display:none;"></div>
               <form id="userForm">
                  @csrf
                  <div class="form-group">
                     <label for="name">Name:</label>
                     <input type="text" class="form-control" id="name" name="name" maxlength="255" required>
                  </div>
                  <div class="form-group">
                     <label for="email">Email:</label>
                     <input type="email" class="form-control" id="email" name="email" maxlength="255" required>
                  </div>
                  <div class="form-group">
                     <label for="email">Password:</label>
                     <input type="text" class="form-control" id="password" name="password" maxlength="30" required>
                  </div>
                  <div class="form-group">
                     <label for="role">Role:</label>
                     <select class="form-control" name="role" id="role" required>
                        <option value="">Select</option>
                        @foreach($roles as $value)
                            <option value="{{$value->name}}">{{$value->name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <div class="form-group">
                     <button type="button" class="btn btn-success" id="submitBtn">Submit</button>
                     <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
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
      $.validator.addMethod("validatePassword", function(value, element) {
         return /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[0-9])(?=.*[a-z]).{8,}$/.test(value);
      }, "Password must be at least 8 characters long and contain at least one uppercase letter, one special character, and one symbol.");

      $('#userForm').validate({
         rules: {
            name: {
               required: true,
               maxlength: 255
            },
            email: {
               required: true,
               email: true,
               maxlength: 255
            },
            password: {
               required: true,
               minlength: 8,
               validatePassword: true
            },
            role: {
               required: true
            }
         },
         messages: {
            name: {
               required: "Please enter the user's name.",
               maxlength: "Name must not exceed 255 characters."
            },
            email: {
               required: "Please enter the user's email address.",
               email: "Please enter a valid email address.",
               maxlength: "Email must not exceed 255 characters."
            },
            password: {
               required: "Please enter a password.",
               minlength: "Password must be at least 8 characters long.",
               validatePassword: "Password must contain at least one uppercase letter, one special character, and one number."
            },
            role: {
               required: "Please select a role for the user."
            }
         },
         errorElement: 'span',
         errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
         },
         highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
         },
         unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
         }
      });

      if ($('#userForm').valid()) {
         $('.alert').hide();
         $("#submitBtn").text("Please wait..");
         $("#submitBtn").prop("disabled",true);
         $.ajax({
            url: '{{ route("users.store") }}',
            type: 'POST',
            data: $('#userForm').serialize(),
            success: function(response) {
               if (response.success) {
                  $('.alert-success').show();
                  $('.alert-success').text(response.success).show();
                  setTimeout(function(){
                     window.location.href = '{{ route("users.index") }}';
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
});
</script>
@endpush

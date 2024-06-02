@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Manage Users') }}</div>
            <div class="card-body">
               <a href="{{ route('users.create') }}" class="btn btn-success mb-3">Add User</a>
               @if(session('success'))
               <div class="alert alert-success">
                  {{ session('success') }}
               </div>
               @endif
               @if ($errors->any())
               <div class="alert alert-danger">
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <table class="table table-bordered" id="user-table">
                  <thead>
                     <tr>
                        <th width="5%">S.No</th>
                        <th width="30%">Name</th>
                        <th width="50%">Role</th>
                        <th width="15%">Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
   $(document).ready(function(){
        $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user-datatable') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'role_name', name: 'role_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
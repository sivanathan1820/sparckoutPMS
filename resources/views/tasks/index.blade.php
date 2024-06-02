@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Manage Tasks') }}</div>
            <div class="card-body">
               <a href="{{ route('tasks.create') }}" class="btn btn-success mb-3">Add Task</a>
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
               <table class="table table-bordered" id="task-table">
                  <thead>
                     <tr>
                        <th>S.No</th>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
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
        $('#task-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('task-datatable') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'project_name', name: 'project_name' },
                { data: 'task', name: 'task' },
                { data: 'assigned_to', name: 'assigned_to' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
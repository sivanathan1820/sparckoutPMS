@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Manage Projects') }}</div>
            <div class="card-body">
               <a href="{{ route('projects.create') }}" class="btn btn-success mb-3">Add Project</a>
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
               <table class="table table-bordered" id="project-table">
                  <thead>
                     <tr>
                        <th width="5%">S.No</th>
                        <th width="30%">Name</th>
                        <th width="50%">Descsription</th>
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
        $('#project-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('project-datatable') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
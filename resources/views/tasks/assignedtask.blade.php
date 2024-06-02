@extends('layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">{{ __('Assigned Tasks') }}</div>
            <div class="card-body">
               <table class="table table-bordered" id="task-table">
                  <thead>
                     <tr>
                        <th>S.No</th>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Status</th>
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
<div class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Status</h5>
      </div>
      <div class="modal-body">
        <div class="row">
           <div class="col-12 col-sm-12 col-md-12">
              <div class="form-group">
                  <label for="status">Update Status</label>
                  <br>
                  <select class="form-control" name="status" id="status" required>
                     @foreach($task_status as $value)
                        <option value="{{$value['key']}}">{{$value['value']}}</option>
                     @endforeach
                  </select>
               </div>
           </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="update_status">Update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            ajax: "{{ route('assigned-task-list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'project_name', name: 'project_name' },
                { data: 'task', name: 'task' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });

   function update_status(id) {
      $("#update_status").attr("onclick","save_status("+id+")");
      $(".modal").modal('show')
   }

   function save_status(id)
   {
      $.ajax({
         url: '{{ route("update-status") }}',
         type: 'POST',
         data: {'status' : $("#status").val(),'_token':'{{csrf_token()}}','id':id},
         success: function(response) {
            location.reload();
         },
         error: function(xhr, status, error) {
            
         }
      });
   }
</script>
@endpush
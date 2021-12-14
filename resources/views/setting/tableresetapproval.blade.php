<div class="table-responsive tag-container mt-3">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
      <th width="12%">PO No.</th>
      <th width="15%">Supplier Code</th>
      <th width="30%">Supplier Name</th>
      <th width="15%">Due Date</th> 
      <th width="15%">Price</th> 
	    <th width="12%">User</th>
      <th width="8%">Reset</th>
  </tr>
   </thead>
    <tbody>         
        @forelse ($data as $show)
          <tr>
            <td>{{ $show->xpo_app_nbr }}</td>
            <td>{{ $show->xpo_vend }}</td>
            <td>{{ $show->xalert_nama }}</td>
            <td>{{ $show->xpo_due_date }}</td>
            <td>{{ number_format($show->xpo_total,2,'.',',') }}</td>
            <td>{{ $show->xpo_app_user }}</td>
            <td>
              <a href="" class="edituser" data-toggle="modal" data-target="#editModal" data-ponbr="{{$show->xpo_app_nbr}}"><i class="fas fa-edit"></i></a>
            </td>
          </tr>
        @empty
          <td colspan="7" style="color:red"><center>No Data Available</center></td>
        @endforelse               
        <tr>
          <td></td>
        </tr>       
    </tbody>
  </table>
      {!! $data->render() !!}
</div>
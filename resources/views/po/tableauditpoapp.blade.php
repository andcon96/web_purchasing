<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>ID</th>
         <th>PO No.</th>
         <th>Approver</th>
         <th>Alt Approver</th>
         <th>Approved By</th>
         <th>Order</th>
         <th>Reason</th>
         <th>Date</th>
         <th>Status</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($data as $show)
        <tr>
            <td>{{ $show->appid }}</td>
            <td>{{ $show->xpo_app_nbr }}</td>
            <td>{{ $show->name }}</td>
            <td>{{ $show->nama }}</td>
            <td>{{ $show->xpo_app_user }}</td>
            <td>{{ $show->xpo_app_order }}</td>
            <td>{{ $show->xpo_app_reason }}</td>
            <td>{{ $show->xpo_app_date }}</td>
            <td>
                @if($show->xpo_app_status == '0')
                Waiting Response
                @elseif($show->xpo_app_status == '1')
                Approved
                @elseif($show->xpo_app_status == '2')
                Rejected
                @endif
            </td>
        </tr>
        @empty
            <td colspan='9' class='text-danger'><b><center>No Data Available</center></b></td>
        @endforelse   
        <tr>
          <td></td>
        </tr>
    </tbody>
  </table>
</div>
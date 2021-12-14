<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>ID</th>
         <th>PO No.</th>
         <th>Create Date</th>
         <th>Line</th>
         <th>Part</th>
         <th>Desc</th>
         <th>Qty Order</th>
         <th>Qty Received</th>
         <th>Price</th>
         <th>Due Date</th>
         <th>Status</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($data as $show)
        <tr>
            <td>{{ $show->id }}</td>
            <td>{{ $show->xpo_nbr }}</td>
            <td>{{ date('Y-m-d', strtotime($show->created_at)) }}</td>
            <td>{{ $show->xpo_line }}</td>
            <td>{{ $show->xpo_part }}</td>
            <td>{{ $show->xpo_desc }}</td>
            <td>{{ number_format($show->xpo_qty_ord,2) }}</td>
            <td>{{ number_format($show->xpo_qty_rcvd,2) }}</td>
            <td>{{ number_format($show->xpo_price,2) }}</td>
            <td>{{ $show->xpo_due_date }}</td>
            <td>{{ $show->xpo_status }}</td>
        </tr>
        @empty
            <td colspan='12' class='text-danger'><b><center>No Data Available</center></b></td>
        @endforelse   
        <tr>
          <td></td>
        </tr>
    </tbody>
  </table>
</div>
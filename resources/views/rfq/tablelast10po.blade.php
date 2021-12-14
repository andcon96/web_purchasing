<div class="table-responsive tag-containerpo" style="overflow-x: auto; display: block;white-space: nowrap; margin-top:30px;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>Supplier</th>
         <th>PO No.</th>
         <th>Item No.</th>
         <th>Line</th>
         <th>Qty</th> 
         <th>Price</th> 
         <th>Date</th>  
      </tr>
   </thead>
    <tbody>     
        @forelse ($datapo as $show)
        <tr>
            <td>{{ $show->xalert_supp }}</td> 
            <td>{{ $show->xpo_nbr }}</td> 
            <td>{{ $show->xpo_part }}</td> 
            <td>{{ $show->xpo_line }}</td> 
            <td>{{ $show->xpo_qty_ord}}</td> 
            <td>{{ number_format($show->xpo_price,2) }}</td>  
            <td>{{ Carbon\Carbon::parse($show->created_at)->format('d-m-Y') }}</td> 
        </tr>
        @empty
        <tr>
            <td colspan="7" style="color:red"><center> <b>No Data</b> </center> </td>
        </tr>
        @endforelse         
        <tr>
          <td></td>
        </tr>
    </tbody>
  </table>
</div>
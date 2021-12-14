<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>Supplier</th>
         <th>RFQ No.</th>
         <th>Item No.</th>  
         <th>Qty</th>  
         <th>Price</th>
         <th>Date</th>  
      </tr>
   </thead>
    <tbody>     
        @forelse ($users as $show)
        <tr>
            <td>{{ $show->xalert_supp }}</td>
            <td>{{ $show->xbid_nbr }}</td> 
            <td>{{ $show->xbid_part }}</td> 
            <td>{{ $show->xbid_pur_qty}}</td>  
            <td>{{ number_format($show->xbid_pro_price,2) }}</td>
            <td>{{ $show->xbid_pur_date }}</td> 
        </tr>
        @empty
        <tr>
            <td colspan="6" style="color:red"><center> <b>No Data</b> </center> </td>
        </tr>
        @endforelse      
        <tr>
          <td></td>
        </tr>   
    </tbody>
  </table>
</div>
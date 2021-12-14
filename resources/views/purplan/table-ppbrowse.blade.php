<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         
         <th>RFP/RFQ No.</th>
         <th>Supplier Code</th>
         <th>Supplier Name</th>  
         <th>Due Date</th>
         <th>Propose Date</th>
         <th>Status</th>
         <th>Line</th>
         <th>Item Code</th>
         <th>Item Description</th>
         <th>Qty Req.</th>
         <th>Qty Pro.</th>
      </tr>
   </thead>
    <tbody>
        
        @forelse($data as $show )
        <tr>
        <td>{{ $show->rf_number }}</td>
        <td>{{ $show->supp_code }}</td>
        <td>{{ $show->xalert_nama }}</td>
        <td>{{ date('d-m-Y', strtotime($show->due_date)) }}</td>
        @if($show->propose_date == null)
        <td class="text-center" >-</td>
        @else
        <td>{{ $show->propose_date }}</td>
        @endif
        <td>{{ $show->status }}</td>
        <td>{{ $show->line }}</td>
        <td>{{ $show->item_code }}</td>
        <td>{{ $show->xitemreq_desc }}</td>
        
        @if($show->qty_req == null)
        <td class="text-center">-</td>
        @else
        <td>{{ $show->qty_req}}</td>
        @endif
        
        @if( $show->qty_pro == null )
        <td class="text-center">-</td>
        @else
        <td>{{ $show->qty_pro }}</td>
        @endif
        
        @empty
	    <td colspan='12' class='text-danger'><b><center>No Data Available</center></b></td>
        </tr>
        @endforelse
           
    </tbody>
  </table>
  {!! $data->render() !!}
</div>
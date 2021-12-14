<div class="table-responsive tag-container" style="overflow: auto; display: block;white-space: nowrap;max-height: 300px;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>No.</th>
         <th>RFP/RFQ No.</th>
         <th>Supplier Code</th>
         <th>Supplier Name</th>
         <th>Line</th>
         <th>Item No.</th>
         <th>Item Description</th>
         <th>Qty Req</th>  
         <th>Qty Pro</th>
         <th>Qty Purch</th>
         <th>Price</th>
         <th>Due Date</th>
         <th>Pro Date</th>
         <th>Pur Date</th>
         <th>Action</th>
      </tr>
   </thead>
    <tbody>   
        @forelse ($data as $show)
        <tr>
        	<td>{{ $loop->iteration }}</td>
            <td>{{ $show->rf_number }}</td> 
            <td>{{ $show->supp_code }}</td> 
            <td>{{ $show->xalert_nama }}</td> 
            <td>{{ $show->line }}</td> 
            <td>{{ $show->item_code }}</td>
            <td>{{ $show->xitemreq_desc }}</td>
            <td>{{ $show->qty_req }}</td>
            <td>{{ $show->qty_pro }}</td>
            <td>{{ $show->qty_pur }}</td>
            <td>{{ $show->price }}</td>
            <td>{{ $show->due_date }}</td>
            <td>{{ $show->propose_date }}</td>
            <td>{{ $show->purchase_date }}</td>
            <td>
	                <a href="" class="editdata"  data-iteration="{{$loop->iteration}}"
	                data-id="{{$show->id}}" data-rfnumber="{{$show->rf_number}}"
	                data-suppcode="{{$show->supp_code}}" data-line="{{$show->line}}"
	                data-itemcode="{{$show->item_code}}" data-qtyreq="{{$show->qty_req}}"
	                data-qtypro ="{{$show->qty_pro}}" data-qtypur="{{$show->qty_pur}}"
	                data-price="{{$show->price}}" data-duedate="{{$show->due_date}}"
	                data-prodate="{{$show->propose_date}}" data-itemdesc="{{$show->xitemreq_desc}}"
	                data-suppdesc="{{$show->xalert_nama}}" data-purdate="{{$show->purchase_date}}"><i class="fas fa-edit"></i></a>

	                <a href="" id="deletetmp"  data-iteration="{{$loop->iteration}}"
	                data-id="{{$show->id}}" data-rfnumber="{{$show->rf_number}}"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        @empty
          <tr>
            <td class='text-danger' colspan='15'>
              <center><b>No Data Available</b></center>
            </td>
          </tr>
        @endforelse     
    </tbody>
  </table>
</div>
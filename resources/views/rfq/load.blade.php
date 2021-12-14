<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>RFQ No.</th>
         <th>Supplier Code</th>
         <th>Supplier Name</th>
         <th>Propose Qty</th>  
         <th>Propose Date</th>
         <th>Price</th>  
         <th>Action</th> 
         <th>History</th>
         <th>Reject</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($users as $show)
        <tr>
            <td>{{ $show->xbid_id }}</td> 
            <td>{{ $show->xalert_supp }}</td> 
            <td>{{ $show->xalert_nama }}</td> 
            <td>{{ number_format($show->xbid_pro_qty,2) }}</td> 
            <td>{{ $show->xbid_pro_date }}</td> 
            <td>{{ number_format($show->xbid_pro_price,2 )}}</td> 
            <td> 
                <a href="" class='editUser' data-toggle='modal' data-target='#detailModal' 
                data-detid="{{$show->xbid_det_id}}" data-rfqnbr="{{$show->xbid_id}}" 
                data-itemcode="{{$show->xbid_part}}" data-qtyreq="{{number_format($show->xbid_qty_req,2)}}"
                data-duedate="{{$show->xbid_due_date}}" data-pricemin="{{number_format($show->xbid_price_min,2)}}"
                data-pricemax="{{number_format($show->xbid_price_max,2)}}" data-supplier="{{$show->xalert_nama}}" 
                data-proqty="{{number_format($show->xbid_pro_qty,2)}}" data-prodate="{{$show->xbid_pro_date}}" 
                data-price="{{number_format($show->xbid_pro_price,2)}}" data-rfqsite="{{$show->xbid_site}}"
                data-supplierid="{{$show->xbid_supp}}" data-startdate="{{$show->xbid_start_date}}"
                data-notesupp="{{$show->xbid_pro_remarks}}" data-notepurch="{{$show->xbid_remarks}}"><i class="fas fa-edit"></i></a>
                @if(!empty($show->xbid_pro_attch))
                <a href="{{ route('downloadrfqsupp',[$show->xbid_id,$show->xbid_supp]) }}" data-nbr="{{$show->xbid_id}}"><i class="fas fa-download"></i> </a>
                @endif
            </td>
            <td>
                <a href="" class='last10search' data-toggle='modal' data-target='#last10Modal'
                data-itemcode="{{$show->xbid_part}}" data-itemdesc="{{$show->xitemreq_desc}}"><i class="fas fa-history"></i></a>
            </td>
            <td>
              <a href="" class='deleteUser' data-toggle='modal' data-target='#deleteModal' 
                data-detid="{{$show->xbid_det_id}}" data-rfqnbr="{{$show->xbid_id}}" 
                data-itemcode="{{$show->xbid_part}}" data-qtyreq="{{number_format($show->xbid_qty_req,2)}}"
                data-duedate="{{$show->xbid_due_date}}" data-pricemin="{{number_format($show->xbid_price_min,2)}}"
                data-pricemax="{{number_format($show->xbid_price_max,2)}}" data-supplier="{{$show->xalert_nama}}" 
                data-proqty="{{number_format($show->xbid_pro_qty,2)}}" data-prodate="{{$show->xbid_pro_date}}" 
                data-price="{{number_format($show->xbid_pro_price,2)}}" data-rfqsite="{{$show->xbid_site}}"
                data-supplierid="{{$show->xbid_supp}}" data-startdate="{{$show->xbid_start_date}}"
                data-notesupp="{{$show->xbid_pro_remarks}}" data-notepurch="{{$show->xbid_remarks}}"><i class="fas fa-thumbs-down "></i></a>
            </td>
        </tr>
        @empty
        <tr>
          <td colspan="12" style="color:red"><center><b>No Data Available</b></center></td>
        </tr>
        @endforelse   
        <tr>
          <td></td>
        </tr>
    </tbody>
  </table>
  {!! $users->render() !!}
</div>
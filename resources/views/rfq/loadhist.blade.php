<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th style="width:15%;">RFQ No.</th>
         <th style="width:8%;">Site</th>
         <th style="width:8%;">Supp Code</th>
         <th style="width:8%;">Supplier Name</th>  
         <th style="width:8%;">Item No.</th>
         <th style="width:25%;">Item Description</th>
         <th style="width:8%;">Qty Requested</th>
         <th style="width:8%;">Qty Proposed</th>
         <th style="width:12%;">Due Date</th>
         <th style="width:12%;">Status</th>
         <th style="width:8%;">Action</th>
      </tr>
   </thead>
    <tbody>         
        @foreach ($alert as $show)
        <tr>
            <td>{{ $show->xbid_nbr }}</td> 
            <td>{{ $show->xbid_site }}</td> 
            <td>{{ $show->xbid_supp }}</td> 
            <td>{{ $show->xalert_nama }}</td> 
            <td>{{ $show->xbid_part }}</td> 
            <td>{{ $show->xitemreq_desc }}</td> 
            <td>{{ $show->xbid_qty_req }}</td> 
            <td>{{ $show->xbid_pro_qty }}</td>
            <td>{{ $show->xbid_due_date }}</td> 
            @if($show->xbid_flag == '1')
            <td>Submitted</td>
            @elseif($show->xbid_flag == '2')
            <td>Approved</td>
            @elseif($show->xbid_flag == '0')
            <td>Open</td>
            @elseif($show->xbid_flag == '3')
            <td>Closed</td>
            @elseif($show->xbid_flag == '4')
            <td>Closed</td>
            @endif 
            <td>
                <a href="" class='editUser' data-toggle='modal' data-target='#detailModal' 
                data-detid="{{$show->xbid_id}}" data-rfqnbr="{{$show->xbid_nbr}}" 
                data-site="{{$show->xbid_site}}" data-itemcode="{{$show->xbid_part}}" 
                data-qtyreq="{{$show->xbid_qty_req}}" data-startdate="{{$show->xbid_start_date}}"
                data-duedate="{{$show->xbid_due_date}}" data-pricemin="{{ number_format($show->xbid_price_min,2) }}"
                data-pricemax="{{ number_format($show->xbid_price_max,2) }}" data-supplier="{{$show->xalert_nama}}" 
                data-proqty="{{$show->xbid_pro_qty}}" data-prodate="{{$show->xbid_pro_date}}" 
                data-proprice="{{ number_format($show->xbid_pro_price,2) }}"
                data-supplierid="{{$show->xbid_supp}}" data-purqty="{{$show->xbid_pur_qty}}"
                data-purdate="{{$show->xbid_pur_date}}" data-itemdesc="{{$show->xitemreq_desc}}"><i class="fas fa-eye"></i></a>
            </td>
        </tr>
        @endforeach
        <tr>
          <td></td>
        </tr>     
    </tbody>
  </table>
  {{$alert->links()}}
</div>
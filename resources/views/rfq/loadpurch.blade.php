<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="margin-left:10px">
    <thead>
      <tr>
         <th style="width:15%;">RFQ No.</th>
         <th style="width:10%;">Start Date</th>
         <th style="width:10%;">Due Date</th>  
         <th style="width:10%;">Item No.</th> 
         <th style="width:30%;">Item Description</th>
         <!--
         <th style="width:10%;">Qty Req</th>  
         <th style="width:12%;">Price Min</th>
         <th style="width:12%;">Price Max</th>  
         -->
         <th style="width:6%;">Edit</th>
         <th style="width:6%;">Close</th>
         <th style="width:6%;">Email</th>
         <th style="width:6%;">Offer</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($bid as $show)
        <tr>
            <td>{{ $show->xbid_id }}</td> 
            <td>{{ $show->xbid_start_date }}</td> 
            <td>{{ $show->xbid_due_date }}</td> 
            <td>{{ $show->xbid_part }}</td> 
            <td>{{ $show->xitemreq_desc }}</td> 
            <td> 
                <a href="" class='editdata' data-toggle='modal' data-target='#editModal' 
                data-rfqnbr="{{$show->xbid_id}}" data-startdate="{{$show->xbid_start_date}}"
                data-itemcode="{{$show->xbid_part}}" data-qtyreq="{{$show->xbid_qty_req}}"
                data-duedate="{{$show->xbid_due_date}}" data-remarks="{{$show->xbid_remarks}}"
                data-pricemin="{{$show->xbid_price_min}}" data-pricemax="{{$show->xbid_price_max}}"
                data-rfqsite="{{$show->xbid_site}}" data-itemdesc="{{$show->xitemreq_desc}}"><i class="fas fa-edit"></i></a>
            </td>
            <td> 
                <a href="" class='deletedata' data-toggle='modal' data-target='#deleteModal' 
                data-rfqnbr="{{$show->xbid_id}}" data-startdate="{{$show->xbid_start_date}}"
                data-itemcode="{{$show->xbid_part}}" data-qtyreq="{{ number_format($show->xbid_qty_req,2)}}"
                data-duedate="{{$show->xbid_due_date}}" data-remarks="{{$show->xbid_remarks}}"
                data-pricemin="{{ number_format($show->xbid_price_min,2) }}" data-pricemax="{{ number_format($show->xbid_price_max,2) }}"
                data-rfqsite="{{$show->xbid_site}}" data-itemdesc="{{$show->xitemreq_desc}}"><i class="fas fa-edit"></i></a>
            </td>
            <td> 
                <a href="" class='adddata' data-toggle='modal' data-target='#addModal' 
                data-rfqnbr="{{$show->xbid_id}}" data-itemdesc="{{$show->xitemreq_desc}}"
                data-itemcode="{{$show->xbid_part}}"><i class="fas fa-edit"></i></a>
            </td>
            <td> 
                <a href="" class='viewdetail' data-toggle='modal' data-target='#viewdetail' 
                data-rfqnbr="{{$show->xbid_id}}" data-itemdesc="{{$show->xitemreq_desc}}"
                data-itemcode="{{$show->xbid_part}}"><i class="fas fa-edit"></i></a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan='12' style="color:red;"><center><b>No Data Available</b></center></td>
        </tr>
        @endforelse  
        <tr>
            <td></td>
        </tr>   
    </tbody>
  </table>
    {!! $bid->render() !!}
</div>
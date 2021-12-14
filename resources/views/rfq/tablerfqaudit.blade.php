<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>ID</th>
         <th>RFQ No.</th>
         <th>Site</th>
         <th>Start Date</th>
         <th>Due Date</th>
         <th>Item No.</th>
         <th>Qty Req</th>
         <th>Price Min</th>
         <th>Price Max</th>
         <th>Price Pro</th>
         <th>Supp</th>
         <th>Qty Pro</th>
         <th>Qty Purch</th>
         <th>Date Pro</th>        
         <th>Date Purch</th>
         <th>Status</th>
         <th>Remarks History</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($data as $show)
        <tr>
            <td>{{ $show->xbid_id }}</td>
            <td>{{ $show->xbid_nbr }}</td>
            <td>{{ $show->xbid_site }}</td>
            <td>{{ $show->xbid_start_date }}</td>
            <td>{{ $show->xbid_due_date }}</td>
            <td>{{ $show->xbid_part }}</td>
            <td>{{ number_format($show->xbid_qty_req,2) }}</td>
            <td>{{ number_format($show->xbid_price_min,2) }}</td>
            <td>{{ number_format($show->xbid_price_max,2) }}</td>
            <td>{{ number_format($show->xbid_pro_price,2) }}</td>
            <td>{{ $show->xbid_supp }}</td>
            <td>{{ number_format($show->xbid_pro_qty,2) }}</td>
            <td>{{ number_format($show->xbid_pur_qty,2) }}</td>
            <td>{{ $show->xbid_pro_date }}</td>
            <td>{{ $show->xbid_pur_date }}</td>
            <td>
                @if($show->xbid_flag == '0')
                    Open
                @elseif($show->xbid_flag == '1')
                    Submitted
                @elseif($show->xbid_flag == '2')
                    Approved
                @elseif($show->xbid_flag == '3')
                    Rejected
                @elseif($show->xbid_flag == '4')
                    Closed
                @endif
            </td>
            <td>{{ $show->xbid_hist_remarks }}</td>
        </tr>
        @empty
            <td colspan='16' class='text-danger'><b><center>No Data Available</center></b></td>
        @endforelse   
        <tr>
            <td></td>
        </tr>
    </tbody>
  </table>
  {!! $data->render() !!}
</div>
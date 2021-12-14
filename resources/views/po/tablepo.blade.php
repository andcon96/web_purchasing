<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th style="width:10%">PO Number</th>
         <th style="width:5%">Line</th>
         <th style="width:8%">Supplier</th>
         <th style="width:8%">Supplier Desc</th>
         <th style="width:15%">Item Number</th>  
         <th style="width:30%">Item Description</th>
         <th style="width:8%">Qty Order</th>
         <th style="width:8%">Qty Receipt</th>
         <th style="width:8%">Due Date</th>
         <th style="width:10%">Status</th>  
         <th style="width:10%">Last Confirmed</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($users as $show)
        <tr>
            <td>{{ $show->xpo_nbr }}</td>
            <td>{{ $show->xpod_line }}</td>
            <td>{{ $show->xpo_vend }}</td>
            <td>{{ $show->xalert_nama }}</td>
            <td>{{ $show->xpod_part }}</td>
            <td>{{ $show->xpod_desc }}</td>
            <td>{{ number_format($show->xpod_qty_ord,2 ) }}</td>
            <td>{{ number_format($show->xpod_qty_rcvd,2) }}</td>
            <td>{{ $show->xpo_due_date }}</td>
            <td>
                @if($show->xpo_status == 'UnConfirm')
                Unapproved
                @else
                {{ $show->xpo_status }}
                @endif
            </td>
            <td>{{ $show->xpod_last_conf }}</td>
        </tr>
        @empty
            <td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
        @endforelse   
        <tr>
            <td></td>
        </tr>
    </tbody>
  </table>
  <div class="row ml-1">
    <div>   
        {!! $users->render() !!}
    </div>
    <div class="ml-auto mr-3">
         Last Synchronized : <?php if($updatedat != '0'){ echo $updatedat->com_last_sync; }else{ echo 'No Data Avail'; } ?>
    </div>  
  </div>

</div>
<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Purchase Order</th>
        <th>Supllier</th>
        <th>Line</th>
        <th>Item Number</th>
        <th>Item Desc</th>
        <th>Qty Order</th>
        <th>Qty Open</th>
        <th>UM</th>
        <th>Due Date</th>
        <th>Past Due In Days</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
    @forelse($pastduepo as $pdue)
      <tr>
        <td data-title = 'Purchase Order'>{{ $pdue->xpo_nbr }}</td>
        <td data-title = 'Supplier'>{{ $pdue->xpo_vend }}</td>
        <td data-title = 'Line'>{{ $pdue->xpod_line }}</td>
        <td data-title = 'Item Number'>{{ $pdue->xpod_part }}</td>
        <td data-title = 'Item Desc'>{{ $pdue->xpod_desc }}</td>
        <td data-title = 'Qty Order'>{{ $pdue->xpod_qty_ord }}</td>
        <td data-title = 'Qty Open'>{{ $pdue->xpod_qty_open }}</td>
        <td data-title = 'UM'>{{ $pdue->xpod_um }}</td>
        <td data-title = 'Due Date'>{{ $pdue->xpo_due_date }}</td>
       <!--  <td data-title = 'Past Due in Days'>{{ \Carbon\Carbon::parse($pdue->xpo_due_date)->diffInDays() }} Days</td> -->
        <td data-title = 'Past Due in Days'> {{ \Carbon\Carbon::parse($pdue->xpo_due_date)->floatDiffInDays(now()->format('y-m-d'),false) * -1 }} Days</td>
        <!-- <td data-title = 'Past Due in Days'> {{ \Carbon\Carbon::parse($pdue->xpod_due_date)->diffInDays(now(), false) }} Days</td> -->

         <!-- @if( \Carbon\Carbon::parse($pdue->xpod_due_date)->diffInDays(now(), false) == 0 )
          <td data-title = 'Status'>n/a</td>
        @endif -->
        
        <td data-title = 'Status  '>{{ $pdue->xpod_status }}</td>
      </tr>
    @empty
          <td colspan='13' class='text-danger'><b> <center>No Data Available</center> </b></td>
    @endforelse   
    </tbody>
  </table>
  {!! $pastduepo->render() !!}
</div>



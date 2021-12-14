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
          <th>Upcoming Due</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
    @forelse($upcoming as $p)
      <tr>
        <td data-title = 'Purchase Order'>{{ $p->xpo_nbr }}</td>
        <td data-title = 'Supplier'>{{ $p->xpo_vend }}</td>
        <td data-title = 'Line'>{{ $p->xpod_line }}</td>
        <td data-title = 'Item Number'>{{ $p->xpod_part }}</td>
        <td data-title = 'Item Desc'>{{ $p->xpod_desc }}</td>
        <td data-title = 'Qty Order'>{{ $p->xpod_qty_ord }}</td>
        <td data-title = 'Qty Open'>{{ $p->xpod_qty_open }}</td>
        <td data-title = 'UM'>{{ $p->xpod_um }}</td>
        <td data-title = 'Due Date'>{{ $p->xpo_due_date }}</td>
       <!--  <td data-title = 'Past Due in Days'>{{ \Carbon\Carbon::parse($p->xpo_due_date)->diffInDays() }} Days</td> -->
        <td data-title = 'Past Due in Days'> {{ \Carbon\Carbon::parse($p->xpo_due_date)->floatDiffInDays(now()->format('y-m-d'),false) * -1 }} Days</td>
        <!-- <td data-title = 'Past Due in Days'> {{ \Carbon\Carbon::parse($p->xpod_due_date)->diffInDays(now(), false) }} Days</td> -->

         <!-- @if( \Carbon\Carbon::parse($p->xpod_due_date)->diffInDays(now(), false) == 0 )
          <td data-title = 'Status'>n/a</td>
        @endif -->
        
        <td>
                @if($p->xpo_status == 'UnConfirm')
                Unapproved
                @else
                {{ $p->xpo_status }}
                @endif
            </td>
      </tr>
    @empty
          <td colspan='8' class='text-danger'><b>No Data Available</b></td>
    @endforelse   
    </tbody>
  </table>
  {!! $upcoming->render() !!}
</div>



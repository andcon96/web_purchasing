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
        <th>Create Date</th>
        <th>Due Date</th>
        <th>Idle Days</th>
        <th>Days to Due</th>
        <th>Status</th>
       <!--  <th width="7%">Action</th> -->
      </tr>
    </thead>
    <tbody>
    @forelse($poappbrw as $p)
      <tr>
        <td>{{ $p->xpo_nbr }}</td>
        <td>{{ $p->xpo_vend }}</td>
        <td>{{ $p->xpod_line }}</td>
        <td>{{ $p->xpod_part }}</td>
        <td>{{ $p->xpod_desc }}</td>
        <td>{{ $p->xpod_qty_ord }}</td>
        <td>{{ $p->xpo_crt_date }}</td>
        <td>{{ $p->xpod_due_date }}</td>
        <td>{{\Carbon\Carbon::parse($p->xpo_crt_date)->diffInDays() }}</td>
        <td>{{\Carbon\Carbon::parse($p->xpod_due_date)->diffInDays() }}</td>
        
        @if( $p->xpo_app_flg == 1 )
          <td data-title = 'Status'>Approved</td>
        @elseif( $p->xpo_app_flg == 2 )
          <td data-title = 'Status'>Unapproved</td>
        @endif
         <!--  <td data-title="Delete" class="action">        
            <a href="" class="deletesupp" data-id="{{$p->xpo_nbr}}" data-role="{{$p->xpo_nbr}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
          </td> -->
      </tr>
        @empty
          <td colspan='11' class='text-danger'><b>No Data Available</b></td>
        @endforelse   
      </tbody>

  </table>      
    {!! $poappbrw->render() !!}
</div>




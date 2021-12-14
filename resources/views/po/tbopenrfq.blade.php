              <div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>RFQ No.</th>
                      <th>Supllier</th>
                      <th>Item Number</th>
                      <th>Item Desc</th>
                      <th>QTY Req</th>
                      <th>UM</th>
                      <th>Due Date</th>
                      <th>Create Date</th>
                      <th>Idle Days</th>
                      <th>Upcoming Due</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                  @forelse($openrfq as $p)
                    <tr>
                      <td data-title = 'Purchase Order'>{{ $p->xbid_id }}</td>
                      <td data-title = 'Supplier'>{{ $p->xbid_supp }}</td>
                      <td data-title = 'Line'>{{ $p->xitemreq_part }}</td>
                      <td data-title = 'Item Desc'>{{ $p->xitemreq_desc }}</td>
                      <td data-title = 'QTY Req'>{{ $p->xbid_qty_req }}</td>
                      <td data-title = 'Qty Open'>{{ $p->xitemreq_um }}</td>
                      <td data-title = 'Due Date'>{{ $p->xbid_due_date }}</td>
                      <td data-title = 'Create Date'> {{ $p->xbid_start_date }} </td>
                      <td data-title = 'Idle Days'> {{ \Carbon\Carbon::parse($p->xbid_start_date)->diffInDays() }} Days</td>
                      <!--  <td data-title = 'Past to Due'> {{ \Carbon\Carbon::parse($p->xbid_due_date)->diffInDays() }} Days</td> -->
                      <td data-title = 'Past to Due'> {{ now()->diffInDays(Carbon\Carbon::parse($p->xbid_due_date), false) }} Days</td>
                      @if( $p->xbid_flag == 0  )
                      <td data-title = 'Status'>New Request</td>
                      @elseif( $p->xbid_flag == 1 )
                      <td data-title = 'Status'>Submitted</td>
                      @elseif( $p->xbid_flag == 3  )
                      <td data-title = 'Status'>Closed</td>
                      @endif
                    </tr>
                  @empty
                    <td colspan='11' class='text-danger'><b>No Data Available</b></td>
                  @endforelse    
                  </tbody>
                </table>      
                {!! $openrfq->render() !!}
              </div>
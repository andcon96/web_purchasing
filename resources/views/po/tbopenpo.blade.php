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
                  @foreach($openpo as $show)
                    <tr>
                      <td data-title = 'Purchase Order'>{{ $show->xpo_nbr }}</td>
                      <td data-title = 'Supplier'>{{ $show->xpo_vend }}</td>
                      <td data-title = 'Line'>{{ $show->xpod_line }}</td>
                      <td data-title = 'Item Number'>{{ $show->xpod_part }}</td>
                      <td data-title = 'Item Desc'>{{ $show->xpod_desc }}</td>
                      <td data-title = 'Qty Order'>{{ $show->xpod_qty_ord }}</td>
                      <td data-title = 'Qty Open'>{{ $show->xpod_qty_open }}</td>
                      <td data-title = 'UM'>{{ $show->xpod_um }}</td>
                      <td data-title = 'Due Date'>{{ $show->xpo_due_date }}</td>
                     <!--  <td data-title = 'Past Due in Days'> {{ now()->diffInDays(Carbon\Carbon::parse($show->xpo_due_date), false) }} Days</td> -->
                      <td data-title = 'Upcomimg Due'>{{ \Carbon\Carbon::parse($show->xpo_due_date)->floatDiffInDays(now()->format('y-m-d'),false) * -1 }} Days</td>
                      <td data-title = 'Status'>{{ $show->xpo_status }}</td>
                    </tr>
                  @endforeach   
                  </tbody>
                </table>      
                 {!! $openpo->render() !!}
              </div>

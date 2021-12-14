            <div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Purchase Order</th>
                      <th>Supllier</th>
                      <th>PO Order</th>
                      <th>Line</th>
                      <th>Item Number</th>
                      <th>Item Desc</th>
                      <th>Qty Order</th>
                      <th>UM</th>
                      <th>Create Date</th>
                      <th>Idle days</th>
                      <th>Due Date</th>
                      <th>Days Until Due</th>
                    </tr>
                  </thead>
                  <tbody>
                  @forelse($nbrofpo as $show)
                    <tr>
                      <td data-title = 'Purchase Order'>{{ $show->xpo_nbr }}</td>
                      <td data-title = 'Supplier'>{{ $show->xpo_vend }}</td>
                      <td data-title = 'PO Date'>{{ $show->xpo_ord_date }}</td>
                      <td data-title = 'Line'>{{ $show->xpod_line }}</td>
                      <td data-title = 'Item Number'>{{ $show->xpod_part }}</td>
                      <td data-title = 'Item Desc'>{{ $show->xpod_desc }}</td>
                      <td data-title = 'Qty Order'>{{ $show->xpod_qty_ord }}</td>
                      <td data-title = 'UM'>{{ $show->xpod_um }}</td>
                      <td data-title = 'Create Date'>{{ $show->xpo_crt_date }}</td>
                      <td data-title = 'Idle Days'>{{\Carbon\Carbon::parse($show->xpo_crt_date)->diffInDays() }}</td>
                      <td data-title = 'Due Date'>{{ $show->xpo_due_date }} </td>
                      <td data-title = 'Days Until Due'>{{\Carbon\Carbon::parse($show->xpo_due_date)->diffInDays() }}</td>
                    </tr>        
                    @empty
                    <td colspan='8' class='text-danger'><b>No Data Available</b></td>
                    @endforelse 
                  </tbody>
                </table>      
                {!! $nbrofpo->render() !!}
              </div>
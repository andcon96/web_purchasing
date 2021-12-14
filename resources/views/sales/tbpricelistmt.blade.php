          <div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                 <th>Customer Code</th>
                 <th>Item Code</th>  
                 <th>Customer Type</th>
                 <th>Start Effective Date</th>
                 <th>Minimum Order</th>
                 <th>List Price</th>
                 <th>Discount</th>
                 <th>End Effective Date</th>
                 <th width="10%">Edit</th>
                 <th width="10%">Delete</th>
                </tr>
              </thead>
              <tbody>         
                @foreach ($cust as $show)
                <tr>
                  <td>{{ $show->xcust_code }}</td>
                  <td>{{ $show->xitem_code }}</td>
                  <td>{{ $show->xcust_type }}</td>
                  <td>{{ $show->xcust_start_date }}</td>
                  <td>n/a</td>
                  <td>n/a</td>
                  <td>n/a</td>
                  <td>n/a</td>
                  <td>
                    <a href="" class="editModal" data-toggle="modal" data-target="#editModal" data-id="{{ $show->id }}" data-part="" data-supp=""><i class="fas fa-edit"></i></a>
                  </td>
                  <td>
                    <a href="" class="deleteModal" data-toggle="modal" data-target="#deleteModal" data-id="{{ $show->xcust_code }}" data-supp="" data-part=""><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                @endforeach                      
              </tbody>
            </table>
            {!! $cust->render() !!}
          </div>
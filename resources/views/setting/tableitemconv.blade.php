<div class="table-responsive tag-container"  style="overflow-x: auto; display: block;white-space: nowrap;">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
         <th>Item Code</th>
         <th>UM 1</th>
         <th>UM 2</th>
         <th>Qty Item</th>  
         
      </tr>
       </thead>
        <tbody>         
            @forelse ($data as $show)
              <tr>
                <td>{{ $show->item_code }}</td>
                <td>{{ $show->um_1 }}</td>
                <td>{{ $show->um_2 }}</td>
                <td>
                    {{ $show->qty_item }}
                </td>
              </tr>
            @empty
              <tr>
                <td class="text-danger" colspan='5'>
                    <center><b>No Data Available</b></center>
                </td>
              </tr>
            @endforelse                      
        </tbody>
      </table>
      {!! $data->links() !!}
</div>
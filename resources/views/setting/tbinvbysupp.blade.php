<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>Item Number</th>
         <th>Item Desc</th>
         <th>Supplier</th>
         <th>Safety Stock</th>
         <th width="7%">Delete</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($invbysupp as $show)
        <tr>
            <td >{{ $show->xitem_nbr }}</td>
            <td >{{ $show->xitem_desc }}</td>
            <td >{{ $show->xsupp }}</td>
            <td >{{ $show->xinv_sft_stock }}</td>
            <td data-title="Delete" class="action">        
                <a href="" class="deletesupp" data-id="{{$show->xitem_nbr}}" data-role="{{$show->xitem_nbr}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
            </td>
        </tr>
        @empty
            <td colspan='8' class='text-danger'><b>No Data Available</b></td>
        @endforelse   
    </tbody>
  </table>
{!! $invbysupp->render() !!}
</div>
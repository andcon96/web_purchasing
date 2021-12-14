<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>Item Number</th>
         <th>Item Desc</th>
         <th>Supplier</th>
         <th width="7%">Delete</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($suppinv as $show)
        <tr>
            <td >{{ $show->xitem_nbr }}</td>
            <td >{{ $show->xitem_desc }}</td>
            <td >{{ $show->xsupp }}</td>
            <td data-title="Delete" class="action">        
                <a href="" class="deletesupp" data-id="{{$show->xitem_nbr}}" data-role="{{$show->xitem_nbr}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
            </td>

        </tr>
        @empty
            <td colspan='4' class='text-danger'><center><b>No Data Available</b></center></td>
        @endforelse   
    </tbody>
  </table>
{!! $suppinv->render() !!}
</div>
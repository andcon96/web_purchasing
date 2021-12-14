<div class="table-responsive col-lg-12 col-md-12 tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th style="width:20% !important">PO No.</th>
         <th>Line</th>
         <th>Item No.</th> 
         <th>Item Description</th> 
         <th>Qty Order</th>
         <th>Qty Shipped</th>
         <th>Eff Date</th>
         <th>Ship Date</th>
         <th>Qty Receipt</th>
         <th>UM</th>
         <th>Site</th>
         <th>Loc</th>
         <th>Lot</th>
         <th>Ref</th>
         <th>Add</th>
         <th>Edit</th>
         <th>Delete</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($value as $show)
        <tr>
            <td style="width:15%">{{ $show->xpo_nbr }}</td>
            <td style="width:8%">{{ $show->xpo_line }}</td>
            <td style="width:15%">{{ $show->xpo_part }}</td>
            <td style="width:15%">{{ $show->xpo_desc }}</td>
            <td style="width:8%">{{ $show->xpo_qty_ord }}</td>
            <td style="width:8%">{{ $show->xpo_qty_ship }}</td>
            <td style="width:8%">{{ $show->xpo_eff_date }}</td>
            <td style="width:8%">{{ $show->xpo_ship_date }}</td>
            <td style="width:8%">{{ $show->xpo_qty_rcvd }}</td>
            <td style="width:8%">{{ $show->xpo_um }}</td>
            <td style="width:8%">{{ $show->xpo_site }}</td>
            <td style="width:8%">{{ $show->xpo_loc }}</td>
            <td style="width:8%">{{ $show->xpo_lot }}</td>
            <td style="width:8%">{{ $show->xpo_ref }}</td>
            <td> <!--Add Row-->
              @if($show->xpo_status == 'Created')
              <form action="{{ url('newreceiptrow') }}" method="post">
                {{csrf_field()}}
                <input type="hidden" name='domain' value='{{$show->xpo_domain}}'>
                <input type="hidden" name='nopo' value='{{$show->xpo_nbr}}'>
                <input type="hidden" name='line' value='{{$show->xpo_line}}'>
                <input type="hidden" name='part' value='{{$show->xpo_part}}'>
                <input type="hidden" name='desc' value='{{$show->xpo_desc}}'>
                <input type="hidden" name='um' value='{{$show->xpo_um}}'>
                <input type="hidden" name='site' value='{{$show->xpo_site}}'>
                <input type="hidden" name='qtyopen' value='{{$show->xpo_qty_open}}'>
                <input type="hidden" name='qtyord' value='{{$show->xpo_qty_ord}}'>
                <input type="hidden" name='qtyship' value='{{$show->xpo_qty_ship}}'>
                <input type="hidden" name='sj_id' value='{{$show->xpo_sj_id}}'>
                <button type="submit" class="btn btn-default" style="color:#4D20FD">
                    <i class="fa fa-plus"></i>
                </button>
              </form>
              @endif
            </td> 
            <td style="width:8%"> <!--Edit Row-->
              
              	<a href="" class='btn fa-input editUser' style="color:#4D20FD" data-toggle='modal' data-target='#detailModal' 
              	data-nbr='{{$show->xpo_nbr}}' data-sj='{{$show->xpo_sj_id}}' 
              	data-line='{{$show->xpo_line}}' data-part='{{$show->xpo_part}}'
              	data-desc='{{$show->xpo_desc}}' data-qtyord='{{$show->xpo_qty_ord}}'
              	data-qtyship='{{$show->xpo_qty_ship}}' data-qtyopen='{{$show->xpo_qty_open}}'
                data-id= '{{$show->xpo_rcp_id}}' data-effdate='{{$show->xpo_eff_date}}' 
                data-shipdate='{{$show->xpo_ship_date}}' data-qtyrcvd='{{$show->xpo_qty_rcvd}}'
                data-um='{{$show->xpo_um}}' data-site='{{$show->xpo_site}}'
                data-loc='{{$show->xpo_loc}}' data-lot='{{$show->xpo_lot}}'
                data-ref='{{$show->xpo_ref}}'>

              	<i class="fas fa-edit"></i></a> 
              
            </td>
            <td>
            @if($show->xpo_status == 'newrow') <!--Delete Row-->
              <a href="" class='btn deleteUser' style="color:#4D20FD" data-toggle='modal' data-target='#deleteModal' 
              data-nbr='{{$show->xpo_nbr}}' data-sj='{{$show->xpo_rcp_id}}' 
              data-line='{{$show->xpo_line}}' data-part='{{$show->xpo_part}}'
              data-desc='{{$show->xpo_desc}}' data-qtyord='{{$show->xpo_qty_ord}}'
              data-qtyship='{{$show->xpo_qty_ship}}' data-qtyopen='{{$show->xpo_qty_open}}'
              data-id= '{{$show->xpo_rcp_id}}'>

              <i class="fas fa-minus"></i></a>
            @endif
            </td> 

        </tr>
        @empty
            <td colspan='7' class='text-danger'><b>No Data Available</b></td>
        @endforelse   
    </tbody>
  </table>
</div>
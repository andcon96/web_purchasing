@forelse($sjmt as $show)
<tr>
    <td>{{ $show->xsj_id }}</td> 
    <td>{{ $show->xsj_supp }}</td>
    <td>{{ $show->xsj_po_nbr }}</td>
    <td>{{ $show->xsj_line }}</td>
    <td>{{ $show->xsj_part }}</td>
    <td>{{ $show->xsj_desc }}</td>
    <td>{{ $show->xsj_qty_ship }}</td>
    <td>{{ $show->xsj_loc }}</td>
    <td>{{ $show->xsj_lot }}</td>
    <td>{{ $show->xsj_ref }}</td>
    <td>{{ $show->xsj_status }}</td>
    <td>
    <form action="/sjmtedt" method="get">
        {{ csrf_field() }}  
        <input disable type="hidden" name="id" value= {{ $show->xsj_id }} >
        <input disable type="hidden" name="nbr" value= {{ $show->xsj_po_nbr }} >
        <input disable type="hidden" name="line" value= {{ $show->xsj_line }} >
        <input disable type="hidden" name="lot" value= {{ $show->xsj_lot }} >
        <button class='btn' type="submit" value="EDIT" ><i class="fas fa-edit"></i>
    </form>                          
    </td>
    <td>
    <a href="" class="deleteUser" 
    data-toggle="modal" 
    data-target="#deleteModal" 
    data-id="{{$show->xsj_id}}" 
    data-supp="{{$show->xsj_supp}}" 
    data-line="{{$show->xsj_line}}"
    data-lot="{{$show->xsj_lot}}"
    data-qship="{{$show->xsj_qty_ship}}"
    data-shp="{{$show->xsj_qty_ship}}"
    data-opn="{{$show->xsj_qty_open}}"
    data-nbr="{{$show->xsj_po_nbr}}"
    > 
    <i class="fas fa-trash"></i></a>  
    </td>
</tr>
@empty
<tr>
    <td colspan="12" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr>
  <td style="border: none !important;" colspan="5">
    {{ $sjmt->links() }}
  </td>
</tr>
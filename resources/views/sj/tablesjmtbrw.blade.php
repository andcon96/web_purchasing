@forelse($sjmt as $show)
<tr>
<td>{{ $show->xsj_id }}</td> 
<td>{{ $show->xsj_supp }}</td>
<td>{{ $show->xsj_po_nbr }}</td>
<td>{{ $show->xsj_line }}</td>
<td>{{ $show->xsj_part }}</td>
<td>{{ $show->xsj_desc }}</td>
<td>{{ $show->xsj_qty_ship }}</td>
<td>{{ $show->xsj_status }}</td>                   
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
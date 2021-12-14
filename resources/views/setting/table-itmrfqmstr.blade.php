@forelse($itm as $show)
<tr>
    <td>{{ $show->xitemreq_part }}</td> 
    <td>{{ $show->xitemreq_desc }}</td>
    <td>{{ $show->xitemreq_um }}</td>
    <td>{{ $show->xitemreq_sfty_stk }}</td>
    <td>{{ $show->xitemreq_type}}</td>
    <td>{{ $show->xitemreq_prod_line}}</td>
    <td>{{ $show->xitemreq_group}}</td>
    <td>{{ $show->xitemreq_dsgn}}</td>                                          
</tr>
@empty
<tr>
  <td class="text-danger" colspan='12'>
    <center><b>No Data Available</b></center>
  </td>
</tr>
@endforelse
<tr style="border:0 !important">
  <td colspan="12">
    {{ $itm->links() }}
  </td>
</tr>             
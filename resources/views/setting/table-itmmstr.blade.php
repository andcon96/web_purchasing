@forelse($itm as $show)
<tr>
    <td>{{ $show->xitem_part }}</td>
    <td>{{ $show->xitem_desc }}</td>
    <td>{{ $show->xitem_um }}</td>
    <td>{{ $show->xitem_sfty_stk }}</td>
    <td>{{ $show->xitem_type}}</td>
    <td>{{ $show->xitem_prod_line}}</td>
    <td>{{ $show->xitem_day1}} day</td>
    <td>{{ $show->xitem_day2}} day</td>
    <td>{{ $show->xitem_day3}} day</td>
    <td>{{ $show->xitem_sfty}} %</td>
    <!--   <td>
            <form action="/itmmstredt" method="get">
                  {{ csrf_field() }}  
                    <input disable type="hidden" name="part" value= {{ $show->xitem_part }} >
                  <button class='editdata' type="submit" value="EDIT" ><i class="fas fa-edit"></i>
                </form>  
            </td>-->
</tr>
@empty
<tr>
    <td colspan='12' class="text-danger">
        <center><b>No Data Available</b></center>
    </td>
</tr>
@endforelse
<tr style="border:0 !important">
  <td colspan="12">
    {{ $itm->links() }}
  </td>
</tr>             
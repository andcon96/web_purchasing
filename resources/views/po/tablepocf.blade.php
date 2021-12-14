@forelse ($users as $show)
<tr>
    <td style="width:10%">{{ $show->xpo_nbr }}</td>
    <td style="width:10%">{{ $show->xpo_vend }}</td>
    <td style="width:10%">{{ $show->xpo_ord_date }}</td>
    <td style="width:15%">{{ $show->xpo_due_date }}</td>
    <td style="width:10%">{{ $show->xpo_curr }}</td>
    <td style="width:15%">{{ $show->xpo_status }}</td>
    <td style="width:8%">
      <form action="/poddet" method="GET">
        <input disable type="hidden" name="cari" value= {{ $show->xpo_nbr }}>                       
        <input type="submit" name="submit" value="Detail">                                                
      </form>
    </td>
    <td style="width:8%">
      <form action="/popdf" method="GET">
      <input disable type="hidden" name="nbr" value= {{ $show->xpo_nbr }}>                       
      <input type="submit" value="PDF">                                                
      </form>                        
    </td>
</tr>
@empty
<tr>
  <td colspan='8' class='text-danger'><center><b>No Data Available</b></center></td>
</tr>
@endforelse   
<tr>
  <td style="border: none !important;" colspan="8">
    {{ $users->links() }}
  </td>
</tr>
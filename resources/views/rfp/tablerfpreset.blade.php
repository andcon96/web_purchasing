@forelse( $data as $show )
<tr>
	<td>{{ $show->xrfp_nbr }}</td>
	<td>{{ $show->created_by }}</td>
	<td>{{ $show->xrfp_supp }}</td>
	<td>{{ $show->xrfp_enduser }}</td>
	<td>{{ $show->xrfp_duedate }}</td>
	<td>
		@if($show->status == 'Rejected'  && ($show->created_by == Session::get('username') or Session::get('user_role') == 'Admin'))
			<a href="" class="confirmReset" data-toggle="modal" data-target="#confirmModal"
			data-rfpnbr="{{$show->xrfp_nbr}}" data-shipto="{{$show->xrfp_shipto}}" 
			data-site="{{$show->xrfp_site}}" data-enduser="{{$show->xrfp_enduser}}" 
			data-dept="{{$show->xrfp_dept}}" data-supp="{{$show->xrfp_supp}}"
			data-duedate="{{$show->xrfp_duedate}}" data-status="{{$show->status}}" ><i class="fas fa-edit"></i></a>
		@else
			
		@endif
	</td>
</tr>
@empty
<tr>
	<td colspan='6' class='text-danger'><b><center>No Data Available</center></b></td>
</tr>
@endforelse
<tr>
  <td style="border: none !important;" colspan="12">
    {{ $data->links() }}
  </td>
</tr>

@forelse( $bid as $show )
<tr>
	<td>{{ $show->xrfp_nbr }}</td>
	<td>{{ $show->created_by }}</td>
	<td>{{ $show->xrfp_supp }}</td>
	<td>{{ $show->xrfp_enduser }}</td>
	<td>{{ $show->xrfp_duedate }}</td>
	<td>

		@if( $show->status == 'New Request')
			New Request
		@elseif($show->status == 'Approved')
			Approved
		@elseif($show->status == 'Rejected')
			Rejected
		@elseif($show->status == 'Close')
			Closed
		@endif

	</td>
	<td>
		@if($show->status == 'New Request'  && ($show->created_by == Session::get('username') or Session::get('user_role') == 'Admin'))
			<a href="" class="editRFP" data-toggle="modal" data-target="#editModal"
			data-rfpnbr="{{$show->xrfp_nbr}}" data-shipto="{{$show->xrfp_shipto}}" 
			data-site="{{$show->xrfp_site}}" data-enduser="{{$show->xrfp_enduser}}" 
			data-dept="{{$show->xrfp_dept}}" data-supp="{{$show->xrfp_supp}}"
			data-duedate="{{$show->xrfp_duedate}}" data-status="{{$show->status}}" ><i class="fas fa-edit"></i></a>
		@else
			
		@endif
	</td>
	<td>
		@if($show->status == 'New Request'  && ($show->created_by == Session::get('username') or Session::get('user_role') == 'Admin'))
			<a href="" class="closeRFP" data-toggle="modal" data-target="#closeModal"
			data-rfpnbr="{{$show->xrfp_nbr}}" data-shipto="{{$show->xrfp_shipto}}" 
			data-site="{{$show->xrfp_site}}" data-enduser="{{$show->xrfp_enduser}}" 
			data-dept="{{$show->xrfp_dept}}"><i class="fas fa-edit"></i></a>
		@else
			
		@endif
	</td>
	<td>
		@if( $show->status == 'New Request' or $show->status == 'Approved' or $show->status == 'Rejected')
			<a href="" class="routeRFP" data-toggle="modal" data-target="#routeModal"
			data-rfpnbr="{{$show->xrfp_nbr}}"><i class="fas fa-eye"></i></a>
		@else
			
		@endif
	</td>	
</tr>
@empty
<tr>
    <td colspan="9" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr>
  <td style="border: none !important;" colspan="9">
    {{ $bid->links() }}
  </td>
</tr>
	   
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
	@elseif($show->status == 'Close')
		Closed
	@endif
		

	</td>
	<td>
		<a href="{{url('/detailrfpapp/'.$show->xrfp_nbr.'')}}">
			@if($show->xrfp_app_approver == Session::get('userid') or $show->xrfp_app_alt_approver == Session::get('userid')) 
				<i class="fas fa-thumbs-up"></i>
			@else
				<i class="fas fa-eye"></i>
			@endif
		</a>
	</td>
	<td>
		<a href="" class="routeRFP" data-toggle="modal" data-target="#routeModal"
		data-rfpnbr="{{$show->xrfp_nbr}}" data-shipto="{{$show->xrfp_shipto}}" 
		data-site="{{$show->xrfp_site}}" data-enduser="{{$show->xrfp_enduser}}" 
		data-dept="{{$show->xrfp_dept}}"><i class="fas fa-eye"></i></a>
	</td>
</tr>
@empty
<tr>
    <td colspan="8" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr>
  <td style="border: none !important;" colspan="8">
    {{ $bid->links() }}
  </td>
</tr>

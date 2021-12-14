@forelse( $histrfpdata as $show)
<tr>
	
	<td>{{ $show->rfp_hist_nbr }}</td>
	<td>{{ $show->rfp_create_by }}</td>
	<td>{{ $show->rfp_hist_supp }}</td>
	<!-- <td>{{ $show->rfp_duedate_mstr }}</td> -->
	<td>
		@if( $show->rfp_status == 'New Request')
			New Request
		@elseif($show->rfp_status == 'Approved')
			Approved
		@elseif($show->rfp_status == 'Close')
			Closed
	@elseif($show->rfp_status == 'Rejected')
			Rejected
	@endif
	</td>
	<td>{{ $show->itemcode_hist }}</td>
	<td>{{ $show->need_date_dets }}</td>
	<td>{{ $show->due_date_dets }}</td>
	<td>{{ $show->qty_order_hist }}</td>
	<td>{{ $show->nbr_convert }}</td>
	<td>{{ $show->rfp_create_at }}</td>
</tr>
@empty
<tr>
    <td colspan="12" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr>
  <td style="border: none !important;" colspan="12">
    {{ $histrfpdata->links() }}
  </td>
</tr>

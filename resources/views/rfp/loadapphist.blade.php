<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    	<thead>
	      	<tr>
		    	<!-- <th style="width:5%;">No.</th> -->
		     	<th style="width:10%;">RFP No.</th>
				<th style="width:10%;">Request By</th>
                <th style="width:10%;">Due date</th>
                <th style="width: 3%;">Order</th> 
		     	<th style="width:10%;">Approver</th>
                <th style="width: 10%;">Alt. Approver</th>
                <th style="width: 7%;">Approved By</th>
                <th style="width: 15%;">Reason</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Timestamp</th>

		  	</tr>
	   	</thead>
		<tbody>
            @forelse($test as $show)
			<tr>
                <td>{{$show->rfpnbr}}</td>
                <td>{{$show->requestby}}</td>
                <td>{{$show->rfpduedate}}</td>
                <td>{{$show->xrfp_app_order}}</td>
                <td>{{$show->name}}</td>
                <td>{{$show->nama }}</td>
                <td>{{$show->xrfp_app_user}}
                <td>{{$show->xrfp_app_reason}}</td>
                <td>
                    @if($show->xrfp_app_status == '3')
                        History
                    @elseif($show->xrfp_app_status == '2')
                        Rejected
                    @elseif($show->xrfp_app_status == '1')
                        Approved
                    @elseif($show->xrfp_app_status == '0')
                        On Status
                    @endif
                </td>
                <td>{{$show->create_at}}</td>
            </tr>
            @empty
            <tr>
                <td colspan='12' style="color:red">
                    <center><b>No Data Available</b></center>
                </td>
            </tr>
            @endforelse
	    </tbody>
    </table>
</div>
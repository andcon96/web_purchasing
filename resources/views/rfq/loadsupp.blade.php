<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
			    	<thead>
			      	<tr>
				    	<th style="width:15%;">RFQ Number</th>
                        @if(str_contains( Session::get('userid'), '4'))
                        <th>Supplier</th>
                        @endif
				     	<th style="width:12%;">Item Number</th>
				     	<th style="width:23%;">Item Description</th>
				     	<th style="width:8%;">Qty Requested</th>
				     	<th style="width:12%;">Due Date</th>
				     	<th style="width:12%;">Status</th>
				     	<th style="width:8%;">Action</th>
				  </tr>
			   	</thead>
				<tbody>       
					@forelse ($users as $show)
					  <tr>
					    	<td>{{ $show->xbid_id }}</td>
                            @if(str_contains( Session::get('userid'), '4'))
                                <td>{{ $show->xalert_nama }}</td>
                            @endif
					        <td>{{ $show->xbid_part }}</td>
					    	<td>{{ $show->xitemreq_desc }}</td>
					        <td>{{ $show->xbid_qty_req }}</td>
					    	<td>{{ $show->xbid_due_date }}</td>
					    	@if( $show->xbid_flag == '0')
					    		<td>New Request</td>
					    	@elseif( $show->xbid_flag == '1' )
					    		<td>Waiting Reply</td>
                            @elseif( $show->xbid_flag == '2' )
                                <td>Accepted</td>
                            @elseif( $show->xbid_flag == '3' )
                                <td>Closed</td>
                            @elseif( $show->xbid_flag == '4' )
                                <td>Closed</td>
					    	@endif
					    	<td>
					    		@if(Session::get('user_role') == 'Supplier')
					    		@if($show->xbid_flag <= '1')
					    		<a href="" class=" editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->xbid_det_id}}" data-part="{{$show->xbid_part}}"
					    			data-nbr="{{$show->xbid_id}}" data-qty="{{number_format($show->xbid_qty_req,2)}}"
					    			data-date="{{$show->xbid_due_date}}" data-pricemin="{{ number_format($show->xbid_price_min,2 ) }}"
					    			data-pricemax="{{number_format($show->xbid_price_max,2)}}" data-attch="{{$show->xbid_attch}}"
                                    data-supplier="{{$show->xbid_supp}}" data-site="{{$show->xbid_site}}"
                                    data-startdate="{{$show->xbid_start_date}}" data-partdesc="{{$show->xitemreq_desc}}" data-proremarks="{{$show->xbid_pro_remarks}}" data-prodate="{{$show->xbid_pro_date}}" data-proprice="{{$show->xbid_pro_price}}" data-proqty="{{$show->xbid_pro_qty}}"><i class="fas fa-edit"></i></a>
					    		@elseif($show->xbid_flag > '1')
					    		<a href="" class=" checkuser" data-toggle="modal" data-target="#checkModal" data-id="{{$show->xbid_det_id}}" data-proqty="{{number_format($show->xbid_pro_qty,2)}}" data-part="{{$show->xbid_part}}" data-prodate="{{$show->xbid_pro_date}}" data-proprice="{{number_format($show->xbid_pro_price,2)}}" data-proremarks="{{$show->xbid_pro_remarks}}"  
					    			data-nbr="{{$show->xbid_id}}" data-qty="{{$show->xbid_qty_req}}"
					    			data-date="{{$show->xbid_due_date}}" data-pricemin="{{number_format($show->xbid_price_min,2)}}"
					    			data-pricemax="{{number_format($show->xbid_price_max,2)}}" data-supplier="{{$show->xbid_supp}}" 
                                    data-site="{{$show->xbid_site}}" data-startdate="{{$show->xbid_start_date}}" data-partdesc="{{$show->xitemreq_desc}}">
                                    <i class="fas fa-eye"></i></a>
					    		@endif
					    		@else
					    		<a href="" class=" checkuser" data-toggle="modal" data-target="#checkModal" data-id="{{$show->xbid_det_id}}" data-proqty="{{number_format($show->xbid_pro_qty,2)}}" data-part="{{$show->xbid_part}}" data-prodate="{{$show->xbid_pro_date}}" data-proprice="{{number_format($show->xbid_pro_price,2)}}" data-proremarks="{{$show->xbid_pro_remarks}}"  
					    			data-nbr="{{$show->xbid_id}}" data-qty="{{$show->xbid_qty_req}}"
					    			data-date="{{$show->xbid_due_date}}" data-pricemin="{{number_format($show->xbid_price_min,2)}}"
					    			data-pricemax="{{number_format($show->xbid_price_max,2)}}" data-supplier="{{$show->xbid_supp}}" 
                                    data-site="{{$show->xbid_site}}" data-startdate="{{$show->xbid_start_date}}" data-partdesc="{{$show->xitemreq_desc}}">
                                    <i class="fas fa-eye"></i></a>
					    		@endif
					    		@if(!empty($show->xbid_attch))
				    			<a href="{{ route('downloadrfq',[$show->xbid_id]) }}" data-nbr="{{$show->xbid_id}}" data-attch="{{$show->xbid_attch}}"><i class="fas fa-download"></i> </a>
				    			@endif
					    	</td>
					  </tr>
					@empty
					<tr>
						<td colspan='12' class='text-danger'>
							<center><b>No Data Available</b></center>
						</td>
					</tr>
					@endforelse  
					<tr>
						<td></td>
					</tr> 
			    </tbody>
			  </table>
  {!! $users->render() !!}
</div>
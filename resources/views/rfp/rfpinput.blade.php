@extends('layout.layout')

@section('menu_name','Request For Purchase Data Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Request For Purchase Data Maintenance</li>
</ol>
@endsection


@section('content')
	@if(session('errors'))
	    <div class="alert alert-danger">
	        @foreach($errors as $error)
	            <li>{{ $error }}</li>
	        @endforeach
	    </div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
			{{ session()->get('error') }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif

	@if(session()->has('updated'))
		<div class="alert alert-success  alert-dismissible fade show"  role="alert">
			{{ session()->get('updated') }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif

	<input type="hidden" id="tmprfpnumber"/>
	<input type="hidden" id="tmpsupplier"/>
	<input type="hidden" id="tmpstatus"/>
	<input type="hidden" id="tmpenduser"/>
	<input type="hidden" id="tmpdatefrom"/>
	<input type="hidden" id="tmpdateto"/>

	<button  class="btn bt-action" style="margin-left:10px;" data-toggle="modal" data-target="#createModal">
	Create RFP</button>
	<br><br>

	<!--Search By RFP Number-->
	<div class="form-group row col-md-12">
	    <label for="s_rfpnumber" class="col-md-2 col-form-label text-md-right">{{ __('RFP No.') }}</label>
	    <div class="col-md-3">
	        <input id="s_rfpnumber" type="text" class="form-control" name="s_rfpnumber" 
	        value="" autofocus autocomplete="off">
	    </div>
	    <label for="s_supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
	    <div class="col-md-3">
	        <input id="s_supplier" type="text" class="form-control" name="s_supplier" 
	        value="" autofocus autocomplete="off">
	    </div>
	</div>

	<div class="form-group row col-md-12">
		<label for="s_status" class="col-md-2 col-form-label text-md-right">{{ __('Status') }}</label>
		<div class="col-md-3">
			<select id="s_status" class="form-control" name="s_status"  autofocus autocomplete="off">
				<option value=""> --Select Status-- </option>
				<option value="New Request">New Request</option>
				<option value="Approved">Approved</option>
				<option value="Rejected">Rejected</option>
				<option value="Close">Closed</option>
			</select>
		</div>

		<label for="s_enduser" class="col-md-2 col-form-label text-md-right">{{ __('Request By') }}</label> 
		<div class="col-md-3">
			<input id="s_enduser" type="text" class="form-control" name="s_enduser" autofocus autocomplete="off">
		</div>
	</div>

	<div class="form-group row col-md-12">
		<label for="datefrom" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Due Date From') }}</label> 
     	<div class="col-md-4 col-lg-3">
          	<input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="YYYY-MM-DD"
                  required autofocus autocomplete="off">
      	</div>
     	<label for="dateto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Due Date To') }}</label>
      	<div class="col-md-4 col-lg-3">
        	<input type="text" id="dateto" class="form-control" name='dateto' placeholder="YYYY-MM-DD"
                  required autofocus autocomplete="off">
		</div>
		  
		<div class="offset-0">
	        <input type="button" class="btn bt-ref" 
			id="btnsearch" value="Search" />
			<button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
		</div>
	</div>

	<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;"">
	<!-- <table class="table table-bordered mt-4 text-center no-footer mini-table" id="dataTable" width="100%" cellspacing="0"> -->
	<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr>
				<!-- <th style="width:5%;">No.</th> -->
				<th style="width:10%;">RFP No.</th>
				<th style="width:15%;">Request By</th>
				<th style="width:15%;">Supplier</th>
				<th style="width:10%;">End User</th>
				<th style="width:10%;">Due date</th>
				<th style="width:10%;">Status</th>
				<th style="width: 5%;">Edit</th>
				<th style="width: 5%;">Close</th>
				<th style="width: 6%;">Route To</th>
			</tr>
		</thead>
		<tbody>
		@include('rfp.tablerfpinput')
		</tbody>
	</table>
	<input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	<input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
	<input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
	</div>

	<!-- CREATE RFP -->

	<div class="modal fade" id="createModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="exampleModalLabel">Create RFP</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form id="createnew" class="form-horizontal" enctype="multipart/form-data" method="POST" action="/insertrfp">
					{{csrf_field()}}

					<?php 
						if($alert == null){
							$nomor = '';
						}else{
							$nomor = $alert->xrfq_rfp_prefix.$date.'-'.$alert->xrfq_rfp_nbr;
						}
					?>

					<div style="display: none;" >
						<label for="rfpnumber" class="col-md-4 col-form-label text-md-right">{{ __('RFP Number') }}</label>
						<div class="col-md-7">
							<input id="rfpnumber" type="text" class="form-control" name="rfpnumber" 
							value="<?php echo $nomor?>" readonly autofocus>
							@if ($errors->has('rfpnumber'))
								<span class="help-block">
									<strong>{{ $errors->first('rfpnumber') }}</strong>
								</span>
							@endif
						</div>		
					</div>
	
					<div class="modal-body">
						<div class="form-group row pr-5">
							<label for="supp" class="col-md-2 col-form-label text-md-right">{{ __('Supplier :') }}</label>
							<div class="col-md-4">
								<select id="supp" class="form-control supp" name="supp" style="font-size: 16px;" autofocus>
									<option value="" style="font-size: 16px !important;"> Select Supplier</option>
									@foreach($supp as $supp)
										<option value="{{ $supp->xalert_supp}}" style="font-size: 16px !important;" >{{$supp->xalert_supp.' - '.$supp->xalert_nama}}</option>
									@endforeach
								</select>
								@if ($errors->has('supp'))
									<span class="help-block">
										<strong>{{ $errors->first('supp') }}</strong>
									</span>
								@endif
							</div>

							<label for="enduser" class="col-md-2 col-form-label text-md-right">{{ __('End User :') }}</label>
							<div class="col-md-4">
								<input type="text" id="enduser" name="enduser" class="form-control" value="" autofocus autocomplete="off">
								@if ($errors->has('enduser'))
									<span class="help-block">
										<strong>{{ $errors->first('enduser') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group row pr-5">
							<label for="shipto" class="col-md-2 col-form-label text-md-right">{{ __('Ship-to :') }}</label>
							<div class="col-md-4">
								<input type="text" id="shipto" name="shipto" class="form-control" value="" autofocus autocomplete="off">
								@if ($errors->has('shipto'))
									<span class="help-block">
										<strong>{{ $errors->first('shipto') }}</strong>
									</span>
								@endif
							</div>

							<label for="site" class="col-md-2 col-form-label text-md-right">{{ __('Site :') }}</label>
							<div class="col-md-4">
								<select id="site" class="form-control site" name="site" style="font-size: 16px !important;" required autofocus>
									<option value="" style="font-size: 16px !important;"> Select Site </option>
									@foreach($site as $site)
										<option value="{{$site->xsite_site}}" style="font-size: 16px !important;">{{$site->xsite_site.' - '.$site->xsite_desc}}</option>
									@endforeach
								</select>
								@if ($errors->has('site'))
									<span class="help-block">
										<strong>{{ $errors->first('site') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="form-group row pr-5">
							<label for="dept" class="col-md-2 col-form-label text-md-right">{{ __('Dept :') }}</label>
							<div class="col-md-4">
								<input id="dept" value="{{$dept}}" name="dept" class="form-control dept" style="font-size: 16px !important;" readonly required autofocus>
									@if ($errors->has('dept'))
										<span class="help-block">
											<strong>{{ $errors->first('dept') }}</strong>
										</span>
									@endif
							</div>
						</div>

						<div class="form-group row pr-3">
							<div class="col-md-12">
								<table id="rfptable" class="table order-list">
									<thead>
										<tr>
											<!-- <th style="width: 5%;">Line</th> -->
											<th style="width: 30%;">Item</th>
											<!-- <th style="width: 18%;">Item Desc</th> -->
											<th style="width: 10%;">Need Date</th>
											<th style="width: 10%;">Due Date</th>
											<th style="width: 10%;">Qty Ordered</th>
											<th style="width: 10%;">UM</th>
											<th style="width: 15%;">Price</th>
											<th style="width: 10%;"></th>
										</tr>
									</thead>
									<tbody id="createbody">
									</tbody>
									<tfoot>
										<tr>
											<td colspan="12">
												<input type="button" class="btn btn-lg btn-block"
												id="addrow" value="Add Row" style="background-color: #1234A5; color:white; font-size:16px"/>
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<div class="form-group row justify-content-center">
                    		<label class="col-md-3 my-auto col-form-label" style="margin-top:8px;">Do You Want Notify Approver ?</label>
                    		<div class="col-md-3">
								<select id="kirimnotif" class="form-control" name="kirimnotif" autofocus required>
									<option value="">--Select--</option>
									<option value="Y">Yes</option>
									<option value="N">No</option>
								</select>
                    		</div> 
               			</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-success bt-action" id="btnconf">Confirm</button>
						<button type="button" class="btn bt-action" id="btnloading" style="display: none;">
							<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;Loading
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- EDIT MODAL -->
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="exampleModalLabel">Edit RFP</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form id="update" class="form-horizontal" method="POST" action="/updaterfpmaint">
					{{csrf_field()}}

					<?php 
						if($alert == null){
							$nomor1 = '';
						}else{
							$nomor1 = $alert->xrfq_rfp_prefix.$date.'-'.$alert->xrfq_rfp_nbr;
						}
					?>

								

					<div class="modal-body">
						<div class="form-group row col-md-12">
							<label for="u_supp" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Supplier :') }}</label>
							<div class="col-md-2">
								<input type="text" id="u_supp" name="u_supp" class="form-control" value="" required readonly autofocus autocomplete="off">
							</div>

							<label for="u_enduser" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('End User :') }}</label>
							<div class="col-md-2">
								<input type="text" id="u_enduser" name="u_enduser" class="form-control" value="" autofocus readonly autocomplete="off">
							</div>

							<label for="u_site" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Site :') }}</label>
							<div class="col-md-2">
								<input type="text" id="u_site" name="u_site" class="form-control" value="" autofocus readonly autocomplete="off">
							</div>
						</div>

						<div class="form-group row col-md-12">
							<label for="u_shipto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Ship-to :') }}</label>
							<div class="col-md-2">
								<input type="text" id="u_shipto" name="u_shipto" class="form-control" value="" autofocus readonly autocomplete="off">
							</div>

							<label for="u_dept" class="col-md-2 col-form-label text-md-right">{{ __('Dept :') }}</label>
							<div class="col-md-2">
								<input type="text" id="u_dept" name="u_dept" class="form-control" readonly autocomplete="off">
							</div>

							<label for="u_rfpnumber" class="col-md-2 col-form-label text-md-right">{{ __('RFP Number :') }}</label>
							<div class="col-md-2">
							<input id="u_rfpnumber" type="text" class="form-control" name="u_rfpnumber" 
							value="<?php echo $nomor1?>" readonly autofocus>
							</div>
						</div>
							<input type="hidden" id="rfpmstrs_duedate" name="rfpmstrs_duedate">
							<input type="hidden" id="rfp_status" name="rfp_status">
						<div class="table-responsive form-group row">
							<div class="col-md-12">
								<table id="rfptable1" class="table edit-list">
									<thead>
										<tr>
											<!-- <th style="width: 5%;">Line</th> -->
											<th style="width: 30%;">Item</th>
											<!-- <th style="width: 15%;">Item Desc</th> -->
											<th style="width: 10%;">Need Date</th>
											<th style="width: 10%;">Due Date</th>
											<th style="width: 10%;">Qty Ordered</th>
											<th style="width: 10%;">UM</th>
											<th style="width: 15%;">Price</th>
											<th style="width: 20%;"></th>
										</tr>
									</thead>
									<tbody id="editbody">
									</tbody>
									<tfoot>
										<tr>
											<td colspan="12">
												<input type="button" class="btn btn-lg btn-block"
												id="addrowedit" value="Add Row" style="background-color: #1234A5; color:white; font-size:16px"/>
											</td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
						<button type="button" class="btn bt-action" id="e_btnloading" style="display: none;">
							<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;Loading
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Close RFP -->
	<div class="modal fade" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="exampleModalLabel">Close RFP</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form id="close" class="form-horizontal" method="POST" action="/cancelrfp">
					{{csrf_field()}}

					<?php 
						if($alert == null){
							$nomor2 = '';
						}else{
							$nomor2 = $alert->xrfq_rfp_prefix.$date.'-'.$alert->xrfq_rfp_nbr;
						}
					?>

						<div style="display: none;" >
						<label for="d_rfpnumber" class="col-md-4 col-form-label text-md-right">{{ __('RFP Number') }}</label>
						<div class="col-md-7">
							<input id="d_rfpnumber" type="text" class="form-control" name="d_rfpnumber" 
							value="<?php echo $nomor2?>" readonly autofocus>
						</div>		
					</div>

					<div class="modal-body">
						<span class="col-md-12"><b>Are you sure want to close this RFP <span id="thisrfpnbr"><b></b></span> ?</b></span>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-info bt-action" id="c_btnclose" data-dismiss="modal">No</button>
						<button type="submit" class="btn btn-success bt-action" id="c_btnconf">Yes</button>
						<button type="button" class="btn bt-action" id="c_btnloading" style="display: none;">
							<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;Loading
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Route RFP -->
	<div class="modal fade" id="routeModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="exampleModalLabel">Route to Action</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>

				<div class="modal-body">
					<div class="form-group row">
						<label for="rfpnumb" class="col-2 col-sm-2 col-md-2 col-lg-2 col-form-label text-md-right">{{ __('RFP No.') }}</label>
						<div class="col-7 col-sm-7 col-md-7 col-lg-5">
							<input type="text" class="form-control" id="rfpnumb" name="rfpnumb" readonly>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-12 col-md-12" style="overflow-x: auto; display: block;white-space: nowrap;">
							<table id='routetable' class='table route-list'>
								<thead>
									<tr>
										<th style="width:10%">No.</th>
										<th style="width:20%">Approver</th>
										<th style="width:20%">Alt Approver</th>
										<th style="width:15%">Reason</th>
										<th style="width:10%">Status</th>
										<th style="width:15%">Timestamp</th>
										
									</tr>
								</thead>
								<tbody id='bodyroute'>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="modal-footer">
				<button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Close</button>
				</div>

			</div>
		</div>
	</div>

	<div id="loader" class="lds-dual-ring hidden overlay"></div>
@endsection

@section('scripts')

<script>
	$( function(){
		$('#datefrom').datepicker({
			dateFormat : 'yy-mm-dd'
		});
		$('#dateto').datepicker({
			dateFormat : 'yy-mm-dd'
		});
	});
	

	function selectPicker(){

		$('.selectpicker').selectpicker().focus();

	}
	

	$('#createnew').submit(function(e){

		// e.preventDefault();

		var rfpnumber = document.getElementById('rfpnumber').value;
		var supp = document.getElementById('supp').value;
		var enduser = document.getElementById('enduser').value;
		var shipto = document.getElementById('shipto').value;
		var site = document.getElementById('site').value;
		var dept = document.getElementById('dept').value;
		var emailflag = document.getElementById('kirimnotif').value;

		// var itemno = document.getElementById('itemno').value;

		// var needdate = document.getElementById('needdate').value.split('/');
		// var duedate = document.getElementById('duedate').value.split('/');

		// var qtyorder = document.getElementsByName('qtyorder[]').value;

		// var reg  =/^(\s*|\d+)$/;
		// var regqty = /^(\s*|\d+\.\d*|\d+)$/;

		// if(qtyorder == 0){
		// 	alert('Qty Order cannot be 0');
		// 	return false;
		// }else if(!regqty.test(qtyorder)){
		// 	alert('Qty Order Must ne number');
		// 	return false;
		// }else{
		// 	document.getElementById('btnclose').style.display = 'none';
		// 	document.getElementById('btnconf').style.display = 'none';
		// 	document.getElementById('btnloading').style.display = '';
		// 	$(this).unbind('submit').submit();
		// }
	});

	$('#update').submit(function(e){
		var rfpnumber = document.getElementById('u_rfpnumber').value;
		var supp = document.getElementById('u_supp').value;
		var enduser = document.getElementById('u_enduser').value;
		var shipto = document.getElementById('u_shipto').value;
		var site = document.getElementById('u_site').value;
		var dept = document.getElementById('u_dept').value;
	});

	$(document).on('submit', '#createnew,#update,#close', function(e) {
		document.getElementById('e_btnconf').style.display = 'none';
		document.getElementById('btnconf').style.display = 'none';
		document.getElementById('e_btnclose').style.display = 'none';
		document.getElementById('btnclose').style.display = 'none';
		document.getElementById('e_btnloading').style.display = '';
		document.getElementById('btnloading').style.display = '';

		document.getElementById('c_btnconf').style.display = 'none';
		document.getElementById('c_btnclose').style.display = 'none';
		document.getElementById('c_btnloading').style.display = '';
	});

	$(document).on('click', '.editRFP', function(){
		var rfpnbr = $(this).data('rfpnbr');
		var supp = $(this).data('supp');
		var shipto = $(this).data('shipto');
		var enduser = $(this).data('enduser');
		var qtyorder = $(this).data('qtyorder');
		var site = $(this).data('site');
		var dept = $(this).data('dept');
		var duedate = $(this).data('duedate');
		var status = $(this).data('status');

		document.getElementById("u_rfpnumber").value = rfpnbr;
		document.getElementById("u_supp").value = supp;
		document.getElementById("u_shipto").value = shipto;
		document.getElementById("u_enduser").value = enduser;
		document.getElementById("u_dept").value = dept;
		document.getElementById("u_site").value = site;
		document.getElementById("rfpmstrs_duedate").value = duedate;
		document.getElementById("rfp_status").value = status;

		jQuery.ajax({
				type : "get",
				url : "{{URL::to("searchold")}}",
				data :{
					search : rfpnbr,
				},
				success:function(data){
					$('#editbody').html(data);
				}
		
		});
	});

	$(document).on('change', '.needdate', function(){
		let data = (this).value;

		$(this).closest('tr').find('.duedate').attr({
			"min" : data,
		});	
	});

	$(document).on('click', '.closeRFP', function(e){
		var rfpnbr = $(this).data('rfpnbr');

		document.getElementById('thisrfpnbr').innerHTML = rfpnbr;
		document.getElementById('d_rfpnumber').value = rfpnbr;
	});

	$(document).on('click', '.routeRFP', function(e){
		var rfpnbr = $(this).data('rfpnbr');

		document.getElementById('rfpnumb').value = rfpnbr;

		jQuery.ajax({
			type : "get",
			url : "{{URL::to("searchroute")}}",
			data : {
				search : rfpnbr,
			},
			success:function(data){
				$('#bodyroute').html(data);
			}
		});
	});

	$(document).ready(function(){

		$('#supp').select2({
			width: '100%'
		});
		$('#site').select2({
			width: '100%'
		});

		// $('#needdate').datepicker({
		// 	dateFormat : 'dd/mm/yy'
		// });

		var counter = 0;

		$("#addrow").on('click', function(){
			var newRow = $("<tr>");
			var cols = "";

			// cols += '<td>';
			// cols += '';
			// cols += '</td>';

			cols += '<td>';
			cols += '<div style="border: 1px solid black; width: 302px;">';
			cols += '<select id="itemno[]" class="form-control itemno selectpicker" name="itemno[]" data-width="300px" data-live-search="true" required autofocus>';
			cols += '<option value="">Select Data</option>';
			@foreach($item as $item)
			cols += '<option value="{{$item->xitemreq_part}}">{{$item->xitemreq_part}} -- {{$item->xitemreq_desc}}</option>';
			@endforeach
			cols += '</select>';
			cols += '</div>';
			cols += '</td>';

			// cols += '<td data-title="itemdesc[]"><input type="text" id="itemdesc" class="form-control form-control-sm itemdesc" autocomplete="off" name="itemdesc[]" style="height:37px" readonly required/></td>';
			cols += '<td data-title="needdate[]"><input type="date" min="{{Carbon\Carbon::now()->format("Y-m-d")}}" class="form-control form-control-sm needdate" autocomplete="off" name="needdate[]"  style="height:37px; width:150px" placeholder="DD/MM/YYYY" required></td>';
			cols += '<td data-title="duedate[]"><input type="date" min="{{Carbon\Carbon::now()->format("Y-m-d")}}" class="form-control form-control-sm duedate" autocomplete="off" name="duedate[]" style="height:37px; width:150px" placeholder="DD/MM/YYYY" required></td>';
			cols += '<td date-title="qtyorder[]"><input type="number" min="0" step="0.01" class="form-control form-control-sm qtyorder" autocomplete="off" name="qtyorder[]" style="height:37px; width:100%" required></td>';
			
			cols += '<td date-tile="um[]"><input type="text" id="um" class="form-control form-control-sm um" name="um[]" autocomplete="off" style="height:37px" readonly required></td>';
			cols += '<td date-title="price[]"><input type="number" min="0" step="0.01" class="form-control form-control-sm price" autocomplete="off" name="price[]" style="height:37px; width:100%" required></td>';
			cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger" value="delete"></td>';
			cols += '<td data-title="itemflg[] style="display:none;"><input type="hidden" class="form-control form-control-sm itemflg" name="itemflg[]" value="New Request"></td>'

			cols += '</tr>';

			// $(document).on('change', '#itemno', function(){
			// 	data = document.getElementById('itemno').value;

			// 	// alert(data);

			// 	jQuery.ajax({
			// 		type : "get",
			// 		url : "{{URL::to("searchitemdesc")}}",
			// 		data : {
			// 			search : data,
			// 		},
			// 		success:function(data){
			// 			console.log(data);
			// 			document.getElementById('um').value = data[0].xitemreq_um;
			// 		}
			// 	});
			// });

			newRow.append(cols);
			$('table.order-list').append(newRow);
			counter++;

			selectPicker();
		});

		$('table.order-list').on('click', '.ibtnDel', function(e){
			$(this).closest('tr').remove();
			// counter -= 1;
		});

		var concont = 0;
		//ADD ROW EDIT
		$("#addrowedit").on('click', function(){
			var newRow = $("<tr>");
			var cols = "";

			// cols += '<td>';
			// cols += '';
			// cols += '</td>';

			cols += '<td>';
			cols += '<div style="border: 1px solid black; width: 302px;">';
			cols += '<select id="itemno[]" class="form-control itemno selectpicker" name="itemno[]" data-width="300px" data-live-search="true" required autofocus>';
			// cols += '<option value="">Select Data</option>';
			@foreach($item2 as $item2)
			cols += '<option value="{{$item2->xitemreq_part}}">{{$item2->xitemreq_part}} -- {{$item2->xitemreq_desc}}</option>';
			@endforeach
			cols += '</select>';
			cols += '</div>';
			cols += '</td>';

			// cols += '<td data-title="itemdesc[]"><input type="text" id="itemdesc" class="form-control form-control-sm itemdesc" autocomplete="off" name="itemdesc[]" style="height:37px" readonly required/></td>';
			cols += '<td data-title="needdate[]"><input type="date" min="{{Carbon\Carbon::now()->format("Y-m-d")}}" class="form-control form-control-sm needdate" autocomplete="off" name="needdate[]"  style="height:37px; width:180px" placeholder="DD/MM/YYYY" required></td>';
			cols += '<td data-title="duedate[]"><input type="date" min="{{Carbon\Carbon::now()->format("Y-m-d")}}" class="form-control form-control-sm duedate" autocomplete="off" name="duedate[]" style="height:37px; width:180px" placeholder="DD/MM/YYYY" required></td>';
			cols += '<td date-title="qtyorder[]"><input type="number" min="0" step="0.01" class="form-control form-control-sm qtyorder" autocomplete="off" name="qtyorder[]" style="height:37px; width:100%" required></td>';
			// cols += '<td date-tile="um[]"><input type="text" id="um" class="form-control form-control-sm um" name="um[]" autocomplete="off" style="height:37px" readonly required></td>';
			cols += '<td date-tile="um[]"><input type="text" id="um" class="form-control form-control-sm um" name="um[]" autocomplete="off" style="height:37px" readonly required></td>';
			cols += '<td date-title="price[]"><input type="number" min="0" step="0.01" class="form-control form-control-sm price" autocomplete="off" name="price[]" style="height:37px; width:100%" required></td>';
			cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger" value="delete"></td>';
			cols += '</tr>';

			newRow.append(cols);
			$('table.edit-list').append(newRow);
			concont++;

			selectPicker();
		});

		$('table.edit-list').on('click', '.ibtnDel', function(e){
			$(this).closest('tr').remove();
			// concont -= 1;
		});
		
		
		$(document).on('change', '.selectpicker', function() {
			var um = $(this).closest('tr').find('.um');
			var price = $(this).closest('tr').find('.price')
			var item = $(this).val();

			$.ajax({
			url: "/getumitem",
			data: {
				item: item,
			},
			success: function(data) {
				
				console.log(data);

				um.val($.trim(data.item_um));
				price.val($.trim(data.item_price));
				// um.val($.trim(data));
			}
			})
		});
	});

	function fetch_data(page, rfpnumber, supplier, status, requestby, datefrom, dateto) {
      $.ajax({
        url: "/inputrfp/rfpinputsearch?page=" + page + "&rfp=" + rfpnumber + "&supp=" + supplier + "&status=" + status + "&requestby=" + requestby + "&datefrom=" + datefrom + "&dateto=" + dateto,
		beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    		$('#loader').removeClass('hidden')
    	},
        success: function(data) {
          console.log(data);
          $('tbody').html('');
          $('tbody').html(data);
        },
		complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
			$('#loader').addClass('hidden')
		},

      })
    }


    $(document).on('click', '#btnsearch', function() {
      var rfpnumber  = $('#s_rfpnumber').val(); 
      var supplier    = $('#s_supplier').val(); 
      var status = $('#s_status').val();
      var requestby = $('#s_enduser').val();
      var datefrom = $('#datefrom').val();
	  var dateto = $('#dateto').val();

      // var column_name = $('#hidden_column_name').val();
      // var sort_type = $('#hidden_sort_type').val();
      var page = 1;

      document.getElementById('tmprfpnumber').value  = rfpnumber;
      document.getElementById('tmpsupplier').value = supplier;
      document.getElementById('tmpstatus').value = status;
      document.getElementById('tmpenduser').value = requestby;
      document.getElementById('tmpdatefrom').value = datefrom;
	  document.getElementById('tmpdateto').value = dateto;

      fetch_data(page, rfpnumber, supplier, status, requestby, datefrom, dateto);
    });

  
    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();

      var rfpnumber  = $('#tmprfpnumber').val(); 
      var supplier    = $('#tmpsupplier').val(); 
      var status = $('#tmpstatus').val();
      var requestby = $('#tmpenduser').val();
      var datefrom = $('#tmpdatefrom').val();
	  var dateto = $('#tmpdateto').val();
      
      fetch_data(page, rfpnumber, supplier, status, requestby, datefrom, dateto);
    });

	$(document).on('click', '#btnrefresh', function() {
	  var rfpnumber  = ''; 
      var supplier    = ''; 
      var status = '';
      var requestby = '';
      var datefrom = '';
	  var dateto = ''; 
      var page = 1;

      document.getElementById('s_rfpnumber').value     = '';
      document.getElementById('s_supplier').value          = '';
      document.getElementById('s_status').value = '';
      document.getElementById('s_enduser').value = '';
      document.getElementById('datefrom').value = '';
      document.getElementById('dateto').value = '';
	  document.getElementById('tmprfpnumber').value  = rfpnumber;
      document.getElementById('tmpsupplier').value = supplier;
      document.getElementById('tmpstatus').value = status;
      document.getElementById('tmpenduser').value = requestby;
      document.getElementById('tmpdatefrom').value = datefrom;
	  document.getElementById('tmpdateto').value = dateto;

      fetch_data(page, rfpnumber, supplier, status, requestby, datefrom, dateto);
    });

	// $(document).on('click','.pagination a', function(e){
	// 	e.preventDefault();

	// 	//alert('123');
	// 	var page = $(this).attr('href').split('?page=')[1];

	// 	//console.log(page);
	// 	getData(page);

	// });

	// function getData(page){
	// 	var rfpnumber = document.getElementById('s_rfpnumber').value;
	// 	var supplier = document.getElementById('s_supplier').value;
	// 	var status = document.getElementById('s_status').value;
	// 	var requestby = document.getElementById('s_enduser').value;
	// 	var datefrom = document.getElementById('datefrom').value;
	// 	var dateto = document.getElementById('dateto').value;
	// 	$.ajax({
	// 		url: '/rfpinputsearch/fetch_data?page='+ page,
	// 		type: "get",
	// 		data : {
	// 				rfp : rfpnumber,
	// 				supp : supplier,
	// 				status : status,
	// 				datefrom : datefrom,
	// 				dateto : dateto,
	// 				enduser : requestby,
	// 		},
	// 		datatype: "html" 
	// 	}).done(function(data){
	// 		console.log('Page = '+ page);

	// 		$(".tag-container").empty().html(data);

	// 	}).fail(function(jqXHR, ajaxOptions, thrownError){
	// 		Swal.fire({
    //             icon: 'error',
    //             text: 'No Response From Server',
    //         })
	// 	});
	// }

	function formatNumber(num) {
		return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
	}

	$(document).on('change', '#site', function(){
		data = document.getElementById('site').value;

		jQuery.ajax({
			type : "get",
			url : "{{URL::to("searchshipto")}}",
			data : {
				search : data,
			},
			success:function(data){
				console.log(data);
				document.getElementById('shipto').value = data[0].xsite_site;
			}
		});
	});


	// $('#btnsearch').on('click', function(){
	// 	var rfpnumber = document.getElementById('s_rfpnumber').value;
	// 	var supplier = document.getElementById('s_supplier').value;
	// 	var status = document.getElementById('s_status').value;
	// 	var requestby = document.getElementById('s_enduser').value;
	// 	var datefrom = document.getElementById('datefrom').value;
	// 	var dateto = document.getElementById('dateto').value;


	// 	jQuery.ajax({
	// 		type : "get",
	// 		url : "{{URL::to("rfpinputsearch")}}",
	// 		data : {
	// 			rfp : rfpnumber,
	// 			supp : supplier,
	// 			status : status,
	// 			datefrom : datefrom,
	// 			dateto : dateto,
	// 			requestby : requestby,
	// 		},
    //         beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    //             $('#loader').removeClass('hidden')
    //         },
	// 		success:function(data){
	// 			console.log(data);
	// 			$(".tag-container").empty().html(data);
	// 		},
    //         complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
    //             $('#loader').addClass('hidden')
    //         },
	// 	});
	// });

	// $('#btnrefresh').on('click', function(){
	// 	var rfpnumber = '';
	// 	var supp = '';
	// 	var requestby = '';
	// 	var status = '';
	// 	var datefrom = '';
	// 	var dateto = '';

	// 	jQuery.ajax({
	// 		type : "get",
	// 		url : "{{URL::to("rfpinputsearch")}}",
	// 		data : {
	// 			rfp : rfpnumber,
	// 			supp : supp,
	// 			status : status,
	// 			datefrom : datefrom,
	// 			dateto : dateto,
	// 			requestby : requestby,
	// 		},
    //         beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    //             $('#loader').removeClass('hidden')
    //         },
	// 		success:function(data){
	// 			console.log(data);
	// 			document.getElementById('s_rfpnumber').value = '';
	// 			document.getElementById('s_supplier').value = '';
	// 			document.getElementById('s_status').value = '';
	// 			document.getElementById('s_enduser').value = '';
	// 			document.getElementById('datefrom').value = '';
	// 			document.getElementById('dateto').value = '';

	// 			$(".tag-container").empty().html(data);
	// 		},
    //         complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
    //             $('#loader').addClass('hidden')
    //         },
	// 	});

	// });


</script>

@endsection
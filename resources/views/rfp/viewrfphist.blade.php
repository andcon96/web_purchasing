@extends('layout.layout')

@section('menu_name','RFP Audit Data')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFP Audit Data</li>
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

		<label for="s_requestby" class="col-md-2 col-form-label text-md-right">{{ __('Request By') }}</label>
		<div class="col-md-3">
			<input id="s_requestby" type="text" class="form-control" name="s_requestby" autofocus autocomplete="off">
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
			<button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
		</div>
	</div>

	<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;"">
	<!-- <table class="table table-bordered mt-4 text-center no-footer mini-table" id="dataTable" width="100%" cellspacing="0"> -->
	<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr>
		    	<!-- <th style="width:5%;">No.</th> -->
				<th style="width:10%;">RFP No.</th>
				<th style="width:10%;">Request By</th>
				<th style="width:10%;">Supplier</th>
		     	<!-- <th style="width:10%;">End User</th> -->
		     	<!-- <th style="width:10%;">Due date</th> -->
		     	<th style="width:10%;">Status</th>
		     	<th style="width:5%;">Item No.</th>
		     	<th style="width:5%;">Need Date</th>
		     	<th style="width:5%;">Due Date</th>
		     	<th style="width:5%;">Qty</th>
		     	<th style="width:15%;">RFQ/PO/PR No.</th>
		     	<th style="width:15%;">Timestamp</th>
				<!-- <th style="width: 5%;">Detail</th> -->
			</tr>
		</thead>
		<tbody>
		@include('rfp.loadhistrfp')
		</tbody>
	</table>
	<input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	<input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
	<input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
	</div>

	<!-- EDIT MODAL -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center" id="exampleModalLabel">Detail RFP</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form id="detail" class="form-horizontal" method="get" name="detail">
					{{csrf_field()}}

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
							value="" readonly autofocus>
							</div>
						</div>

						<div class="table-responsive form-group row">
							<div class="col-md-12">
								<table id="rfptable1" class="table edit-list">
									<thead>
										<tr>
											<!-- <th style="width: 5%;">Line</th> -->
											<th style="width: 30%;">Item No.</th>
											<!-- <th style="width: 15%;">Item Desc</th> -->
											<th style="width: 15%;">Need Date</th>
											<th style="width: 15%;">Due Date</th>
											<th style="width: 15%;">Qty Ordered</th>
											<th style="width: 8%;">UM</th>
											<th style="width: 15%;">Create At</th>
											<th style="width: 20%;"></th>
										</tr>
									</thead>
									<tbody id="editbody">
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Back</button>
						<button type="button" class="btn bt-action" id="e_btnloading" style="display: none;">
							<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;Loading
						</button>
					</div>
				</form>
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

		$('#update').submit(function(e){
			var rfpnumber = document.getElementById('u_rfpnumber').value;
			var supp = document.getElementById('u_supp').value;
			var enduser = document.getElementById('u_enduser').value;
			var shipto = document.getElementById('u_shipto').value;
			var site = document.getElementById('u_site').value;
			var dept = document.getElementById('u_dept').value;
		});

		$(document).on('click', '.detailhist', function(){
			var rfpnbr = $(this).data('rfpnbr');
			var supp = $(this).data('supp');
			var shipto = $(this).data('shipto');
			var enduser = $(this).data('enduser');
			var qtyorder = $(this).data('qtyorder');
			var site = $(this).data('site');
			var dept = $(this).data('dept');

			document.getElementById("u_rfpnumber").value = rfpnbr;
			document.getElementById("u_supp").value = supp;
			document.getElementById("u_shipto").value = shipto;
			document.getElementById("u_enduser").value = enduser;
			document.getElementById("u_dept").value = dept;
			document.getElementById("u_site").value = site;
			

			jQuery.ajax({
					type : "get",
					url : "{{URL::to("searchdets")}}",
					data :{
						search : rfpnbr,
					},
					success:function(data){
						$('#editbody').html(data);
					}
			
			});

		});

		function fetch_data(page, rfpnumber, supplier, status, requestby, datefrom, dateto) {
      $.ajax({
        url: "rfphist/histsearch?page=" + page + "&rfp=" + rfpnumber + "&supp=" + supplier + "&status=" + status + "&enduser=" + requestby + "&datefrom=" + datefrom + "&dateto=" + dateto,
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
      var requestby = $('#s_requestby').val();
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

      document.getElementById('s_rfpnumber').value = '';
      document.getElementById('s_supplier').value = '';
      document.getElementById('s_status').value = '';
      document.getElementById('s_requestby').value = '';
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
		// 	var requestby = document.getElementById('s_requestby').value;
		// 	var datefrom = document.getElementById('datefrom').value;
		// 	var dateto = document.getElementById('dateto').value;
		// 	$.ajax({
		// 		url: '/histsearch/fetch_data?page='+ page,
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
		// 			icon: 'error',
		// 			text: 'No Response From Server',
		// 		})
		// 	});
	  	// }

		function formatNumber(num) {
      		return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
		}

		// $('#btnsearch').on('click', function(){
		// 	var rfpnumber = document.getElementById('s_rfpnumber').value;
		// 	var supplier = document.getElementById('s_supplier').value;
		// 	var status = document.getElementById('s_status').value;
		// 	var requestby = document.getElementById('s_requestby').value;
		// 	var datefrom = document.getElementById('datefrom').value;
		// 	var dateto = document.getElementById('dateto').value;


		// 	jQuery.ajax({
		// 		type : "get",
		// 		url : "{{URL::to("histsearch")}}",
		// 		data : {
		// 			rfp : rfpnumber,
		// 			supp : supplier,
		// 			status : status,
		// 			datefrom : datefrom,
		// 			dateto : dateto,
		// 			enduser : requestby,
		// 		},
		// 		beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
		// 			$('#loader').removeClass('hidden')
		// 		},
		// 		success:function(data){
		// 			console.log(data);
		// 			$(".tag-container").empty().html(data);
		// 		},
		// 		complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
		// 			$('#loader').addClass('hidden')
		// 		},
		// 	});
		// });

		// $('#btnrefresh').on('click', function(){
		// 	var  rfp = '';
		// 	var supp = '';
		// 	var enduser = '';
		// 	var status = '';
		// 	var datefrom = '';
		// 	var dateto = '';



		// 	jQuery.ajax({
		// 		type : "get",
		// 		url : "{{URL::to("histsearch")}}",
		// 		data : {
		// 			rfp : rfp,
		// 			supp : supp,
		// 			enduser : enduser,
		// 			status : status,
		// 			datefrom : datefrom,
		// 			dateto : dateto,
		// 		},
		// 		beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
		// 			$('#loader').removeClass('hidden')
		// 		},
		// 		success:function(data){
		// 			console.log(data);
		// 			document.getElementById('s_rfpnumber').value = '';
		// 			document.getElementById('s_supplier').value = '';
		// 			document.getElementById('s_status').value = '';
		// 			document.getElementById('s_requestby').value = '';
		// 			document.getElementById('datefrom').value = '';
		// 			document.getElementById('dateto').value = '';


		// 			$(".tag-container").empty().html(data);
		// 		},
		// 		complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
		// 			$('#loader').addClass('hidden')
		// 		},
		// 	});

		// });
		

</script>

@endsection
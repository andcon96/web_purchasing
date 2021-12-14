@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFP Audit Approval</li>
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
    
	<!--Search By RFP Number-->
	<div class="form-group row col-md-12">
	    <label for="s_rfpnumber" class="col-md-2 col-form-label text-md-right">{{ __('RFP No.') }}</label>
	    <div class="col-md-3">
	        <input id="s_rfpnumber" type="text" class="form-control" name="s_rfpnumber" 
	        value="" autofocus autocomplete="off">
	    </div>
	    <!-- <label for="s_supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
	    <div class="col-md-3">
	        <input id="s_supplier" type="text" class="form-control" name="s_supplier" 
	        value="" autofocus autocomplete="off">
	    </div> -->
	</div>

	<!-- <div class="form-group row col-md-12">
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
	</div> -->

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

	@include('rfp.loadapphist')

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
	
	function formatNumber(num) {
		return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
	}

	$('#btnsearch').on('click', function(){
		var rfpnumber = document.getElementById('s_rfpnumber').value;
		// var supplier = document.getElementById('s_supplier').value;
		// var status = document.getElementById('s_status').value;
		// var requestby = document.getElementById('s_requestby').value;
		var datefrom = document.getElementById('datefrom').value;
		var dateto = document.getElementById('dateto').value;


		jQuery.ajax({
			type : "get",
			url : "{{URL::to("apphistsearch")}}",
			data : {
				rfp : rfpnumber,
				// supp : supplier,
				// status : status,
				datefrom : datefrom,
				dateto : dateto,
				// enduser : requestby,
			},
			beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
				$('#loader').removeClass('hidden')
			},
			success:function(data){
				console.log(data);
				$(".tag-container").empty().html(data);
			},
			complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
				$('#loader').addClass('hidden')
			},
		});
	});

	$('#btnrefresh').on('click', function(){
		var  rfp = '';
		// var supp = '';
		// var enduser = '';
		// var status = '';
		var datefrom = '';
		var dateto = '';

		jQuery.ajax({
			type : "get",
			url : "{{URL::to("apphistsearch")}}",
			data : {
				rfp : rfp,
				// supp : supp,
				// enduser : enduser,
				// status : status,
				datefrom : datefrom,
				dateto : dateto,
			},
			beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
				$('#loader').removeClass('hidden')
			},
			success:function(data){
				console.log(data);
				$(".tag-container").empty().html(data);
			},
			complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
				$('#loader').addClass('hidden')
			},
		});

	});


</script>

@endsection
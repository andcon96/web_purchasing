@extends('layout.layout')

@section('menu_name','Request for Quotation Menu')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<style type="text/css">
		.text-color{
			color:#0000CD !important;
		}
		.row{
			margin: 0px;
		}
	</style>

	@if(session('errors'))
		<div class="alert alert-danger">
			@foreach($errors as $error)
	    		<li>{{ $error }}</li>
	    	@endforeach
		</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger" id="getError">
			{{ session()->get('error') }}
		</div>
	@endif	
	<!-- Page Heading -->


  	@if(str_contains( Session::get('menu_flag'), 'RF01'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('inputrfq') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0"> 
				<img src="/img/432.jpg" width="135px" height="150px" />
				RFQ Data Maintenance
			</div>
		</div>
	</div>
	@endif
	@if(str_contains( Session::get('menu_flag'), 'RF02'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfqapprove') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/rqfapp.jpg" width="135px" height="150px" /> 
				RFQ Approval
			</div>
		</div>
	</div>
	@endif
	@if(str_contains( Session::get('menu_flag'), 'RF03'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfqhist') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/rfqhist.jpg" width="135px" height="150px" /> 
				RFQ History Data
			</div>
		</div>
	</div>
	@endif
	@if(str_contains( Session::get('menu_flag'), 'RF04'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('top10menu') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/chart.jpg" width="135px" height="135px" /> 
				Last 10 RFQ and PO Data
			</div>
		</div>
	</div>
	@endif
	@if(str_contains( Session::get('menu_flag'), 'RF06'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfqaudit') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/iconpo.jpg" width="135px" height="135px" /> 
				RFQ Audit Trail
			</div>
		</div>
	</div>
	@endif

@endsection
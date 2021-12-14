@extends('layout.layout')

@section('menu_name', 'Request For Purchase Menu')

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
    <!--Page Heading -->
    @if(str_contains( Session::get('menu_flag'), 'RFP01'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('inputrfp') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0"> 
				<img src="/img/432.jpg" width="135px" height="150px" />
				RFP Data Maintenance
			</div>
		</div>
	</div>
    @endif
    @if(str_contains( Session::get('menu_flag'), 'RFP02'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfpapproval') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/rqfapp.jpg" width="135px" height="150px" /> 
				RFP Approval
			</div>
		</div>
	</div>
    @endif
    @if(str_contains( Session::get('menu_flag'), 'RFP04'))
    <div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfphist') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/iconpo.jpg" width="135px" height="150px" /> 
				RFP Audit Data
			</div>
		</div>
	</div>
    @endif
    @if(str_contains( Session::get('menu_flag'), 'RFP05'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfpaudit') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/iconpo.jpg" width="135px" height="135px" /> 
				RFP Audit Approval
			</div>
		</div>
	</div>
	@endif
	@if(str_contains( Session::get('menu_flag'), 'RFP06'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('rfputil') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/2616.jpg" width="135px" height="135px" /> 
				RFP Approval Utility
			</div>
		</div>
	</div>
	@endif
@endsection
@extends('layout.layout')

@section('menu_name','Inventory Menu')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<style type="text/css">
		.text-color{
			color:#0000CD !important;
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
	
	@if(str_contains( Session::get('menu_flag'), 'IV01'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/bstock';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/safetystck.jpg" /> Safety Stock Data
			</div>	             
		</div>
    </div>  
    @endif
    @if(str_contains( Session::get('menu_flag'), 'IV02'))	
    <div class="row  col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/expitem';" style="cursor:pointer;">
	        <div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/expinven.jpg" width="158" /> Expired inventory
			</div>	                 
		</div>
	</div>  
	@endif
	@if(str_contains( Session::get('menu_flag'), 'IV03'))	
	<div class="row  col-lg-8 col-xl-7 col-md-12"> 
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/noinv';" style="cursor:pointer;">
	        <div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/slowmov.jpg" width="158" /> Slow moving inventory
			</div>	                 
		</div>
	</div> 
	@endif


@endsection
@extends('layout.layout')

@section('menu_name','Sales Menu')

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

	<div class="row">
		@if(str_contains( Session::get('menu_flag'), 'PO01'))
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-4" onclick="location.href='{{ url('pricelistmt') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color text-uppercase mb-1">Price List Create</div>
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    @endif

<!-- 		@if(str_contains( Session::get('menu_flag'), 'PO03'))
		<div class="col-xl-4 col-md-6 mb-4" onclick="location.href='{{ url('poappbrowse') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color text-uppercase mb-1">PO Approval Browse</div>
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    @endif -->
	</div>

@endsection
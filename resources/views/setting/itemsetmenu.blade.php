@extends('layout.layout')

@section('menu_name','Item Setup')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<style>
		.text-color{
			color:#0000CD !important;
		}
	</style>


	<!-- Page Heading -->
	<div class="row">
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-4" onclick="location.href='{{ url('itmsetup') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary test shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color mb-1 text-uppercase">Item Inventory Control</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>
	    <div class="col-xl-4 col-md-6 mb-4" onclick="location.href='{{ url('itemrfqset') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color mb-1 text-uppercase">Item RFQ Control</div>	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	</div>
   <div class="row">
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-4" onclick="location.href='{{ url('itmmstr') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary test shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color mb-1 text-uppercase">Item inventory Master</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>
      <div class="col-xl-4 col-md-6 mb-4" onclick="location.href='{{ url('itmrfqmstr') }}';" style="cursor:pointer;">
	      <div class="card border-bottom-primary test shadow h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-color mb-1 text-uppercase">Item Rfq Master</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>

	</div>

	
	 
@endsection
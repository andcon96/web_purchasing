@extends('layout.layout')

@section('menu_name','Purchase Order Menu')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<style type="text/css">
		.text-color{
			color:#0000CD !important;
		}

		.text123{
			vertical-align: middle;
			position: relative;
			top: 33%;
			height: 100px;
			width: 100% !important;
		}

		.textkpi{
			vertical-align: middle;
			position: relative;
			top: 30%;
			margin-left: 10px;
			height: 100px;
			padding-top: 40px;
		}

		@media only screen and (max-width: 1199px){
			.kpi{
				padding-left : 35px;
			}

			.divkpi{
				padding-left: 45px;
				margin-bottom: 25px;
				display: none !important; 
			}

			.mindivkpi{
				display: block !important;
			}

			.text-info{
				font-size: 15px;
			}
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
	
	<?php
		if($shipmentconf == null){
			$shipment = '';
		}else{
			$shipment = $shipmentconf->Total; 
		}
	?>

	<div class="row mindivkpi" style="display:none;text-align: center;color: red;">
		<div class="col-12">
			 Unconfirmed PO : <?php echo $unconfpo ?> -- Unapproved PO : <?php echo $unapproved ?> -- Shipment to Confirm <?php echo $shipment ?>
		</div>
	</div>

	<div style="float:left" class="row col-lg-8 col-xl-7 col-md-12">
      	@if(str_contains( Session::get('menu_flag'), 'PO01'))
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('pobrowse') }}';" style="cursor:pointer;">
			<div class="mb-0 font-weight-bold text-info text-uppercase">
				<img src="/img/poinfo.jpg" width="160" /> Purchase Order List
			</div>
		</div>
		@endif
      	@if(str_contains( Session::get('menu_flag'), 'PO03'))
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('poappbrowse') }}';" style="cursor:pointer;">                                         
	        <div class="mb-1 font-weight-bold text-info text-uppercase">
	        	<img src="/img/poapr.png" width="160" /> Purchase Order Approval
			</div>              
		</div>
		@endif
      	@if(str_contains( Session::get('menu_flag'), 'PO02'))
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('poreceipt') }}';" style="cursor:pointer;">                                         
	        <div class="mb-1 font-weight-bold text-info text-uppercase">
	        	<img src="/img/porcp.png" width="160" /> Purchase receipt confirmation
			</div>                 
		</div>
		@endif
      	@if(str_contains( Session::get('menu_flag'), 'PO04'))
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('top10menu') }}';" style="cursor:pointer;">
	        <div class="mb-0 font-weight-bold text-info text-uppercase">
	        	<img src="/img/chart.jpg" width="160" /> Last 10 RFQ and PO
			</div>                  
		</div>  
		@endif
		@if(str_contains( Session::get('menu_flag'), 'PO05'))	
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('resetapprove') }}';" style="cursor:pointer;">
			<div class="mb-0 font-weight-bold text-info text-uppercase">
				<img src="/img/iconpo.jpg" width="160" /> 
				Purchase Order Approval Utility
			</div>
		</div>
		@endif
		@if(str_contains( Session::get('menu_flag'), 'PO06'))	
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('poaudit') }}';" style="cursor:pointer;">
			<div class="mb-0 font-weight-bold text-info text-uppercase">
				<img src="/img/iconpo.jpg" width="160" /> 
				Purchase Order Audit Trail
			</div>
		</div>
		@endif
		@if(str_contains( Session::get('menu_flag'), 'PO07'))	
		<div class="col-xl-12 offset-xl-1 col-md-11 offset-md-1 d-flex" onclick="location.href='{{ url('poappaudit') }}';" style="cursor:pointer;">
			<div class="mb-0 font-weight-bold text-info text-uppercase">
				<img src="/img/iconpo.jpg" width="160" /> 
				PO Approval Audit Trail
			</div>
		</div>
		@endif
	</div>

	<div style="float:left" class="col-lg-5">
		<div class="col-xl-12 col-md-12 mt-4 d-flex divkpi">
			<div><img src="/img/KPI 4.png" width="100px" /></div>
			<div class="text123 font-weight-bold text-info text-uppercase textkpi kpi"> Unconfirmed PO : <?php echo $unconfpo ?></div>
        </div>
		<div class="col-xl-12 col-md-6 mt-4 d-flex divkpi">
			<div><img src="/img/KPI 5.png"/ width="100px"></div>
			<div class="font-weight-bold text-info text-uppercase textkpi kpi"> Unapproved PO : <?php echo $unapproved ?></div>
        </div>   
		<div class="col-xl-12 col-md-6 mt-4 d-flex divkpi">
			<div><img src="/img/KPI 2.png"/ width="100px"></div>
			<div class="font-weight-bold text-info text-uppercase textkpi kpi"> Shipment to Confirm : <?php echo $shipment ?></div>
        </div>   
    </div>  

	

@endsection
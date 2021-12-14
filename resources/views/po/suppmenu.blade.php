@extends('layout.layout')

@section('menu_name','Supplier Menu')

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
			width: 200px !important;
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

	<div class="row mindivkpi" style="display:none;text-align: center;color: red;">
		<div class="col-12" style="margin-bottom: 10px">
			 Unconfirmed PO : <?php echo $poc ?> -- Open PO : <?php echo $pod ?>
		</div>
	</div>
	
	<div style="float:left" class="row col-lg-7	 col-md-12">
		@if(str_contains( Session::get('menu_flag'), 'SH01'))	
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/poconf';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/poconf.jpg" width="130" /> PO Confirmation
			</div>	             
		</div>
		@endif
		@if(str_contains( Session::get('menu_flag'), 'SH02'))	
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/sjmt';" style="cursor:pointer;">
	        <div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/pos.png" /> Shipment Registration
			</div>	                 
		</div>
		@endif
      
       
		@if(str_contains( Session::get('menu_flag'), 'SH04'))	
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/sjmtbrw';" style="cursor:pointer;">
	        <div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/pos.png" /> Shipment Browse
			</div>	                 
		</div>
		@endif
		
      
		@if(str_contains( Session::get('menu_flag'), 'SH03'))	
		<div class="col-xl-12 offset-xl-1 col-md-12 mb-4" onclick="location.href='/inputrfqsupp';" style="cursor:pointer;">
	        <div class="font-weight-bold text-info text-uppercase mb-1"><img src="/img/rfqsupp.jpg" width="130" /> RFQ Feedback
			</div>	                 
		</div>        
		@endif
      
     
	</div>

	<div style="float:left" class="col-lg-5">
		<div class="col-xl-12 col-md-12 mt-2 d-flex divkpi">
			<div><img src="/img/KPI 1.png" width="100px"/></div>
			<div class="font-weight-bold text-info text-uppercase textkpi kpi"> Unconfirmed PO : <?php echo $poc ?></div>
        </div>
		<div class="col-xl-12 col-md-12 mt-4 d-flex divkpi">
			<div><img src="/img/KPI 3.png" width="100px" /></div>
			<div class="font-weight-bold text-info text-uppercase textkpi kpi"> Open PO : <?php echo $pod ?> </div>
        </div>
	</div>


@endsection
@extends('layout.layout')

@section('menu_name','Settings')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<style>
		.text-color{
			color:#0000CD !important;
		}

		.card{
			box-shadow: 1px 1px #C0C0C0;
		}

		.usrmain{
			background: #A8E9FF;
		}
		.usrmain:hover{
			background: #9FDEF3;
		}

		.rolecrt{
			background: #73BCD4;
			color: #000000;
		}

		.rolecrt:hover{
			background: #6DB1C8;
		}

		.rolemenu{
			background: #269AE7;
			color:#fff;
		}

		.rolemenu:hover{
			background: #1B81C5;
		}

		.supplier{
			background: #8E26FC;
			color:#fff;
		}
		
		.supplier:hover{
			background: #7D1FE1;
		}

		.item{
			background: #00099C;
			color: #fff;
		}

		.item:hover{
			background: #01087F;
		}

		.itemalert{
			background: #FFFBBC;
		}

		.itemalert:hover{
			background: #DCD9A2;
		}

		.supplieritem{
			background: #FBD446;
		}

		.supplieritem:hover{
			background: #E7C23D;
		}

		.rfq{
			background: #FC9F18;
			color:#fff;
		}

		.rfq:hover{
			background: #E38E14;
		}

		.poapp{
			background: #D75B00;
			color:#fff;
		}

		.poapp:hover{
			background: #BB4F00;
		}

		.site{
			background: #826B03;
			color:#fff;
		}

		.site:hover{
			background: #695702;
		}

		.trsync{
			background: #556B2F;
			color: #fff;
		}

		.trsync:hover{
			background: #3F4E24;
		}

		.invbysupp{
			background: #696969;
			color: #fff;
		}

		.invbysupp:hover{
			background: #515150;
		}

		.suppinv{
			background: #D2B48C;
			color: #000000;
		}

		.suppinv:hover{
			background: #AC9577;
		}

		.deptmaint{
			background: #2ec1ac;
			color : #000000; 
		}

		.deptmaint:hover{
			background: #009e77;
		}

		.rfpapprove{
			background: #6883ba;
			color: #fff;
		}

		.rfpapprove:hover{
			background: #3d3b8e;
		}

		.itemconvmenu{
			background: #33FF99;
		}

		.itemconvmenu:hover{
			background: #00CC66;
		}
		
		.ummastermenu{
			background: #bf9600;
			color: #fff;
		}

		.ummastermenu:hover{
			background: #edbb02;
		}

	</style>


	<!-- Page Heading -->
	<div class="row"> <!-- row 1 -->
      	@if(str_contains( Session::get('menu_flag'), 'ST01'))
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('usermaint') }}';" style="cursor:pointer;">
	      <div class="card h-100 py-2 usrmain">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">User Maintenance</div>
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-user fa-2x" aria-hidden="true"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    @endif
	    @if(str_contains( Session::get('menu_flag'), 'ST04'))
		<div class=" col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('suppinv') }}';" style="cursor:pointer;">
	      <div class="card suppinv h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Supplier Inventory Maint</div> 
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-database fa-2x text-black"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   	</div>
	    @endif
	</div>


	<div class="row"><!-- row 2 -->
        @if(str_contains( Session::get('menu_flag'), 'ST13'))
			<div class="offset-xl-2 col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('deptmaint') }}';" style="cursor:pointer;">
				<div class="card deptmaint h-100 py-2 divbutton">
					<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
						<div class="font-weight-bold text-uppercase mb-1">Department Maintenance</div> 
						</div>
						<div class="col-auto">
						<i class="fa fa-building fa-2x text-black"></i>
						</div>
					</div>
					</div>
				</div>
			</div>
		@endif
        @if(str_contains( Session::get('menu_flag'), 'ST07'))
	   <div class="col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('supprel') }}';" style="cursor:pointer;">
	      <div class="card h-100 py-2 supplieritem divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Supplier-Item Maint</div>
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-link fa-2x"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    @endif
	</div>
	
   	<div class="row"><!-- row 3 -->
        @if(str_contains( Session::get('menu_flag'), 'ST11'))
	    <div class="col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('alertcreate') }}';" style="cursor:pointer;">
	      <div class="card supplier h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Alert Maintenance</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-tags fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    @endif
	    @if(str_contains( Session::get('menu_flag'), 'ST08'))
	   <div class="col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('rfqmaint') }}';" style="cursor:pointer;">
	      <div class="card rfq h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">RFQ / RFP Control</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-cog fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>
	   @endif
	</div>

	<div class="row"><!-- row 4 -->
        @if(str_contains( Session::get('menu_flag'), 'ST02'))
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('rolecreate') }}';" style="cursor:pointer;">
	      <div class="card rolecrt h-100 py-2">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Role Maintenance</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-wrench fa-2x"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>
	   @endif
	   @if(str_contains( Session::get('menu_flag'), 'ST14'))
			<div class=" col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('rfpapprove') }}';" style="cursor:pointer;">
				<div class="card rfpapprove h-100 py-2 divbutton">
					<div class="card-body">
    					<div class="row no-gutters align-items-center">
    						<div class="col mr-2">
    						<div class="font-weight-bold text-uppercase mb-1">RFP Approval Control</div> 
    						</div>
    						<div class="col-auto">
    						<i class="fas fa-check fa-2x text-gray-300"></i>
    						</div>
    					</div>
				    </div>
			    </div>
			</div>
		@endif
	</div>

 	<div class="row"><!-- row 5-->
        @if(str_contains( Session::get('menu_flag'), 'ST03'))
	   <div class="col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('role') }}';" style="cursor:pointer;">
	      <div class="card h-100 py-2 rolemenu">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Role Menu Access</div>	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-sitemap fa-2x text-black"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   </div>
	   @endif
	   @if(str_contains( Session::get('menu_flag'), 'ST09'))
	    <div class="col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('poappcontrol') }}';" style="cursor:pointer;">
	      <div class="card h-100 poapp py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">PO Approval Control</div>
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-thumbs-up fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   	</div>
	   	@endif
	</div>
	
	 

	<div class="row"><!-- row 6 -->
        @if(str_contains( Session::get('menu_flag'), 'ST10'))
		<div class="col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('site') }}';" style="cursor:pointer;">
	      <div class="card site h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Site Control</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fa fa-location-arrow fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   	</div>
	   	@endif
	   	@if(str_contains( Session::get('menu_flag'), 'ST12'))
		<div class=" col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('thistinput') }}';" style="cursor:pointer;">
	      <div class="card trsync h-100 py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Transaction Sync</div> 
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-cloud fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   	</div>
		@endif
	</div>

	<div class="row"><!-- row 7 -->
		@if(str_contains( Session::get('menu_flag'), 'ST05'))
		<div class="offset-xl-2 col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('itmmenu') }}';" style="cursor:pointer;">
	      <div class="card h-100 item py-2 divbutton">
	        <div class="card-body">
	          <div class="row no-gutters align-items-center">
	            <div class="col mr-2">
	              <div class="font-weight-bold text-uppercase mb-1">Item Control</div>
	             
	            </div>
	            <div class="col-auto">
	              <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
	            </div>
	          </div>
	        </div>
	      </div>
	   	</div>
	   	@endif
	</div>

	<div class="row"><!-- row 8 -->
		@if(str_contains( Session::get('menu_flag'), 'ST16'))
			<div class=" col-xl-4 offset-xl-2 col-md-6 mb-1" onclick="location.href='{{ url('ummastermenu') }}';" style="cursor:pointer;">
				<div class="card ummastermenu h-100 py-2 divbutton">
					<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
						<div class="font-weight-bold text-uppercase mb-1">UM Maintenance</div> 
						</div>
						<div class="col-auto">
						<i class="fa fa-balance-scale fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
			</div>
		@endif
	</div>
	
	<div class="row"><!-- row 9 -->
	    @if(str_contains( Session::get('menu_flag'), 'ST15'))
			<div class="offset-xl-2 col-xl-4 col-md-6 mb-1" onclick="location.href='{{ url('itemconvmenu') }}';" style="cursor:pointer;">
				<div class="card itemconvmenu h-100 py-2 divbutton">
					<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
						<div class="font-weight-bold text-uppercase mb-1">Item Conversion Maint</div> 
						</div>
						<div class="col-auto">
						<i class="fa fa-exchange fa-2x text-black"></i>
						</div>
					</div>
					</div>
				</div>
			</div>
		@endif
	</div>
	 
@endsection
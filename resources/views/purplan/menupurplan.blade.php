@extends('layout.layout')

@section('menu_name', 'Purchase Plan')

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
    @if(str_contains( Session::get('menu_flag'), 'PP01'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('purplanbrowse') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0"> 
				<img src="/img/poinfo.jpg" width="160px" height="150px" />
				Purchase Order List
			</div>
		</div>
	</div>
    @endif
    @if(str_contains( Session::get('menu_flag'), 'PP02'))	
	<div class="row col-lg-8 col-xl-7 col-md-12">
		<div class="col-xl-12 offset-xl-1 col-md-12" onclick="location.href='{{ url('purplanview') }}';" style="cursor:pointer;">
			<div class="font-weight-bold text-info text-uppercase mb-0">
				<img src="/img/rqfapp.jpg" width="160px" height="150px" /> 
				Purchase Order Create
			</div>
		</div>
	</div>
    @endif
@endsection
@extends('layout.layout')
@section('menu_name','Loccation Browse')
@section('content')

<style type="text/css">
	tbody{
        font-size: 12px;

    }

    h1{
      color: black !important;
    }

    thead{
        background-color: #4e73df;
        text-align: left;
        color:white !important;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:14px;
        font-weight: 600;
    }


    .bt-action{
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
  
    .bt-confirm{
      float: right !important;
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
	
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }
 
       .bt-confirm1{
       margin-right: 5px;
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }

    @media only screen and (max-width: 800px) {
        
    /* Force table to not be like tables anymore */
    #dataTable table, 
    #dataTable thead, 
    #dataTable tbody, 
    #dataTable th, 
    #dataTable td, 
    #dataTable tr { 
        display: block; 
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    #dataTable thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    #dataTable tr { border: 1px solid #ccc; }

    #dataTable td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 40%; 
        white-space: normal;
        text-align:left;
    }

    #dataTable td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align:left;
        font-weight: bold;
    }

    /*
    Label the data
    */
    #dataTable td:before { 
        content: attr(data-title); 
        vertical-align: top;
        padding: 6px 0px 0px 0px;
    }
}   
</style>

	<!-- Page Heading -->
  	<div class="d-sm-flex align-items-center justify-content-between mb-4">
    	
		
		<a href="" class='editdata' data-toggle='modal' data-target='#myModal' 
                        ><i class="fas fa-info-circle"></i></a>
  	</div>
      
    <!--<a href="/sjcrt" class="btn btn-primary mb-1">Create Surat Jalan</a> -->
<div class="card shadow mb-4">

	<div class="card shadow mb-4">
	    <h5>Site: 10-100</h5> 		
		@foreach($data as $data)
		<div class="form-group row">
			<div class='container'>
				<p>{{$data->supp_id}}</p>
			</div>
			@foreach($podedt as $p)
				@if($data->supp_id == $p->xpo_vend)
				<div class="col-md-1 offset-md-1 col-lg-1 ">		
					<img height="50px" width="40px" src="http://127.0.0.1:8000/img/location/loca.gif">{{ $p->xpo_vend }}</img>   				
				</div>
				@endif		
			@endforeach
		</div>
		@endforeach
</div>	
            

	
	
@endsection
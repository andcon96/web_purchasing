@extends('layout.layout')

@section('menu_name','Item Browse')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<style type="text/css">
    tbody{
        font-size: 14px;

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
        font-size:22px;
        font-weight: 600;
    }


    .bt-action{
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
  
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
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
	@if(session()->has('updated'))
	    <div class="alert alert-success">
	        {{ session()->get('updated') }}
	    </div>
	@endif
	@if(session()->has('deleted'))
	    <div class="alert alert-success">
	        {{ session()->get('deleted') }}
	    </div>
	@endif
 	<div class="card shadow mb-4">
	    <div class="card-body">
      
	      <div class="table-responsive col-lg-12 col-md-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
	          <thead>
	            <tr>
				   <th>Item Number</th>
				   <th>Description</th>
			       <th>Um</th>  
				   <th>Prod Line</th>  
				   <th>Item Type</th> 
				   <th>P/M</th> 
				   <th>Safety Stock</th> 
			      
			    </tr>
		       </thead>
			  <tbody>					
				@foreach ($item as $show)
					<tr>
						<td>{{ $show->xitem_part }}</td>
                		<td>{{ $show->xitem_desc }}</td>
						<td>{{ $show->xitem_um }}</td>
						<td>{{ $show->xitem_prod_line }}</td>
						<td>{{ $show->xitem_type }}</td>
						<td>{{ $show->xitem_pm }}</td>
						<td>{{ $show->xitem_sfty_stk }}</td>                		
					</tr>
				@endforeach			                 
	          </tbody>
	        </table>
			 {{ $item->render() }}
	      </div>
	    </div>
	</div>



@endsection
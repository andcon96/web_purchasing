@extends('layout.layout')

@section('menu_name','Domain Control')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Domain Control</li>
</ol>
@endsection

@section('content')
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
        font-size:18px;
        font-weight: 600;
        padding: 5px 10px 5px 10px;
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
    <br>
 <div class="card shadow mb-4">
      
            <div class="card-body">
              <div class="table-responsive"> 
                 <form action="/domain" method="post">  
					    {{ csrf_field() }}
                     @if($data == 'yes')           
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Domain</label>
                           <input type="text" name="dom" class="form-control" required="" value={{ old('dom') }} >
                        </div>
					    	</div>
					    	
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Description</label>
                           <input type="textarea" name="desc" class="form-control" required="" >
                        </div>
					    	</div>
                     @else
                         @foreach($dom as $show)
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Domain</label>
                           <input type="text" name="dom" class="form-control" required="" value= {{ $show->xdomain_code }}>
                        </div>
					    	</div>
					    	
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Description</label>
                           <input type="textarea" name="desc" class="form-control" required="" value= {{ $show->xdomain_desc }}>
                        </div>
					    	</div>
                   @endforeach 
                    @endif
                   
					    	<div class="form-group">
					    		<button type="submit" class="btn btn-danger btn-sm">Save</button>
					    	</div>

					    </form> 
                  
               
              </div>
            </div>
          </div>

       



	
   
@endsection
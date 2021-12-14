@extends('layout.layout')

@section('menu_name','Item RFQ Control')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Item RFQ Control</li>
</ol>
@endsection

@section('content')

<style type="text/css">
	tbody{
		font-size: 14px;
	}

	thead{
		background-color: #F0F8FF;
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
    padding-top: 10px;
}

/*
Label the data
*/
#dataTable td:before { 
	content: attr(data-title); 
	vertical-align: top;
}
}   

</style>

	<!-- Page Heading -->
	
@if(session()->has('updated'))
   <div class="alert alert-success">
      {{ session()->get('updated') }}
   </div>
@endif
   
<form action="/itmreqsave" method="post">  
   {{ csrf_field() }}                           
   <div class="row">
      <div class="col-md-6">
         <label>Item No.</label>
         <input type="text" name="part" class="form-control" autocomplete="off">                          
      </div>

                     
      <div class="col-md-6">
         <label>Item Type</label>
         <input type="text" name="type" class="form-control" autocomplete="off">
      </div>
   </div>    
   <div class="row">
     
      <div class="col-md-6">
         <label>Design Group</label>
         <input type="text" name="dsgn" class="form-control" autocomplete="off">
      </div>

      <div class="col-md-6">
         <label>Promo Group</label>
         <input type="text" name="promo" class="form-control" autocomplete="off">
      </div>
   </div>  
   <div class="row">
      <div class="col-md-6">
         <label>Group</label>
         <input type="text" name="grp" class="form-control" autocomplete="off">
      </div>

      <div class="col-md-6">
         <label>Product Line</label>
         <input type="text" name="line" class="form-control" autocomplete="off">
      </div>
   </div>     
   </br>
   
   
   
   
   <div class="row">
      <div class="col-lg-2">
         <button type="submit" class="btn bt-action" ><b>Save</b></button>
     </div>
   </div>
</form>
                               

           
           
	
	
@endsection
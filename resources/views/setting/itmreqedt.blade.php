@extends('layout.layout')
@section('menu_name','Item Control Edit')
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

 @foreach($itm as $show)  
<form action="/itmrequpd" method="post">  
   {{ csrf_field() }}                           
   <div class="row">
      <div class="col-md-4">
         <label>Item Number</label>
         <input type="text" name="part" class="form-control" value= '{{$show->xitmreq_part}}'>                          
      </div>

      <div class="col-md-4">
         <label>Item Type</label>
         <input type="text" name="type" class="form-control" value= '{{$show->xitmreq_type}}'>
      </div>

      <div class="col-md-4">
         <label>Design Group</label>
         <input type="text" name="dsgn" class="form-control" value= '{{$show->xitmreq_design}}'>
      </div>
   </div>  
   <div class="row">
      <div class="col-md-4">
         <label>Promo Group</label>
         <input type="text" name="promo" class="form-control" value= '{{$show->xitmreq_promo}}'>
      </div>
 
      <div class="col-md-4">
         <label>Group</label>
         <input type="text" name="grp" class="form-control" value= '{{$show->xitmreq_group}}'>
      </div>

      <div class="col-md-4">
         <label>Product Line</label>
         <input type="text" name="line" class="form-control" value= '{{$show->xitmreq_prod_line}}'>
      </div>
   </div>     
   </br>
   
   <div class="col-lg-4 ml-0">
      <input type="hidden" name="id"  class="form-control" value= '{{ $show->xitmreq_id }} ' > 
      <button type="submit" class="btn bt-action ml-0"><b>Save</b></button>
   </div>
</form>
 @endforeach                              

           
           
	
	
@endsection
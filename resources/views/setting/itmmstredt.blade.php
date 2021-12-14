@extends('layout.layout')

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
<form action="/itmmstrupd" method="post">  
   {{ csrf_field() }}                           
   <div class="row">
      <div class="col-md-4">
         <label>Item Number</label>
         <input type="text" name="part" class="form-control" value= '{{$show->xitem_part}}' readonly="true">                          
      </div>

      <div class="col-md-8">
         <label>Description</label>
         <input type="text" name="desc" class="form-control" value= '{{$show->xitem_desc}}' readonly="true">
      </div>  
      
</div> 
</br>
<div class="form-group">
                  <h5><center><strong>Alert Exp Days</strong></center></h5>
                  <hr>
                </div>

               <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays1" type="text" class="form-control" placeholder="Almost Safety Stock %"name="sfty" value="{{$show->xitem_sfty}}" autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail1" type="text" class="form-control" placeholder="Email"name="sftyemail1" value="{{$show->xitem_sfty_email}}"  autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays1" type="text" class="form-control" placeholder="Days"name="alertdays1" value="{{$show->xitem_day1}}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail1" type="text" class="form-control" placeholder="Email"name="alertemail1" value="{{$show->xitem_day_email1}}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays2" type="text" class="form-control" placeholder="Days"name="alertdays2" value="{{$show->xitem_day2}}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail2" type="text" class="form-control" placeholder="Email"name="alertemail2" value="{{$show->xitem_day_email2}}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays3" type="text" class="form-control" placeholder="Days"name="alertdays3" value="{{$show->xitem_day3}}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail3" type="text" class="form-control" placeholder="Email"name="alertemail3" value="{{$show->xitem_day_email3}}"  autofocus>
                    </div>
                </div>
                </br>
   
   <div class="form-group">
      <div class="col-lg-2 offset-lg-2">
         <div class="card border shadow">

         <button type="submit" ><h5><b>Save</h5></b></button>
         </div>
     </div>
   </div>
</form>
 @endforeach                              

           
           
	
	
@endsection
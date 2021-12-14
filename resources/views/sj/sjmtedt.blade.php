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
  	<div class="d-sm-flex align-items-center justify-content-between mb-4">
    	<h1 class="h3 mb-0 text-gray-800">Shipper Maintenance</h1>
  	</div>
   

             <form action="/sjmtupd" method="post">  
               {{ csrf_field() }}           
                   
                   @foreach($sjmt as $p)
                    <div class="row">
                        <div class="col-md-2">
                           <label>ID</label>
                           <input type="text" name="id" class="form-control" value={{ $p->xsj_id }} readonly="true">                           
                        </div>
					 
					
                        <div class="col-md-2">
                           <label>Item Number</label>
                           <input type="text" name="part" class="form-control" value={{ $p->xsj_part }} readonly="true">
                        </div>
				    
                    
                        <div class="col-md-5">
                           <label>Description</label>
                           <input type="text" name="desc" class="form-control" value={{ $p->xsj_desc }} readonly="true">
                        </div>
                   	</div> 
              <div class="row">      
					  <div class="col-md-2">
                      <label>Qty Open</label>
                      <input type="text" name="opn" class="form-control" value={{ $p->xsj_qty_open }} readonly="true">
                  </div>                   
                  <div class="col-md-2">
                      <label>Qty Order</label>
                      <input type="text" name="ord" class="form-control" value={{ $p->xsj_qty_ord }} readonly="true">
                  </div>                     						
						<div class="col-md-2">
                      <label>Qty Ship</label>
                      <input type="text" name="ship" class="form-control" value={{ $p->xsj_qty_ship }} >
                  </div>							                      
                  <div class="col-md-3">
                       <label>Lot</label>
                       <input type="text" name="lot" class="form-control" value={{ $p->xsj_lot }} >
                  </div>
						 <div class="col-md-4">
                       <label>Reference</label>
                       <input type="text" name="ref" class="form-control" value={{ $p->xsj_ref }}>
                  </div>
					</div>  
  
					
					 
                     <input type="hidden" name="loc" class="form-control" value={{ $p->xsj_loc }} >
                     <input disable type="hidden" name="nbr" value= {{ $p->xsj_po_nbr }}>  
                     <input disable type="hidden" name="line" value= {{ $p->xsj_line }}>                           
                     <input disable type="hidden" name="supp" value= "sup01">                        
                     <input disable type="hidden" name="conf" value= "Created">   
					 <input type="hidden" name="supp" value= {{ Auth::user()->supp_id }} >
                     <input type="hidden" name="ship1"  value={{ $p->xsj_qty_ship }} >
					 <input type="hidden" name="qship1"  value={{ $p->xpod_qty_ship }} >
					 </br>
                     <div class="form-group">
					    		<button type="submit" class="btn btn-danger btn-sm">Save</button>
					    	</div>
                     
                     </form>
                               
            @endforeach 

            
           
	
	
@endsection
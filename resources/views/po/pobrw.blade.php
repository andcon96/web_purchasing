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
    	<h1 class="h3 mb-0 text-gray-800">Purchase Order Browse</h1>
  	</div>
<div class="card shadow mb-4">
           
            
            
            <div class="card-body">
              <div class="table-responsive">
              <form action="/pobrwcari" method="GET">
              <div class="form-group">                   
                  <div class="col-md-6">
                      <input type="text" name="cari" placeholder="PO Number .." value="{{ old('cari') }}"> </br> </br>                                   
                      <input type="text" name="item" placeholder="Item Number .." value="{{ old('item') }}"> </br> </br> 
                      <input type="submit" value="Search"> </br>
                   </div>
				  </div>
                    

               
            </form>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="1">
                  <thead>
                    <tr>
                      <th>PO Number</th>
                      <th>Supplier</th>
                      <th>Order Date</th>
                      <th>Due Date</th>
                      <th>Line</th>
                      <th>Item Number</th>
                      <th>Description</th>
                      <th>Qty Order</th>
                      <th>Qty Receipt</th>
                      <th>Price</th>
                      <th>Status</th>
                      <th>Option</th>
                    </tr>
                  </thead>                                     
                  </tbody>
                   <tbody>                  
                   @foreach($pobrw as $show)
                    <tr>
                      <td>{{ $show->xpo_nbr }}</td> 
                      <td>{{ $show->xpo_vend }}</td> 
                      <td>{{ $show->xpo_ord_date }}</td>
                      <td>{{ $show->xpo_due_date }}</td>
                      <td>{{ $show->xpod_line }}</td>
                      <td>{{ $show->xpod_part }}</td>
                      <td>{{ $show->xpod_desc }}</td>
                      <td>{{ $show->xpod_qty_ord }}</td>
                      <td>{{ $show->xpod_qty_rcvd }}</td>
                      <td>{{ $show->xpod_price }}</td>
                      <td>{{ $show->xpod_status }}</td>
                      <td>
                       <form action="/poddet" method="GET">
                        <input disable type="hidden" name="cari" value= {{ $show->xpo_nbr }}>                       
                        <input type="submit" name="submit" value="VIEW">                                                
                       </form>                        
                     </td>
                    </tr>
                   @endforeach  
                   </tbody>
                </table>
                
             
              </div>
            </div>
          </div>
          <br/>
 
   {{ $pobrw->links() }}
	
	
@endsection
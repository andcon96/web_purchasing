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

	
 @if(session()->has('updated'))
            <div class="alert alert-success">
                {{ session()->get('updated') }}
            </div>
        @endif
            
            <div class="card-body">
              <div class="table-responsive">
               @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                    
                   @foreach($podedt as $p)
                    <form action="/podedtupd" method="post">
                           {{ csrf_field() }}
                           
                           <div class="form-group">
                        <div class="col-md-6">
                           <label>Order</label>
                          <input type="text" class="form-control" name="nbr" disabled  value={{ $p->xpod_nbr }} >
                        </div>
					    	</div>
					    	<div class="form-group">
                        <div class="col-md-6">
                           <label>Item Number</label>
                           <input type="text" class="form-control" name="part" disabled  value={{ $p->xpod_part }} >
                        </div>
					    	</div>
							
					<div class="form-group">
                        <div class="col-md-6">
                           <label>Qty Order</label>
                           <input type="decimal" name="ord" class="form-control" required="" readonly="true" value={{ $p->xpod_qty_ord }}>
                        </div>
					    	</div>
							
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Promise Qty</label>
                           <input type="decimal" name="qty" class="form-control" required="" value={{ $p->xpod_qty_prom }}>
                        </div>
					    	</div>
                     
                      <div class="form-group">
                        <div class="col-md-6">
                           <label>Promise Due Date</label>
                           <input type="date" name="due" class="form-control" required="" value={{ $p->xpod_due_date }}>
                        </div>
					    	</div>
                     <input disable type="hidden" name="nbr" value= {{ $p->xpod_nbr }}>  
                     <input disable type="hidden" name="line" value= {{ $p->xpod_line }}>
                       
                     <input disable type="hidden" name="domain" value= {{ $p->xpod_domain }}>                      
					    	<div class="form-group">
					    		<button type="submit" class="btn btn-danger btn-sm">Confim</button>
					    	</div>                         
                          
                </form>  
                   
                   @endforeach  
                   
                                
                  
               
              </div>
            </div>
 
          

	
	
@endsection
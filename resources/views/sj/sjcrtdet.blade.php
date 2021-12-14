@extends('layout.layout')
@section('menu_name','Shipper Maintenance')
@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
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
  
       <div class="table-responsive col-lg-12 col-md-12 tag-container">
       
   <h3>Detail</h3>	  
   <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
      <thead>
         <tr>
            <th>Order</th>
            <th>Item Number</th>
            <th>Descrtiption</th>
            <th>Line</th>
            <th>Due Date</th>
            <th>Qty Order</th>
            <th>qty open</th>  
            <th>qty ship</th>
            <th>Option</th>
         </tr>
      </thead>                                                    
      <tbody>                    
         @foreach($poddet as $p)
         <tr>
            <td>{{ $p->xpod_nbr }}</td> 
            <td>{{ $p->xpod_part }}</td>                       
            <td>{{ $p->xpod_desc}}</td>
            <td>{{ $p->xpod_line }}</td>
            <td>{{ $p->xpod_due_date}}</td>
            <td>{{ $p->xpod_qty_ord }}</td>
            <td>{{ $p->xpod_qty_open }}</td>
            <td>{{ $p->xpod_qty_shipx }}</td>
            <td>
              <a href="" class="deleteUser" 
                        data-toggle="modal" 
                        data-target="#deleteModal" 
                        data-id="{{ $id }}" 
                        data-nbr="{{$p->xpod_nbr}}"
                        data-supp="{{$p->xpo_vend}}" 
                        data-part="{{$p->xpod_part}}" 
                        data-shipx="{{$p->xpod_qty_open}}" 
                        data-shipx1="{{$p->xpod_qty_shipx}}" 
                        data-line="{{$p->xpod_line}}"
                        data-due="{{$p->xpod_due_date}}"
                        data-desc="{{$p->xpod_desc}}"
                        data-ord="{{$p->xpod_qty_ord}}"
                        data-opn="{{$p->xpod_qty_open}}"
                        data-ship="{{$p->xpod_qty_ship}}"
                        data-lot="" 
                        data-ref=""
                        
                        > 
                     <i class="fas fa-edit"></i></a>
  
            </td>                              
         </tr>
         @endforeach  
      </tbody> 
   </table>    
</div>      

<form class="form-horizontal" role="form" method="get" action="/sjcrt">
   <input id="id" type="hidden" class="form-control" name="id" value={{ $id }} autofocus>
   <input id="nbr" type="hidden" class="form-control" name="nbr" value={{ $nbr }} autofocus>
   <div class="form-group">
		<div class="offset-md-3 offset-lg-0 offset-xl-6 offset-sm-0 offset-xs-0">
    		<button type="submit" class="btn btn-danger btn-sm">Save</button>
		</div>
  	</div>
</form>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Shipper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/sjctsave" method="post">

        {{ csrf_field() }}

        <div class="modal-body">
        
             <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label ">{{ __('Order') }}</label>
                     <input id="t_nbr" type="text" class="form-control" name="t_nbr" 
                    value="" readonly="true" autofocus>
             </div>

            <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label ">{{ __('part') }}</label>
                     <input id="t_part" type="text" class="form-control" name="t_part" 
                    value="" readonly="true" autofocus>
                </div>
              
                <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label">{{ __('Qty Ship') }}</label>
                     <input id="t_shipx" type="text" class="form-control" name="t_shipx" 
                    value="" autofocus>
                </div>
                
                <div class="form-group row">
                    <label for="supname" class="col-md-5 col-form-label ">{{ __('Lot Number') }}</label>
                     <input id="t_lot" type="text" class="form-control" name="t_lot" 
                    value="" autofocus autocomplete="off">
                </div>
                <div class="form-group row">
                    <label for="supname" class="col-md-5 col-form-label ">{{ __('Reference') }}</label>
                     <input id="t_ref" type="text" class="form-control" name="t_ref" 
                    value="" autofocus autocomplete="off">
                </div>      
        </div>
        
          <input id="t_id" type="hidden" class="form-control" name="t_id" value="" autofocus>
          <input id="t_line" type="hidden" class="form-control" name="t_line" value="" autofocus>
          <input id="t_supp" type="hidden" class="form-control" name="t_supp" value="" autofocus>
          <input id="t_due" type="hidden" class="form-control" name="t_due" value="" autofocus>
          <input id="t_desc" type="hidden" class="form-control" name="t_desc" value="" autofocus>
          <input id="t_ord" type="hidden" class="form-control" name="t_ord" value="" autofocus>
          <input id="t_opn" type="hidden" class="form-control" name="t_opn" value="" autofocus>
          <input id="t_ship" type="hidden" class="form-control" name="t_ship" value="" autofocus>
           <input id="t_shipx1" type="text" class="form-control" name="t_shipx1" value="" autofocus>
      
          <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id='d_btnclose' data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger bt-action" id='d_btnconf'>Confirm</button>
          <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
              <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>

      </form>
    </div>
  </div>
</div>  

<script type="text/javascript">
 $(document).on('click','.deleteUser',function(){
       var uid = $(this).data('id');
       var supp = $(this).data('supp');
       var nbr = $(this).data('nbr');
       var shipx = $(this).data('shipx');
       var shipx1 = $(this).data('shipx1');
       var lot = $(this).data('lot');
       var line = $(this).data('line');
       var ref = $(this).data('ref');
       var part = $(this).data('part');
       var due = $(this).data('due');
       var desc = $(this).data('desc');
       var ord = $(this).data('ord');
       var opn = $(this).data('opn');
       var ship = $(this).data('ship');
       
      	
       document.getElementById('t_id').value = uid;     
       document.getElementById('t_supp').value = supp;
       document.getElementById('t_nbr').value = nbr;
       document.getElementById('t_shipx').value = shipx;
       document.getElementById('t_shipx1').value = shipx1;
       document.getElementById('t_lot').value = lot;
       document.getElementById('t_line').value = line;
       document.getElementById('t_ref').value = ref;
       document.getElementById('t_part').value = part;
       document.getElementById('t_due').value = due;
       document.getElementById('t_desc').value = desc;
       document.getElementById('t_ord').value = ord;
       document.getElementById('t_opn').value = opn;
       document.getElementById('t_ship').value = ship;
    });



</script>
           
	
	
@endsection
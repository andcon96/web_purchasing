@extends('layout.layout')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<style type="text/css">
	.center   { text-align: center;}
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

	<!-- Page Heading -->

   

             <form class="form-horizontal" role="form" method="get" action="/sjcrtdet">
					    {{ csrf_field() }}
                     <div class="form-group">
                        <div class="col-md-6">
                           <label>Shipper ID</label>
                           <input type="text" name="id" class="form-control" required="" value="{{$id}}" autocomplete="off">
                        </div>
					    	</div>					    	
                     <div class="form-group">
                        <div class="col-md-6">
                        <label>Purchase Order</label>
                        <select name="xpod_nbr" id="xpod_nbr" class="form-control input-lg dynamic" data-dependent="xpod_line">
                          <option value="">Select Order</option>
                          @foreach($country_list as $country)
                          <option value="{{ $country->xpo_nbr}}">{{ $country->xpo_nbr }}</option>
                          @endforeach
                         </select>                                                                                       
                        </div>
					    	</div>
                     
					 
					 
					    	<div class="form-group">
								<div class="col-md-6">
					    		<button type="submit" class="btn btn-danger btn-sm">search</button>
								</div>
					    	</div>                    
                    
					    </form> 
                   
                   <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Supplier</th>
                      <th>Order</th>
                      <th>Line</th>
                      <th>Item Number</th>
                      <th>Description</th>
                      <th>Qty Order</th>
                      <th>Qty Ship</th>
                     
                      <th colspan="2">Option</th>
                    </tr>
                  </thead>                                     
                  </tbody>
                   <tbody>                  
                   @foreach($sjmt as $show)
                    <tr>
                      <td>{{ $show->xsj_id }}</td> 
                      <td>{{ $show->xsj_supp }}</td>
                      <td>{{ $show->xsj_po_nbr }}</td>
                      <td>{{ $show->xsj_line }}</td>
                      <td>{{ $show->xsj_part }}</td>
                      <td>{{ $show->xsj_desc }}</td>
                      <td>{{ $show->xsj_qty_ord }}</td>
                      <td>{{ $show->xsj_qty_ship }}</td>
                                                                                  
                       <td>
                         <form action="/sjmtedt" method="get">
                           {{ csrf_field() }}  
                             <input disable type="hidden" name="id" value= {{ $show->xsj_id }} >
                             <input disable type="hidden" name="nbr" value= {{ $show->xsj_po_nbr }} >
                             <input disable type="hidden" name="line" value= {{ $show->xsj_line }} >
                             <input disable type="hidden" name="lot" value= {{ $show->xsj_lot }} >
                           <input type="submit" value="Edit">
                         </form>                       
                                                
                     </td>
                     <td>
					 <button type="submit"> 
						<a href="" class="deleteUser" 
                        data-toggle="modal" 
                        data-target="#deleteModal" 
                        data-id="{{$show->xsj_id}}" 
                        data-supp="{{$show->xsj_supp}}" 
                        data-line="{{$show->xsj_line}}"
                        data-lot="{{$show->xsj_lot}}"
						      data-qship="{{$show->xsj_qty_ship}}"
                        data-shp="{{$show->xpod_qty_ship}}"
                        data-opn="{{$show->xsj_qty_open}}"
                        data-nbr="{{$show->xsj_po_nbr}}"
                        > 
						Delete
                     </a>
					 </button>
                      </td>
                    </tr>
                   @endforeach  
                   </tbody>
                </table>
                
             
              </div>
            </div>
                   
        <div class="row">
         <div class="col-lg-2 offset-lg-3">
            <div class="card border shadow">
               <form class="form-horizontal" role="form" method="get" action="/sjmtcancel">	
                  <input disable type="hidden" name="id" value= {{ $id }} >               
                  	<input type="submit" class="col-lg-12" value="Cancel" ></button>
               </form>
            </div>
         </div>  
         <div class="col-lg-2 offset-lg-2">
            <div class="card border shadow">
               <form class="form-horizontal" role="form" method="get" action="/sjmt">           
              	<input type="submit" class="col-lg-12" value="Confirm" ></button>			
            </form>
         </div>
         </div>
       </div>
       
	
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Shipper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/sjmtdeledt" method="POST">

        {{ csrf_field() }}

        <div class="modal-body">

            <input type="hidden" name="delete_id" id="delete_id" value="">
            <input type="hidden" name="t_supp" id="t_supp" value="">
            <input type="hidden" name="t_line" id="t_line" value="">
            <input type="hidden" name="t_lot"  id="t_lot" value="">
            <input type="hidden" name="t_qship" id="t_qship" value= "" > 							 
            <input type="hidden" name="t_shp"  id="t_shp" value= "">
			<input type="hidden" name="t_opn" id="t_opn" value= "">
			<input type="hidden" name="t_nbr" id="t_nbr" value= "">
           

            <div class="container">
              <div class="row">
                Delete for Shipper : 
                &nbsp; <strong><a name="id" id="id"></a></strong> 
                 &nbsp;?  
              </div>
            </div>
            
        </div>
      
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
		
		
  <script>
   $(document).on('click','.editUser',function(){ // Click to only happen on announce links   
     //alert('tst');
     var supp = $(this).data('supp');
     document.getElementById("nb").value = supp;
     });
     
$(document).ready(function(){
        $("#xpod_nbr").select2({});
        $("#c_nbr").select2({});
        
 $('.dynamic').change(function(){
    
  if($(this).val() != '')
  {
   var select = $(this).attr("id");
   var value = $(this).val();
   var dependent = $(this).data('dependent');
   
   var _token = $('input[name="_token"]').val();
   
 
   $.ajax({
    url:"{{ route('dynamicdependent.fetch') }}",
    method:"POST",
    data:{select:select, value:value, _token:_token, dependent:dependent},
    success:function(result)
    
    {
       console.log(result);
     $('#'+dependent).html(result);
    }

   })
  }
 });

 $('#xpod_nbr').change(function(){
  $('#xpod_line').val('');

 });


 

});

$(document).on('click','.deleteUser',function(){
       var uid = $(this).data('id');
       var tid = $(this).data('id');
       var lot = $(this).data('lot');
       var supp = $(this).data('supp');
       var line = $(this).data('line');
       var qship = $(this).data('qship');
       var shp  = $(this).data('shp')
       var opn = $(this).data('opn')
       var nbr = $(this).data('nbr')
                        
		
		
       document.getElementById('delete_id').value = uid;       
       document.getElementById('t_lot').value = lot;
       document.getElementById('t_line').value = line;
       document.getElementById('t_supp').value = supp;
       document.getElementById('id').innerHTML = tid;
       document.getElementById('t_qship').value = qship;
       document.getElementById('t_shp').value = shp;
       document.getElementById('t_opn').value= opn;
       document.getElementById('t_nbr').value = nbr;



    });

</script>	
	 
           
	
	
@endsection
@section('script')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>

@endsection
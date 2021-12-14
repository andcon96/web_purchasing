@extends('layout.layout')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style type="text/css">
    tbody{
        font-size: 14px;
    }

    thead{
        background-color: #F0F8FF;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        text-align: center;
        color:#000000;
    }

    #dataTable th{
        vertical-align: middle;
    }

    .bt-action{
      font-size: 12px;
      width: 80px;
    }

    @media only screen and (max-width: 800px) {
        
    /* Force table to not be like tables anymore */
    #dataTable table, 
    #dataTable thead, 
    #dataTable tbody, 
    #dataTable th, 
    #dataTable td, 
    #dataTable tr { 
        vertical-align: middle;
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

<script>
      $( function() {
        $( "#datefrom" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
        $( "#dateto" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
</script>

@if(session('error'))
	<div class="alert alert-danger" id="getError">
		{{ session()->get('error') }}
	</div>
@endif

@if(session()->has('updated'))
    <div class="alert alert-success">
        {{ session()->get('updated') }}
    </div>
@endif

<div class="card shadow mb-4">
        <div class="card-header py-3">
         	PO Browse    
        </div>
        <div class="card-body">
	        <!--Search-->
	        <div class="form-group row">
                <label for="ponbr" class="col-md-2 col-form-label text-md-right">{{ __('PO Number') }}</label>
                <div class="col-md-3">
                    <input id="ponbr" type="text" class="form-control" name="ponbr" 
                    value="" autofocus>
                </div>
          </div>
	        <div class="form-group row">
                <label for="itemcode" class="col-md-2 col-form-label text-md-right">{{ __('Item Code') }}</label>
                <div class="col-md-3">
                    <input id="itemcode" type="text" class="form-control" name="itemcode" 
                    value="" autofocus>
                </div>
          </div>
          @if(!Session::get('supp_code'))
	        <div class="form-group row">
                <label for="supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
                <div class="col-md-3">
                    <input id="supplier" type="text" class="form-control" name="supplier" 
                    value="" autofocus>
                </div>
          </div>
          @endif
	        <div class="form-group row">
                <label for="datefrom" class="col-md-2 col-form-label text-md-right">{{ __('Due From') }}</label>
                <div class="col-md-4">
                    <!--
                    <input id="datefrom" type="date" class="form-control" name="datefrom" 
                    value="" autofocus>
                    -->
                    <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                            required autofocus>
                </div>
                <label for="dateto" class="col-md-1 col-form-label text-md-right">{{ __('Due To') }}</label>
                <div class="col-md-4">
                    <!--
                    <input id="dateto" type="date" class="form-control" name="dateto" 
                    value="" autofocus>
                    -->
                    <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                            required autofocus>
                </div>
          </div>
	        <div class="form-group row">
                <label for="status" class="col-md-2 col-form-label text-md-right">{{ __('Status') }}</label>
                <div class="col-md-3">
                    <input id="status" type="text" class="form-control" name="status" 
                    value="" autofocus>
                </div>
          </div>
          <div class="offset-md-2">
            <input type="button" class="btn btn-info" 
            id="btnsearch" value="Search" />
          </div>
    	  <br>
        <!--Table-->

        @include('po.tablepo')
        <br>
        </div>
</div>
        
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Bid</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form>

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="m_ponbr" class="col-md-4 col-form-label text-md-right">{{ __('PO Number') }}</label>
                    <div class="col-md-5">
                        <input id="m_ponbr" type="text" class="form-control" name="m_ponbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_supplier" class="col-md-4 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-5">
                        <input id="m_supplier" type="text" class="form-control" name="m_supplier" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_itemcode" class="col-md-4 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-5">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_itemdesc" class="col-md-4 col-form-label text-md-right">{{ __('Item Desc') }}</label>
                    <div class="col-md-5">
                        <input id="m_itemdesc" type="text" class="form-control" name="m_itemdesc" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyord" class="col-md-4 col-form-label text-md-right">{{ __('Qty Order') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyord" type="text" class="form-control" name="m_qtyord" value="" readonly autofocus>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label for="m_qtyship" class="col-md-4 col-form-label text-md-right">{{ __('Qty Ship') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyship" type="text" class="form-control" name="m_qtyship" value="" readonly autofocus>
                    </div>
                </div>
                -->
                <div class="form-group row">
                    <label for="m_qtyrec" class="col-md-4 col-form-label text-md-right">{{ __('Qty Rec') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyrec" type="text" class="form-control" name="m_qtyrec" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_um" class="col-md-4 col-form-label text-md-right">{{ __('UM') }}</label>
                    <div class="col-md-5">
                        <input id="m_um" type="text" class="form-control" name="m_um" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_duedate" class="col-md-4 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-5">
                        <input id="m_duedate" type="text" class="form-control" name="m_duedate" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                    <div class="col-md-5">
                        <input id="m_status" type="text" class="form-control" name="m_status" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_price" class="col-md-4 col-form-label text-md-right">{{ __('Price') }}</label>
                    <div class="col-md-5">
                        <input id="m_price" type="text" class="form-control" name="m_price" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_lastconf" class="col-md-4 col-form-label text-md-right">{{ __('Last Confirmed') }}</label>
                    <div class="col-md-5">
                        <input id="m_lastconf" type="text" class="form-control" name="m_lastconf" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_totconf" class="col-md-4 col-form-label text-md-right">{{ __('Total Confirmed') }}</label>
                    <div class="col-md-5">
                        <input id="m_totconf" type="text" class="form-control" name="m_totconf" value="" readonly autofocus>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" data-dismiss="modal">Close</button>
            </div>
       </form>

    </div>
  </div>
</div>


<script>

  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];

      //console.log(page);
      getData(page);

  });

  function getData(page){
    $.ajax({
        url: '/po/fetch_data?page='+ page,
        type: "get",
        datatype: "html" 
    }).done(function(data){
          console.log('Page = '+ page);

          $(".tag-container").empty().html(data);

    }).fail(function(jqXHR, ajaxOptions, thrownError){
          alert('No response from server');
    });
  }


	$('#btnsearch').on('click',function(){
      var ponbr = document.getElementById("ponbr").value;
      var itemcode = document.getElementById("itemcode").value;
	  
	  var check_supplier = document.getElementById("supplier");
      var supplier = "";
      if(check_supplier){
          supplier = check_supplier.value;
      }

      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      var status = document.getElementById("status").value;

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("posearch") }}",
          data:{
            nbr : ponbr,
            code : itemcode,
            supp : supplier,
            status : status,
            datefrom : datefrom,
            dateto : dateto,
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
          }
      });

  });


  $(document).on('click','.editUser',function(){ // Click to only happen on announce links
   
   //alert('123');
   var ponbr = $(this).data('nbr');
   var line = $(this).data('line');
   var desc = $(this).data('desc');
   var lastconf = $(this).data('lastconf');
   var qtyord = $(this).data('qtyord');
   var um = $(this).data('um');
   var price = $(this).data('price');
   var supplier = $(this).data('supplier');
   var part = $(this).data('part');
   var status = $(this).data('status');
   var totconf = $(this).data('totconf');
   var qtyrec = $(this).data('qtyrec');
   var duedate = $(this).data('duedate');

   document.getElementById("m_ponbr").value = ponbr;
   //document.getElementById("m_line").value = line;
   document.getElementById("m_itemdesc").value = desc;
   document.getElementById("m_lastconf").value = lastconf;
   document.getElementById("m_qtyord").value = qtyord;
   document.getElementById("m_um").value = um;
   document.getElementById("m_price").value = price;
   document.getElementById("m_supplier").value = supplier;
   document.getElementById("m_itemcode").value = part;
   document.getElementById("m_status").value = status;
   document.getElementById("m_totconf").value = totconf;
   document.getElementById("m_qtyrec").value = qtyrec;
   document.getElementById("m_duedate").value = duedate;
   
  });
</script>

@endsection
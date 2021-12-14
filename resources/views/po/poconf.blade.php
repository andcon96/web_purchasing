@extends('layout.layout')
@section('menu_name','Purchase Order Confirmation')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order Confirmation</li>
</ol>
@endsection
@section('content')


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

<div class="form-group row" style="color:red;margin-bottom:0px !important;margin-left:10px;">
    <label for="ponbr" class="col-form-label">{{ __('Total PO UnConfirm :') }}  {{ $totpo }} </label>
    <div class="col-md-1">
        <label class="col-form-label text-md-left"> </label>
    </div> 
    <!-- <label for="ponbr" class="col-form-label">{{ __('PO UnConfirm:') }}</label>
    <div class="col-md-1">
        <label class="col-form-label text-md-left">4</label>
    </div>
    <label for="ponbr" class="col-form-label">{{ __('Shipped PO :') }}</label>
    <div class="col-md-1">
        <label class="col-form-label text-md-left">5</label>
    </div> -->
</div>
<input type="hidden" id="tmpponumber"/>
<input type="hidden" id="tmpdatefrom"/>
<input type="hidden" id="tmpdateto"/>
<input type="hidden" id="tmpsupplier"/>
<input type="hidden" id="tmpstatus"/>
<input type="hidden" id="tmpitemcode"/>
<div class="form-group row" style="margin-bottom:0px !important;margin-left:0px;">
        <label for="ponbr" class="col-md-1 col-form-label">{{ __('PO No.') }}</label>
        <div class="col-md-2">
            <input id="ponbr" type="text" class="form-control" name="ponbr" autocomplete="off" 
            value="" autofocus>
        </div>       
		
		<label for="datefrom" class="col-md-0 col-form-label text-md-right">{{ __('From') }}</label>
		<div class="col-md-2">
            <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                        required autofocus>
        </div>

        <label for="dateto" class="col-md-0 col-form-label text-md-right">{{ __('To') }}</label>
        <div class="col-md-2">
            <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                            required autofocus>
        </div>
			
        <label for="supplier" class="col-md-0 col-form-label text-md-right" >{{ __('Supplier') }}</label>
        <div class="col-md-2">
            <input id="supplier" type="text" class="form-control" name="supplier" autocomplete="off" 
            value="" autofocus>
        </div>
         
        <div class="offset-md-0">
            <input type="button" class="btn bt-ref" 
            id="btnsearch" value="Search" />
        </div>

        <div class="col-md-3">
            <input id="itemcode" type="hidden" class="form-control" name="itemcode" 
            value="" autofocus>
        </div>

		<div class="col-md-3 mb-3">
                    <input id="status" type="hidden" class="form-control" name="status" 
                    value="" autofocus>
        </div>

        <!--Table-->
        <div class="table-responsive col-lg-12 col-md-12 tag-container mb-3">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>PO No.</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Due Date</th>  
                <th>Currency</th>
                <th>Status</th>  
                <th colspan="2">Option</th>                
            </tr>
        </thead>
            <tbody>         
                @include('po.tablepocf')
            </tbody>
        </table>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
        </div>

        <div id="loader" class="lds-dual-ring hidden overlay"></div>
</div>
        
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Bid</h5>
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



@endsection

@section('scripts')
    
<script>
    $( "#datefrom" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });
    $( "#dateto" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });

    // $(document).on('click','.pagination a', function(e){
    //     e.preventDefault();

    //     //alert('123');
    //     var page = $(this).attr('href').split('?page=')[1];

    //     //console.log(page);
    //     getData(page);

    // });

    // function getData(page){
    //     $.ajax({
    //         url: '/po/fetch_datacf?page='+ page,
    //         type: "get",
    //         datatype: "html" 
    //     }).done(function(data){
    //         console.log('Page = '+ page);

    //         $(".tag-container").empty().html(data);

    //     }).fail(function(jqXHR, ajaxOptions, thrownError){
    //         Swal.fire({
    //             icon: 'error',
    //             text: 'No Response From Server',
    //         })
    //     });
    // }

    function fetch_data(page, ponbr, itemcode, supplier, status, datefrom, dateto) {
      $.ajax({
        url: "/poconf/posearchcf?page=" + page + "&nbr=" + ponbr + "&code=" + itemcode + "&supp=" + supplier + "&status=" + status + "&datefrom=" + datefrom + "&dateto=" + dateto,
		beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    		$('#loader').removeClass('hidden')
    	},
        success: function(data) {
          console.log(data);
          $('tbody').html('');
          $('tbody').html(data);
        },
		complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
			$('#loader').addClass('hidden')
		},
      })
    }


    $(document).on('click', '#btnsearch', function() {
      var ponbr  = $('#s_rfpnumber').val(); 
      var itemcode    = $('#itemcode').val(); 
      var check_supplier = $("#supplier").val();
      var supplier = "";

      if(check_supplier){
          supplier = check_supplier.value;
      }
      
      var status = $('#status').val();
      var datefrom = $('#datefrom').val();
	  var dateto = $('#dateto').val();

      // var column_name = $('#hidden_column_name').val();
      // var sort_type = $('#hidden_sort_type').val();
      var page = 1;

      document.getElementById('tmpponumber').value  = ponbr;
      document.getElementById('tmpdatefrom').value = datefrom;
      document.getElementById('tmpdateto').value = dateto;
      document.getElementById('tmpsupplier').value = supplier;
      document.getElementById('tmpstatus').value = status;
      document.getElementById('tmpitemcode').value = itemcode;

      fetch_data(page, ponbr, itemcode, supplier, status, datefrom, dateto);
    });

  
    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();

      var ponbr  = $('#tmpponumber').val(); 
      var itemcode    = $('#tmpitemcode').val(); 
      var supplier = $('tmpsupplier');
      var status = $('#tmpstatus').val();
      var datefrom = $('#tmpdatefrom').val();
	  var dateto = $('#tmpdateto').val();
      
      fetch_data(page, ponbr, itemcode, supplier, status, datefrom, dateto);
    });


    // $('#btnsearch').on('click',function(){
    //     var ponbr = document.getElementById("ponbr").value;
    //     var itemcode = document.getElementById("itemcode").value;
        
    //     var check_supplier = document.getElementById("supplier");
    //     var supplier = "";
    //     if(check_supplier){
    //         supplier = check_supplier.value;
    //     }

    //     var datefrom = document.getElementById("datefrom").value;
    //     var dateto = document.getElementById("dateto").value;
    //     var status = document.getElementById("status").value;

    //     jQuery.ajax({
    //         type : "get",
    //         url : "{{URL::to("posearchcf") }}",
    //         data:{
    //         nbr : ponbr,
    //         code : itemcode,
    //         supp : supplier,
    //         status : status,
    //         datefrom : datefrom,
    //         dateto : dateto,
    //         },
    //         beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    //             $('#loader').removeClass('hidden')
    //         },
    //         success:function(data){
    //         //$('tbody').html(data);
    //         console.log(data);
    //         $(".tag-container").empty().html(data);
    //         },
    //         complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
    //             $('#loader').addClass('hidden')
    //         },
    //     });

    // });


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
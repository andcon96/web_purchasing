@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order List</li>
</ol>
@endsection


@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
        {{ session()->get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session()->has('updated'))
    <div class="alert alert-success  alert-dismissible fade show"  role="alert">
        {{ session()->get('updated') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!--Search-->
<div class="form-group row col-md-12" style="color:red;margin-bottom:0px !important;margin-left:1px;">
    <label for="ponbr" class="col-form-label">{{ __('Total PO :') }}</label>
      <div class="col-md-1 col-sm-3">
          <label class="col-form-label text-md-left">
              <?php echo $totalpo ?>
          </label>
      </div> 
    <label for="ponbr" class="col-form-label">{{ __('Unapproved PO :') }}</label>
      <div class="col-md-1 col-sm-3">
          <label class="col-form-label text-md-left">
              <?php echo $unapppo ?>
          </label>
      </div>
    <label for="ponbr" class="col-form-label">{{ __('Shipped PO :') }}</label>
      <div class="col-md-1 col-sm-3">
          <label class="col-form-label text-md-left">
              <?php echo $shippo ?>
          </label>
      </div>
</div>

<div class="form-group row mt-3" style="margin-bottom:0px !important;">
      <label for="ponbr" class="col-md-2 col-lg-1 col-form-label">{{ __('PO No.') }}</label>
      <div class="col-md-4 col-lg-2">
          <input id="ponbr" type="text" class="form-control" name="ponbr" autocomplete="off" 
          value="" autofocus>
      </div>
      <label for="itemcode" class="col-md-2 col-lg-1 col-form-label">{{ __('Item No.') }}</label>
      <div class="col-md-4 col-lg-2">
          <input id="itemcode" type="text" class="form-control" name="itemcode" autocomplete="off" 
          value="" autofocus>
      </div>
      <label for="status" class="col-md-2 col-lg-1 col-form-label">{{ __('Status') }}</label>
      <div class="col-md-4 col-lg-2">
          <select id="status" type="text" class="form-control" name="status" required>
            @if(Session::get('supp_code') != null)
            <option value="">Select Data</option>
            <option value="Approved">Approved</option>
            <option value="Confirm">Confirm</option>
            @else
            <option value="">Select Data</option>
            <option value="UnConfirm">Unapproved</option>
            <option value="Approved">Approved</option>
            <option value="Confirm">Confirm</option>
            <option value="Rejected">Rejected</option>
            @endif
          </select>
      </div>
</div>

<div class="form-group row mt-3" style="margin-bottom:0px !important;">
      <label for="datefrom" class="col-md-2 col-lg-1 col-form-label">{{ __('From') }}</label>
      <div class="col-md-4 col-lg-2">
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>
      <label for="dateto" class="col-md-2 col-lg-1 col-form-label">{{ __('To') }}</label>
      <div class="col-md-4 col-lg-2">
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>
      @if(!Session::get('supp_code'))
      <label for="supplier" class="col-md-2 col-lg-1 col-form-label">{{ __('Supplier') }}</label>
          <div class="col-md-4 col-lg-2">
              <input id="supplier" type="text" class="form-control" name="supplier" autocomplete="off"
              value="" autofocus>
          </div>
      @endif
      <div class="offset-md-2 offset-lg-0">
        <input type="button" class="btn bt-ref" 
        id="btnsearch" value="Search" style="margin-left:15px;" />
      </div>
      <div class="offset-md-0 offset-lg-0">
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px"><i class="fas fa-sync"></i></button>
      </div>
</div>
<!--Table-->

<div class='mt-3'>
    @include('po.tablepo')
</div>
        
        
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Detail PO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form>

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="m_ponbr" class="col-md-3 col-form-label text-md-right">{{ __('PO Number') }}</label>
                    <div class="col-md-3">
                        <input id="m_ponbr" type="text" class="form-control" name="m_ponbr" value="" readonly autofocus>
                    </div>
                    <label for="m_itemcode" class="col-md-2 col-form-label text-md-right">{{ __('Item Number') }}</label>
                    <div class="col-md-3">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_itemdesc" class="col-md-3 col-form-label text-md-right">{{ __('Item Desc') }}</label>
                    <div class="col-md-7">
                        <textarea id="m_itemdesc" type="text" class="form-control" name="m_itemdesc" value="" rows='4' readonly autofocus>
                        </textarea>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_supplier" class="col-md-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-3">
                        <input id="m_supplier" type="text" class="form-control" name="m_supplier" value="" readonly autofocus>
                    </div>
                    <label for="m_duedate" class="col-md-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-3">
                        <input id="m_duedate" type="text" class="form-control" name="m_duedate" value="" readonly autofocus>
                    </div>
                </div>
                
                
                <div class="form-group row">
                    <label for="m_qtyord" class="col-md-3 col-form-label text-md-right">{{ __('Qty Order') }}</label>
                    <div class="col-md-3">
                        <input id="m_qtyord" type="text" class="form-control" name="m_qtyord" value="" readonly autofocus>
                    </div>
                <!--
                <div class="form-group row">
                    <label for="m_qtyship" class="col-md-4 col-form-label text-md-right">{{ __('Qty Ship') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyship" type="text" class="form-control" name="m_qtyship" value="" readonly autofocus>
                    </div>
                </div>
                -->
                    <label for="m_qtyrec" class="col-md-2 col-form-label text-md-right">{{ __('Qty Rcpt') }}</label>
                    <div class="col-md-3">
                        <input id="m_qtyrec" type="text" class="form-control" name="m_qtyrec" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_price" class="col-md-3 col-form-label text-md-right">{{ __('Price') }}</label>
                    <div class="col-md-3">
                        <input id="m_price" type="text" class="form-control" name="m_price" value="" readonly autofocus>
                    </div>
                    <label for="m_status" class="col-md-2 col-form-label text-md-right">{{ __('Status') }}</label>
                    <div class="col-md-3">
                        <input id="m_status" type="text" class="form-control" name="m_status" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_lastconf" class="col-md-3 col-form-label text-md-right">{{ __('Last Conf') }}</label>
                    <div class="col-md-3">
                        <input id="m_lastconf" type="text" class="form-control" name="m_lastconf" value="" readonly autofocus>
                    </div>
                    <label for="m_totconf" class="col-md-2 col-form-label text-md-right">{{ __('Total Conf') }}</label>
                    <div class="col-md-3">
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


<div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection

@section('scripts')


<script>

    $(document).on('hide.bs.modal','#detailModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });

    $(document).on('click','.pagination a', function(e){
        e.preventDefault();

        //alert('123');
        var page = $(this).attr('href').split('?page=')[1];
        // var page = 1;
        var pono = $("#ponbr").val();
        var itemcode = $("#itemcode").val();
        var status = $("#status").val();
        var datefrom = $("#datefrom").val();
        var dateto = $("#dateto").val();
        var supplier = $("#supplier").val();

        //console.log(page);
        getData(page,pono,itemcode,status,datefrom,dateto,supplier);

    });

    function getData(page,pono,itemno,status,datefrom,dateto,supplier){
        $.ajax({
            url: '/posearch?page='+page+"&nbr="+pono+"&code="+itemno+"&status="+status+"&datefrom="+datefrom+"&dateto="+dateto+"&supp="+supplier,
            type: "get",
            datatype: "html" 
        }).done(function(data){
                console.log('Page = '+ page);

                $(".tag-container").empty().html(data);

        }).fail(function(jqXHR, ajaxOptions, thrownError){
            Swal.fire({
                icon: 'error',
                text: 'No Response From Server',
            })
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
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
        });
    });

    $('#btnrefresh').on('click',function(){

        var ponbr = '';
        var itemcode = '';
        var supplier = '';
        var datefrom = '';
        var dateto = '';
        var status = '';

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
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
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
        var um1 = $(this).data('um');
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
        document.getElementById("m_qtyord").value = qtyord.concat(um);
        document.getElementById("m_price").value = price;
        document.getElementById("m_supplier").value = supplier;
        document.getElementById("m_itemcode").value = part;
        document.getElementById("m_status").value = status;
        document.getElementById("m_totconf").value = totconf;
        document.getElementById("m_qtyrec").value = qtyrec;
        document.getElementById("m_duedate").value = duedate;

        });
        
        $( function() {
        $( "#datefrom" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
        $( "#dateto" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
    </script>
@endsection
@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFQ History Data</li>
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

  <!--Search By RFQ Number-->
  <div class="form-group row">
        <label for="rfqnumber" class="col-md-2 col-lg-1 col-form-label">{{ __('RFQ No.') }}</label>
        <div class="col-md-4  col-lg-2">
            <input id="rfqnumber" type="text" class="form-control" name="rfqnumber" 
            value="" autofocus>
        </div>
        <label for="itemreq" class="col-md-2 col-lg-1 col-form-label ">{{ __('Item No.') }}</label>
        <div class="col-md-4 col-lg-2">
            <input id="itemreq" type="text" class="form-control" name="itemreq" 
            value="" autofocus>
        </div>
        <label for="status" class="col-md-2 col-lg-1 col-form-label seconddata">{{ __('Status') }}</label>
        <div class="col-md-4 col-lg-2">
            <select  id="status" class="form-control status seconddata" name="status" autofocus>
                <option value="">--Choose--</option>
                <option value="0">Open</option>
                <option value="1">Submitted</option>
                <option value="2">Approved</option>
                <option value="3">Rejected</option>
                <option value="4">Closed</option>
            </select>
        </div>
  </div>
  <div class="form-group row">
        <label for="datefrom" class="col-md-2 col-lg-1 col-form-label">{{ __('From') }}</label>
        <div class="col-md-4 col-lg-2">
            <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                    required autofocus autocomplete="off">
        </div>
        <label for="dateto" class="col-md-2 col-lg-1 col-form-label">{{ __('To') }}</label>
        <div class="col-md-4 col-lg-2">
            <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                    required autofocus autocomplete="off">
        </div>
        <label for="suppcode" class="col-md-2 col-lg-1 col-form-label seconddata">{{ __('Supplier') }}</label>
        <div class="col-md-4 col-lg-2">
            <input type="text" id="suppcode" class="form-control seconddata" name='suppcode'
                    required autofocus autocomplete="off">
        </div>
        <div class="seconddata">
          <input type="button" class="btn bt-action" 
          id="btnsearch" value="Search" style="margin-left:15px;" />
          <button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width:40px"><i class="fa fa-sync"></i></button>
        </div>
  </div>
  <!--Table-->
  @include('rfq.loadhist')

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Detail History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form method="get">

            <input type="hidden" name='edit_id' id='edit_id'/>

            <div class="modal-body">
                <div class="form-group row">
                    <label for="m_rfqnbr" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="m_rfqnbr" type="text" class="form-control" name="m_rfqnbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_itemcode" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Item') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_site" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Site') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_site" type="text" class="form-control" name="m_site" value="" readonly autofocus>
                    </div>
                    <label for="m_qtyreq" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Qty Req') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_qtyreq" type="text" class="form-control" name="m_qtyreq" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_startdate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Start Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_startdate" type="text" class="form-control" name="m_startdate" value="" readonly autofocus>
                    </div>
                    <label for="m_duedate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_duedate" type="date" class="form-control" name="m_duedate" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_pricemin" type="text" class="form-control" name="m_pricemin" value="" readonly autofocus>
                    </div>
                    <label for="m_pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Price Max') }}</label>
                    <div class="col-md-5 col-lg-3">
                        <input id="m_pricemax" type="text" class="form-control" name="m_pricemax" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_supplier" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_supplier" type="text" class="form-control" name="m_supplier" value="" readonly autofocus>
                    </div>
                    <label for="m_proqty" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Propose Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_proqty" type="text" class="form-control" name="m_proqty" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_prodate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Propose Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_prodate" type="date" class="form-control" name="m_prodate" value="" readonly autofocus>
                    </div>
                    <label for="m_proprice" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Price') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_proprice" type="text" class="form-control" name="m_proprice" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_purqty" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Purchase Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_purqty" type="text" class="form-control" name="m_purqty" value="" readonly autofocus>
                    </div>
                    <label for="m_purdate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Purchase Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_purdate" type="text" class="form-control" name="m_purdate" value="" readonly autofocus>
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
      $( function() {
        $( "#datefrom" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
        $( "#dateto" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });

</script>
<script type="text/javascript">
    $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];
      var value = document.getElementById("rfqnumber").value;
      var code = document.getElementById("itemreq").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      var status = document.getElementById("status").value;
      var suppcode = document.getElementById("suppcode").value;

      //console.log(page);
      getData(page,value,code,datefrom,dateto,status,suppcode);

    });

    function getData(page,rfq,code,datefrom,dateto,status,suppcode){

        $.ajax({
            url: '/searchhist?page='+ page+'&rfq='+rfq+'&code='+code+'&datefrom='+datefrom+'&dateto='+dateto+'&status='+status+'&suppcode='+suppcode,
            type: "get",
            datatype: "html" 
        }).done(function(data){
                console.log('Page = '+ page);

                $(".tag-container").empty().html(data);
                //location.hash = page;
                //console.log(data);
        }).fail(function(jqXHR, ajaxOptions, thrownError){
                alert('No response from server');
        });
    }

    $('#btnsearch').on('click',function(){
      var value = document.getElementById("rfqnumber").value;
      var code = document.getElementById("itemreq").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      var status = document.getElementById("status").value;
      var suppcode = document.getElementById("suppcode").value;


      jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchhist") }}",
          data:{
            rfq : value,
            code : code,
            datefrom : datefrom,
            dateto : dateto,
            status : status,
            suppcode : suppcode,
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
      var value = '';
      var code = '';
      var datefrom = '';
      var dateto = '';
      var status = '';
      var suppcode = '';
    
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchhist") }}",
          data:{
            rfq : value,
            code : code,
            datefrom : datefrom,
            dateto : dateto,
            status : status,
            suppcode : suppcode,
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
       var uid = $(this).data('detid');
       var nbr = $(this).data('rfqnbr');
       var part = $(this).data('itemcode');
       var site = $(this).data('site');
       var qty = $(this).data('qtyreq');
       var date = $(this).data('duedate');
       var startdate = $(this).data('startdate');
       var pricemin = $(this).data('pricemin');
       var pricemax = $(this).data('pricemax');
       var supplier = $(this).data('supplier');
       var proqty = $(this).data('proqty');
       var prodate = $(this).data('prodate');
       var proprice = $(this).data('proprice');
       var suppid = $(this).data('supplierid');
       var purqty = $(this).data('purqty');
       var purdate = $(this).data('purdate');
       var itemdesc = $(this).data('itemdesc');

       
       document.getElementById("edit_id").value = uid;
       //document.getElementById("m_itemcode").value = part.concat(' - ',itemdesc); 
       document.getElementById("m_itemcode").value = part + ' - ' + itemdesc; 
       document.getElementById("m_rfqnbr").value = nbr;
       document.getElementById("m_qtyreq").value = qty;
       document.getElementById("m_duedate").value = date;
       document.getElementById("m_pricemin").value = pricemin;
       document.getElementById("m_pricemax").value = pricemax;
       document.getElementById("m_supplier").value = supplier;
       document.getElementById("m_proqty").value = proqty;
       document.getElementById("m_prodate").value = prodate;
       document.getElementById("m_proprice").value = proprice;
       document.getElementById("m_site").value = site;
       document.getElementById("m_startdate").value = startdate;
       document.getElementById("m_purdate").value = purdate;
       document.getElementById("m_purqty").value = purqty;
    });   
</script>
@endsection
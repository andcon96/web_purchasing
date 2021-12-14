@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFQ Approval</li>
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


<div class="form-group row" style="color:red;margin-bottom:0px !important;margin-left:0px;">
    <label for="ponbr" class="col-form-label">{{ __('Open RFQ:') }}</label>
      <div class="col-md-1">
          <label class="col-form-label text-md-left">
              <?php echo $openrfq ?>
          </label>
      </div> 
    <label for="ponbr" class="col-form-label">{{ __('Past Due RFQ :') }}</label>
      <div class="col-md-1">
          <label class="col-form-label text-md-left">
              <?php echo $pastduerfq ?>
          </label>
      </div>
</div>

<div class="form-group row">
      <label for="rfqnumber" class="col-md-2 col-lg-2 col-form-label">{{ __('RFQ No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="rfqnumber" type="text" class="form-control" name="rfqnumber" 
          value="" autofocus>
      </div>
      <label for="itemreq" class="col-md-2 col-lg-2 col-form-label">{{ __('Item No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="itemreq" type="text" class="form-control" name="itemreq" 
          value="" autofocus>
      </div>
</div>
<div class="form-group row">
      <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('From') }}</label>
      <div class="col-md-4 col-lg-3">
        <!--
          <input id="datefrom" type="date" class="form-control" name="datefrom" 
          value="" autofocus>
        --> 
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
      <label for="dateto" class="col-md-2 col-lg-2 col-form-label">{{ __('To') }}</label>
      <div class="col-md-4 col-lg-3">
          <!--
          <input id="dateto" type="date" class="form-control" name="dateto" 
          value="" autofocus>
          -->
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
      <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
        <input type="button" class="btn bt-action seconddata" 
        id="btnsearch" value="Search" />
        <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
      </div>
</div>
<!--Table-->

@include('rfq.load')
        
<div class="modal fade" id="detailModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">RFQ Approve</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/purchupdate" method="post" id="update">
            {{ csrf_field() }}

            <input type="hidden" name="edit_id" id="edit_id">
            <input type="hidden" name="rfqsite" id="rfqsite">
            <input type="hidden" name="suppid" id="suppid">
            <input type="hidden" name="startdate" id="startdate">
            <input type="hidden" name="m_note_purch" id="m_note_purch">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="m_rfqnbr" class="col-md-4 col-lg-3 col-form-label">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="m_rfqnbr" type="text" class="form-control" name="m_rfqnbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_itemcode" class="col-md-4 col-lg-3 col-form-label">{{ __('Item Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyreq" class="col-md-4 col-lg-3 col-form-label">{{ __('Qty Requested') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_qtyreq" type="text" class="form-control" name="m_qtyreq" value="" readonly autofocus>
                    </div>
                    <label for="m_duedate" class="col-md-4 col-lg-2 col-form-label">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_duedate" type="date" class="form-control" name="m_duedate" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_pricemin" class="col-md-4 col-lg-3 col-form-label">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_pricemin" type="text" class="form-control" name="m_pricemin" value="" readonly autofocus>
                    </div>
                    <label for="m_pricemax" class="col-md-4 col-lg-2 col-form-label">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_pricemax" type="text" class="form-control" name="m_pricemax" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_supplier" class="col-md-4 col-lg-3 col-form-label">{{ __('Supplier') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_supplier" type="text" class="form-control" name="m_supplier" value="" readonly autofocus>
                    </div>
                    <label for="m_proqty" class="col-md-4 col-lg-2 col-form-label">{{ __('Propose Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_proqty" type="text" class="form-control" name="m_proqty" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_prodate" class="col-md-4 col-lg-3 col-form-label">{{ __('Propose Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_prodate" type="date" class="form-control" name="m_prodate" value="" readonly autofocus>
                    </div>
                    <label for="m_proprice" class="col-md-4 col-lg-2 col-form-label">{{ __('Price') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="m_proprice" type="text" class="form-control" name="m_proprice" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_pro_ref" class="col-md-4 col-lg-3 col-form-label">{{ __('Supplier Note') }}</label>
                    <div class="col-md-7 col-lg-8">
                      <textarea id="m_pro_ref" class="form-control" name="m_pro_ref" style="height:100px !important"  readonly></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="purdate" class="col-md-4 col-lg-3 col-form-label">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="purdate" type="text" class="form-control" name="purdate" value="" placeholder="DD/MM/YYYY" required autofocus  autocomplete="off">
                        @if ($errors->has('purdate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('purdate') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="purqty" class="col-md-4 col-lg-2 col-form-label">{{ __('Purchase Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="purqty" type="text" class="form-control" name="purqty" value="" required autocomplete="off" autofocus>
                        @if ($errors->has('purqty'))
                            <span class="help-block">
                                <strong>{{ $errors->first('purqty') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="form-group row">
                    <label for="purqty" class="col-md-4 col-lg-3 col-form-label">{{ __('Convert To') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <select class="form-control" id='convert' name='convert' required>
                          <option value="">Select Data</option>
                          @if(empty($listconvert))

                          @else
                            @if( $listconvert->xrfq_po == 'Yes' )
                            <option value="1">PO</option>
                            @endif
                            @if( $listconvert->xrfq_pr == 'Yes')
                            <option value="2">PR</option>
                            @endif
                            <option value="3">Purchase Plan</option>
                          @endif
                        </select>
                    </div>
                    <label for="closerfq" class="col-md-4 col-lg-2 col-form-label">{{ __('Close RFQ') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <select class="form-control" id='closerfq' name='closerfq' required>
                          <option value="">Select Data</option>
                          <option value="Yes">All</option>
                          <option value="No">This one only</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row md-form">
                    <div class="col-md-12" style="text-align: center; margin-top:20px;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmitapp" required>
                          <label class="custom-control-label" for="cbsubmitapp">Confirm to submit</label>
                        </div>
                    </div>
                </div>
                <!--
                <div class="form-group row" id="conpo" style="display:none">
                    <label for="purqty" class="col-md-4 col-form-label">{{ __('Create New PO') }}</label>
                    <div class="col-md-5">
                        <select class="form-control" id='createnew' name='createnew'>
                          <option value="">Select Data</option>
                          <option value="1">Yes</option>
                          <option value="2">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row" id="listpo"  style="display:none">
                    <label for="purqty" class="col-md-4 col-form-label">{{ __('Connect To PO') }}</label>
                    <div class="col-md-5">
                        <select id='linkpo' name='linkpo'>
                          <option value="">Select Data</option>
                          @foreach($listpo as $listpo)
                              <option value="{{$listpo->xpo_nbr}}">{{$listpo->xpo_nbr}}</option>
                          @endforeach
                        </select>
                    </div>
                </div>
                -->
            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" data-dismiss="modal" id='btnclose'>Cancel</button>
              <button type="submit" class="btn bt-action" name='action' value="confirm" id='btnconf'>Save</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

<div class="modal fade" id="last10Modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Last 10 PO/RFQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

            <div class="modal-body">
                <div class="form-group row">
                    <label for="l_item" class="col-md-2 col-lg-2 col-form-label">{{ __('Item Part') }}</label>
                    <div class="col-md-5 col-lg-6">
                        <input id="l_item" type="text" class="form-control" name="l_item" value="" readonly autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12 col-md-12">
                        <table id='top10item' class='table supp-list'>
                            <thead>
                                <tr>
                                    <th style="width:15%">PO No.</th>
                                    <th style="width:15%">Supplier Code</th>
                                    <th style="width:15%">Supplier Desc</th>
                                    <th style="width:15%">Price</th>
                                    <th style="width:15%">Create Date</th>
                                </tr>
                            </thead>
                            <tbody id='bodytop10item'>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12 col-md-12">
                        <table id='top10item' class='table supp-list'>
                            <thead>
                                <tr>
                                    <th style="width:15%">RFQ No.</th>
                                    <th style="width:15%">Supplier Code</th>
                                    <th style="width:15%">Supplier Desc</th>
                                    <th style="width:15%">Price</th>
                                    <th style="width:15%">Create Date</th>
                                </tr>
                            </thead>
                            <tbody id='bodytop10rfq'>
                                
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" data-dismiss="modal" id='btnclose'>Cancel</button>
              </button>
            </div>


    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Close RFQ from Supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/purchupdate" method="post" id="updatereject">
            {{ csrf_field() }}

            <input type="hidden" name="d_edit_id" id="d_edit_id">
            <input type="hidden" name="d_rfqsite" id="d_rfqsite">
            <input type="hidden" name="d_suppid" id="d_suppid">
            <input type="hidden" name="d_startdate" id="d_startdate">
            <input type="hidden" name="d_note_purch" id="d_note_purch">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="d_rfqnbr" class="col-md-4 col-lg-3 col-form-label">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="d_rfqnbr" type="text" class="form-control" name="d_rfqnbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="d_itemcode" class="col-md-4 col-lg-3 col-form-label">{{ __('Item Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="d_itemcode" type="text" class="form-control" name="d_itemcode" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="d_qtyreq" class="col-md-4 col-lg-3 col-form-label">{{ __('Qty Requested') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_qtyreq" type="text" class="form-control" name="d_qtyreq" value="" readonly autofocus>
                    </div>
                    <label for="d_duedate" class="col-md-4 col-lg-2 col-form-label">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_duedate" type="date" class="form-control" name="d_duedate" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="d_pricemin" class="col-md-4 col-lg-3 col-form-label">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_pricemin" type="text" class="form-control" name="d_pricemin" value="" readonly autofocus>
                    </div>
                    <label for="d_pricemax" class="col-md-4 col-lg-2 col-form-label">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_pricemax" type="text" class="form-control" name="d_pricemax" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="d_supplier" class="col-md-4 col-lg-3 col-form-label">{{ __('Supplier') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_supplier" type="text" class="form-control" name="d_supplier" value="" readonly autofocus>
                    </div>
                    <label for="d_proqty" class="col-md-4 col-lg-2 col-form-label">{{ __('Propose Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_proqty" type="text" class="form-control" name="d_proqty" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="d_prodate" class="col-md-4 col-lg-3 col-form-label">{{ __('Propose Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_prodate" type="date" class="form-control" name="d_prodate" value="" readonly autofocus>
                    </div>
                    <label for="d_proprice" class="col-md-4 col-lg-2 col-form-label">{{ __('Price') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_proprice" type="text" class="form-control" name="d_proprice" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="d_pro_ref" class="col-md-4 col-lg-3 col-form-label">{{ __('Supp Note') }}</label>
                    <div class="col-md-7 col-lg-8">
                      <textarea id="d_pro_ref" class="form-control" name="d_pro_ref" style="height:100px !important"  readonly></textarea>
                    </div>
                </div>

                <div class="form-group row md-form">
                    <div class="col-md-12" style="text-align: center; margin-top:15px;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmitdel" required>
                          <label class="custom-control-label" for="cbsubmitdel">Confirm to submit</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" data-dismiss="modal" id='d_btnclose'>Cancel</button>
              <button type="submit" class="btn bt-action" name='action' value="reject" id='d_btnconf'>Save</button>
              <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

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
        $( "#purdate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });

  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];
      var value = document.getElementById("rfqnumber").value;
      var code = document.getElementById("itemreq").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;

      //console.log(page);
      getData(page,value,code,datefrom,dateto);

  });

  $(document).on('hide.bs.modal','#detailModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });

  function getData(page,value,code,datefrom,dateto){

    $.ajax({
        url: '/rfqsearch?page='+ page+'&rfq='+value+'&code='+code+'&datefrom='+datefrom+'&dateto='+dateto,
        type: "get",
        datatype: "html" 
    }).done(function(data){
          console.log('Page = '+ page);

          $(".tag-container").empty().html(data);
          //location.hash = page;
          //console.log(data);
    }).fail(function(jqXHR, ajaxOptions, thrownError){
          alert('No response from the server');
    });
  }

  $('#update').submit(function(event) {


        var purdate = document.getElementById("purdate").value.split('/');
        var new_purdate = new Date(purdate[2].concat('-',purdate[1],'-',purdate[0]));
        var today = new Date();

        var qtypur = document.getElementById("purqty").value; 

        var reg = /^\d+$/;
        var regqty = /^(\s*|\d+\.\d*|\d+)$/;

        if(!regqty.test(qtypur)){
            alert('Qty Purchase must be number'); 
            return false;  
        }else if(qtypur <= 0){
            alert('Qty Purchase must be greater than 0')
            return false;
        }else if(new_purdate < today){
            alert('Purchase Date cannot be earlier than today');
            return false;
        }else{
            document.getElementById('btnclose').style.display = 'none';
            document.getElementById('btnconf').style.display = 'none';
            document.getElementById('btnreject').style.display = 'none';
            document.getElementById('btnloading').style.display = ''; 
            $(this).unbind('submit').submit();
        }

  });

  $('#updatereject').submit(function(event) {
    
      document.getElementById('d_btnclose').style.display = 'none';
      document.getElementById('d_btnconf').style.display = 'none';
      document.getElementById('d_btnreject').style.display = 'none';
      document.getElementById('d_btnloading').style.display = ''; 
      $(this).unbind('submit').submit();

  });

	$('#btnsearch').on('click',function(){
      var value = document.getElementById("rfqnumber").value;
      var code = document.getElementById("itemreq").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      

      //alert(value);
      /*
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqsearch1") }}",
          data:{
            search : value,
          },
          success:function(data){
            document.getElementById("itemreq").value = data[0]['xbid_part'];
            document.getElementById("pricemin").value = data[0]['xbid_price_min'];
            document.getElementById("pricemax").value = data[0]['xbid_price_max'];
            document.getElementById("qtyreq").value = data[0]['xbid_qty_req'];
            document.getElementById("duedate").value = data[0]['xbid_due_date'];
          }
      });
      */
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqsearch") }}",
          data:{
            rfq : value,
            code : code,
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
      var value = '';
      var code = '';
      var datefrom = '';
      var dateto = '';
      
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqsearch") }}",
          data:{
            rfq : value,
            code : code,
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
   var uid = $(this).data('detid');
   var part = $(this).data('itemcode');
   var nbr = $(this).data('rfqnbr');
   var qty = $(this).data('qtyreq');
   var date = $(this).data('duedate');
   var pricemin = $(this).data('pricemin');
   var pricemax = $(this).data('pricemax');
   var supplier = $(this).data('supplier');
   var proqty = $(this).data('proqty');
   var prodate = $(this).data('prodate');
   var price = $(this).data('price');
   var site = $(this).data('rfqsite');
   var suppid = $(this).data('supplierid');
   var startdate = $(this).data('startdate');
   var notesupp = $(this).data('notesupp');
   var notepurch = $(this).data('notepurch');

   document.getElementById("edit_id").value = uid;
   document.getElementById("m_itemcode").value = part;
   document.getElementById("m_rfqnbr").value = nbr;
   document.getElementById("m_qtyreq").value = qty;
   document.getElementById("m_duedate").value = date;
   document.getElementById("m_pricemin").value = pricemin;
   document.getElementById("m_pricemax").value = pricemax;
   document.getElementById("m_supplier").value = supplier;
   document.getElementById("m_proqty").value = proqty;
   document.getElementById("m_prodate").value = prodate;
   document.getElementById("m_proprice").value = price;
   document.getElementById("rfqsite").value = site;
   document.getElementById("suppid").value = suppid;
   document.getElementById("startdate").value = startdate;
   document.getElementById("m_pro_ref").value = notesupp;
   document.getElementById("m_note_purch").value = notepurch;
  });
  
  $(document).on('click','.deleteUser',function(){ // Click to only happen on announce links
   
   //alert('123');
   var uid = $(this).data('detid');
   var part = $(this).data('itemcode');
   var nbr = $(this).data('rfqnbr');
   var qty = $(this).data('qtyreq');
   var date = $(this).data('duedate');
   var pricemin = $(this).data('pricemin');
   var pricemax = $(this).data('pricemax');
   var supplier = $(this).data('supplier');
   var proqty = $(this).data('proqty');
   var prodate = $(this).data('prodate');
   var price = $(this).data('price');
   var site = $(this).data('rfqsite');
   var suppid = $(this).data('supplierid');
   var startdate = $(this).data('startdate');
   var notesupp = $(this).data('notesupp');
   var notepurch = $(this).data('notepurch');

   document.getElementById("d_edit_id").value = uid;
   document.getElementById("d_itemcode").value = part;
   document.getElementById("d_rfqnbr").value = nbr;
   document.getElementById("d_qtyreq").value = qty;
   document.getElementById("d_duedate").value = date;
   document.getElementById("d_pricemin").value = pricemin;
   document.getElementById("d_pricemax").value = pricemax;
   document.getElementById("d_supplier").value = supplier;
   document.getElementById("d_proqty").value = proqty;
   document.getElementById("d_prodate").value = prodate;
   document.getElementById("d_proprice").value = price;
   document.getElementById("d_rfqsite").value = site;
   document.getElementById("d_suppid").value = suppid;
   document.getElementById("d_startdate").value = startdate;
   document.getElementById("d_pro_ref").value = notesupp;
   document.getElementById("d_note_purch").value = notepurch;
  });

  $(document).on('click','#btnreject',function(){


      document.getElementById('purqty').required = false;
      document.getElementById('purdate').required = false;
      document.getElementById('convert').required = false;
      document.getElementById('closerfq').required = false;

      document.getElementById('e_btnclose').style.display = 'none';
      document.getElementById('e_btnconf').style.display = 'none';
      document.getElementById('e_btnloading').style.display = ''; 
      $(this).unbind('submit').submit();

  });

  $(document).on('click','.last10search',function(){ // Click to only happen on announce links
   
   //alert('123');
     var part = $(this).data('itemcode');
     var desc = $(this).data('itemdesc');

     document.getElementById("l_item").value = part + ' - ' + desc;
    
     jQuery.ajax({
          type : "get",
          url : "{{URL::to("polast10search") }}",
          data:{
            search : part
          },
          success:function(data){
            //$('tbody').html(data);
            //console.log(data);
            $("#bodytop10item").empty().html(data);
          }
      });

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqlast10search") }}",
          data:{
            search : part
          },
          success:function(data){
            //$('tbody').html(data);
            //console.log(data);
            $("#bodytop10rfq").empty().html(data);
          }
      });

  });

  $(document).ready(function(){
        $("#linkpo").select2({
      });
  });

</script>

@endsection
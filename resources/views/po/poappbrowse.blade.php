@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order Approval</li>
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

<!--Search-->
<div class="form-group row">
      <input type='hidden' id="sessionid" value="{!! Session::get('userid') !!}">
      <label for="ponbr" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('PO No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="ponbr" type="text" class="form-control" name="ponbr" 
          value="" autofocus>
      </div>
      <label for="datefrom" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('From') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>
      <label for="dateto" class="col-md-2 col-lg-1 col-form-label text-md-right seconddata">{{ __('To') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="dateto" class="form-control seconddata" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>
</div>
<div class="form-group row">
      <label for="approver" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('Approver') }}</label>
      <div class="col-md-4 col-lg-3">
          <!-- <input id="approver" type="text" class="form-control" name="approver" 
          value="" autofocus> -->
          <select name="approver" id="approver" class='form-control'>
            <option value="">Select Data</option>
            @foreach($approver as $approver)
            <option value="{{$approver->id}}">{{$approver->name}}</option>
            @endforeach
          </select>
      </div>
      <label for="status" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('Status') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="status" type="text" class="form-control" name="status" 
          value="" autofocus>
      </div>
      <label for="status" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('') }}</label>
      <div class="offset-md-2 offset-lg-0 ml-3">
        <input type="button" class="btn bt-action" 
        id="btnsearch" value="Search" />

        <button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fas fa-sync"></i></button>
      </div>
</div>


@include('po.tablepoapp')


<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Approval Log</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <input type="hidden" name="edit_id" id="edit_id">

          <div class="modal-body">
              <div class="form-group row">
                  <label for="m_ponbr" class="col-md-2 col-form-label text-md-right">{{ __('PO No.') }}</label>
                  <div class="col-md-5">
                      <input id="m_ponbr" type="text" class="form-control" name="m_ponbr" value="" readonly autofocus>
                  </div>
              </div>

              <div class="form-group row col-lg-12 col-md-12 col-sm-12">
                  <table id='suppTable' class='table supp-list'>
                      <thead>
                          <tr>
                              <th style="width:5%">Order</th>
                              <th style="width:15%">Main Approver</th>
                              <th style="width:15%">Alt Approver</th>
                              <th style="width:15%">Action By</th>
                              <th style="width:10%">Status</th>
                              <th style="width:30%">Reason</th>
                              <th style="width:20%">Date</th>
                          </tr>
                      </thead>
                      <tbody id='oldsupplier'>
                      </tbody>
                  </table>
              </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-info bt-action" data-dismiss="modal">Close</button>
          </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">PO Approval</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/approvepo" method="post" id="update">
          {{csrf_field() }}
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="hidden" name="apporder" id="apporder">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="po_nbr" class="col-md-2 col-form-label text-md-right">{{ __('PO No.') }}</label>
                    <div class="col-md-4">
                        <input id="po_nbr" type="text" class="form-control" name="po_nbr" value="" readonly autofocus>
                    </div>
                    <label for="createdate" class="col-md-2 col-form-label text-md-right">{{ __('Created Date') }}</label>
                    <div class="col-md-4">
                        <input id="createdate" type="text" class="form-control" name="createdate" value="" readonly autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-4">
                        <input id="supplier" type="text" class="form-control" name="supplier" value="" readonly autofocus>
                    </div>
                    <label for="duedate" class="col-md-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-4">
                        <input id="duedate" type="text" class="form-control" name="duedate" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row" style="padding: 0px 12px 0px 20px;">
                    <table id='poapproval' class='table supp-list'>
                        <thead>
                            <tr>
                                <th style="width:10%">Line</th>
                                <th style="width:40%">Description</th>
                                <th style="width:20%">Price</th>
                                <th style="width:10%">Qty</th>
                                <th style="width:20%">Total</th>
                            </tr>
                        </thead>
                        <tbody id='bodyapproval'>
                        </tbody>
                    </table>
                </div>
                <div class="form-group row">
                    <label for="e_reason" class="col-md-2 col-form-label text-md-right">{{ __('Reason') }}</label>
                    <div class="col-md-9">
                        <input id="e_reason" type="text" class="form-control" name="e_reason" value="" autofocus>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" id="btnclose" data-dismiss="modal">Close</button>
              <button type="submit" class="btn bt-action" name='action' value="reject" id="btnreject">Reject</button>
              <button type="submit" class="btn bt-action" name='action' value="confirm" id="btnconf">Save</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
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
      $("#approver").select2({
        width : '100%'
      });
  });
  $(document).on('hide.bs.modal','#editModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });

  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];
      var ponbr = $("#ponbr").val();
      var datefrom = $("#datefrom").val();
      var dateto = $("#dateto").val();
      var approver = $("#approver").val();
      var status = $("#status").val();

      //console.log(page);
      getData(page,ponbr,approver,status,datefrom,dateto);

  });

  function getData(page,ponbr,approver,status,datefrom,dateto){
    $.ajax({
        url: '/poappsearch1?page='+ page+"&nbr="+ponbr+"&approver="+approver+"&status="+status+"&datefrom="+datefrom+"&dateto="+dateto,
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
      var approver = document.getElementById("approver").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      var status = document.getElementById("status").value;
      var altapp = '';

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("poappsearch1") }}",
          data:{
            nbr : ponbr,
            approver : approver,
            status : status,
            datefrom : datefrom,
            dateto : dateto,
            altapp : altapp,
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
      var approver = '';
      var datefrom = '';
      var dateto = '';
      var status = '';
      var altapp = '';


      jQuery.ajax({
          type : "get",
          url : "{{URL::to("poappsearch1") }}",
          data:{
            nbr : ponbr,
            approver : approver,
            status : status,
            datefrom : datefrom,
            dateto : dateto,
            altapp : altapp,
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

  $(document).on('click','.adddata',function(){ // Click to only happen on announce links

    var nbr = $(this).data('nbr');
    
    //alert('123');
    document.getElementById('m_ponbr').value = nbr;
    
    jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchhistapp") }}",
          data:{
            search : nbr,
          },
          success:function(data){
            //alert(data);
            console.log(data);
            $('#oldsupplier').html(data);
          }
    });
  });

  $(document).on('click','.updatedata',function(){ // Click to only happen on announce links

    var nbr = $(this).data('nbr');
    var supplier = $(this).data('supplier');
    var created = $(this).data('created');
    var due = $(this).data('due');
    var approver = $(this).data('approver');
    var altapprover = $(this).data('altapprover');
    var order = $(this).data('apporder');
    var session = document.getElementById('sessionid').value;


    if(session != approver && session != altapprover){
        document.getElementById('btnreject').style.display = 'none';
        document.getElementById('btnconf').style.display = 'none';
        document.getElementById('e_reason').readOnly = true;
    }else{
        document.getElementById('btnreject').style.display = '';
        document.getElementById('btnconf').style.display = '';
        document.getElementById('e_reason').readOnly = false;
    }

    //alert(nbr);

    document.getElementById('po_nbr').value = nbr;
    document.getElementById('supplier').value = supplier;
    document.getElementById('createdate').value = created;
    document.getElementById('duedate').value = due;
    document.getElementById('apporder').value = order;
    
    jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchdetailapppo") }}",
          data:{
            search : nbr,
          },
          success:function(data){
            //alert(data);
            $('#bodyapproval').html(data);
          }
    });
  });

  $(document).on('click','#btnreject',function(){

      var r = confirm("Reject PO ?");
      if(r == true){
          document.getElementById('btnclose').style.display = 'none';
          document.getElementById('btnconf').style.display = 'none';
          document.getElementById('btnreject').style.display = 'none';
          document.getElementById('btnloading').style.display = ''; 
          $(this).unbind('submit').submit();
      }else{
        return false;
      }
  });

  $(document).on('click','#btnconf',function(){

      var r = confirm("Approve PO ?");
      if(r == true){
          document.getElementById('btnclose').style.display = 'none';
          document.getElementById('btnconf').style.display = 'none';
          document.getElementById('btnreject').style.display = 'none';
          document.getElementById('btnloading').style.display = ''; 
          $(this).unbind('submit').submit();
      }else{
        return false;
      }
  });

</script>
@endsection
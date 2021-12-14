@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order Approval Utility</li>
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


<div class="form-group row" style="margin-bottom:10px !important;">
      <label for="ponbr" class="col-md-2 col-lg-1 col-form-label">{{ __('PO No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="ponbr" type="text" class="form-control" name="ponbr" autocomplete="off" 
          value="" autofocus>
      </div>
      @if(!Session::get('supp_code'))
      <label for="supplier" class="col-md-2 col-lg-1 col-form-label">{{ __('Supplier') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="supplier" type="text" class="form-control" name="supplier" autocomplete="off"
              value="" autofocus>
          </div>
      @endif
</div>

<div class="form-group row" style="margin-bottom:10px !important;">
      <label for="datefrom" class="col-md-2 col-lg-1 col-form-label">{{ __('From') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>
      <label for="dateto" class="col-md-2 col-lg-1 col-form-label">{{ __('To') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus>
      </div>

      <div class="offset-md-2 offset-lg-0">
        <input type="button" class="btn bt-ref" 
        id="btnsearch" value="Search" style="margin-left:15px;" />
        
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px"><i class="fa fa-sync"></i></button>
      </div>
</div>


@include('/setting/tableresetapproval')


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">PO Approval Utility</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" method="POST" action="/resetpoapproval" id="update">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="d-flex">
                   Reset Approval PO : <p id='tmp_ponbr' style="margin-left:15px;"></p>
                   <input type="hidden" name="t_ponbr" id="t_ponbr">
                  <div class="custom-control custom-checkbox" style="margin-left: 15px">
                          <input type="checkbox" class="custom-control-input" id="cbsubmit" required>
                          <label class="custom-control-label" for="cbsubmit">Confirm to update</label>
                   </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
              <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
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
    });
      $( function() {
        $( "#dateto" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
    
  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];
      var rfq = $("#ponbr").val();
      var code = $("#supplier").val();
      var datefrom = $("#datefrom").val();
      var dateto = $("#dateto").val();

      //console.log(page);
      getData(page,rfq,code,datefrom,dateto);

  });

  function getData(page,rfq,code,datefrom,dateto){
    $.ajax({
        url: '/searchresetapprove?page='+ page+"&rfq="+rfq+"&code="+code+"&datefrom="+datefrom+"&dateto="+dateto,
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
    var value = document.getElementById("ponbr").value;
    var code = document.getElementById("supplier").value;
    var datefrom = document.getElementById("datefrom").value;
    var dateto = document.getElementById("dateto").value;


    jQuery.ajax({
        type : "get",
        url : "{{URL::to("searchresetapprove") }}",
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
        url : "{{URL::to("searchresetapprove") }}",
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

  $(document).on('click','.edituser',function(){ // Click to only happen on announce links
    
        var ponbr = $(this).data('ponbr');
        var t_ponbr = $(this).data('ponbr');

        document.getElementById("tmp_ponbr").innerHTML = ponbr;
        document.getElementById("t_ponbr").value = t_ponbr;
    });

  $('#update').submit(function(event) {
    
          document.getElementById('e_btnclose').style.display = 'none';
          document.getElementById('e_btnconf').style.display = 'none';
          document.getElementById('e_btnloading').style.display = ''; 
          $(this).unbind('submit').submit();
    });

</script>
@endsection
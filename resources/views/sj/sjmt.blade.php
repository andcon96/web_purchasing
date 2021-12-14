@extends('layout.layout')
@section('menu_name','Shipment Registration')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
  <li class="breadcrumb-item active">Shipment Registration</li>
</ol>
@endsection
@section('content')

<!-- Page Heading -->


<!--<a href="/sjcrt" class="btn btn-primary mb-1">Create Surat Jalan</a> -->

<div class="form-group row" style="margin-bottom:0px !important;margin-left:1px;">
  <form action="/dynamic_dependent" method="get">
    <input disable type="hidden" name="supp" value={{ Auth::user()->supp_id }}>
    <div class="col-md-6">
      <button class="btn bt-action" type="submit" value="Create" style="width:150px !important">Create Shipper</button>
    </div>
  </form>

  <!-- <form action="/sjmtcari" method="get" style="display:flex"> -->

  <label for="id" class="col-md-2 col-form-label text-right">{{ __('Shipper ID') }}</label>
  <div class="col-md-3">
    <input id="id" type="text" class="form-control" name="id" autocomplete="off" value="" autofocus>
  </div>


  <input type="submit" class="btn bt-ref" id="btnsearch" value="Search" />
  <button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>

  <!-- </form> -->
</div>
<input type="hidden" id="tmpshippingid" />
<div class="table-responsive col-lg-12 col-md-2 tag-container">
  <table class="table table-bordered mt-3" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th class="tbhead">Shipper ID</th>
        <th class="tbhead">Supplier Code</th>
        <th class="tbhead">Purchase Order</th>
        <th class="tbhead">Line</th>
        <th class="tbhead">Item Number</th>
        <th class="tbhead">Description</th>
        <th class="tbhead">Qty Ship</th>
        <th class="tbhead">Location</th>
        <th class="tbhead">Lot/Serial</th>
        <th class="tbhead">Reference</th>
        <th class="tbhead">Status</th>
        <th colspan="2">Option</th>
      </tr>
    </thead>
    <tbody>
      @include('sj.tablesjmt')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
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

      <form action="/sjmtdel" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

          <input type="hidden" name="delete_id" id="delete_id" value="">
          <input type="hidden" name="t_supp" id="t_supp" value="">
          <input type="hidden" name="t_line" id="t_line" value="">
          <input type="hidden" name="t_lot" id="t_lot" value="">
          <input type="hidden" name="t_qship" id="t_qship" value="">
          <input type="hidden" name="t_shp" id="t_shp" value="">
          <input type="hidden" name="t_opn" id="t_opn" value="">
          <input type="hidden" name="t_nbr" id="t_nbr" value="">


          <div class="container">
            <div class="row">
              Delete for Shipper :
              &nbsp; <strong><a name="id" id="iddel"></a></strong>
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

@endsection


@section('scripts')
<script type="text/javascript">
  $(document).on('click', '.deleteUser', function() {
    var uid = $(this).data('id');
    var tid = $(this).data('id');
    var lot = $(this).data('lot');
    var supp = $(this).data('supp');
    var line = $(this).data('line');
    var qship = $(this).data('qship');
    var shp = $(this).data('shp')
    var opn = $(this).data('opn')
    var nbr = $(this).data('nbr')



    document.getElementById('delete_id').value = uid;
    document.getElementById('t_lot').value = lot;
    document.getElementById('t_line').value = line;
    document.getElementById('t_supp').value = supp;
    document.getElementById('iddel').innerHTML = tid;
    document.getElementById('t_qship').value = qship;
    document.getElementById('t_shp').value = shp;
    document.getElementById('t_opn').value = opn;
    document.getElementById('t_nbr').value = nbr;



  });

  function fetch_data(page, shippingid) {
    $.ajax({
      url: "/sjmtcari?page=" + page + "&shippingid=" + shippingid,
      beforeSend: function() { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $('#loader').removeClass('hidden')
      },
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      },
      complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
        $('#loader').addClass('hidden')
      },

    })
  }


  $(document).on('click', '#btnsearch', function() {
    var shippingid = $('#id').val();


    // var column_name = $('#hidden_column_name').val();
    // var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    document.getElementById('tmpshippingid').value = shippingid;
    

    fetch_data(page, shippingid);
  });


  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var shippingid = $('#tmpshippingid').val();
 

    fetch_data(page, shippingid);
  });

  $(document).on('click', '#btnrefresh', function() {
    var shippingid = '';

    var page = 1;

    document.getElementById('id').value = '';

    document.getElementById('tmpshippingid').value = shippingid;


    fetch_data(page, shippingid);
  });
</script>
@endsection
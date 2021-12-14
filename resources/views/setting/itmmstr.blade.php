@extends('layout.layout')

@section('menu_name','Item Inventory Master')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
  <li class="breadcrumb-item active">Item Inventory Master</li>
</ol>
@endsection

@section('content')

<!-- Page Heading -->


<!--<a href="/sjcrt" class="btn btn-primary mb-1">Create Surat Jalan</a> -->

<input type="hidden" id="tmp_part" />
<input type="hidden" id="tmp_prod" />
<input type="hidden" id="tmp_type" />

<div class="form-group row" style="margin-bottom:0px !important;margin-left:1px;">


  <label for="part" class="col-form-label">{{ __(' Item No.') }}</label>
  <div class="col-md-3">
    <input id="part" type="text" class="form-control" name="part" autocomplete="off" value="" autofocus>
  </div>
  <label for="prod" class="col-md-0 col-form-label text-md-right">{{ __('Prod Line') }}</label>
  <div class="col-md-2">
    <input type="text" id="prod" class="form-control" name='prod' autofocus>
  </div>
  <label for="type" class="col-md-0 col-form-label text-md-right">{{ __('Item type') }}</label>
  <div class="col-md-2">
    <input type="text" id="type" class="form-control" name='type' autofocus>
  </div>


  <div class="col-md-2">
    <input type="button" class="btn bt-ref" id="btnsearch" value="Search" />
    <button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
  </div>

</div>

<div class="table-responsive col-lg-12 col-md-2 tag-container mt-3">

  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Item Number</th>
        <th>Description</th>
        <th>Um</th>
        <th>Safety Stock</th>
        <th>Item Type</th>
        <th>Prod Line</th>
        <th>Alert Day1</th>
        <th>Alert Day2</th>
        <th>Alert Day3</th>
        <th>Almost Safety%</th>
      </tr>
    </thead>
    <tbody>
      @include('setting.table-itmmstr')
    </tbody>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
  </table>

</div>

@endsection

@section('scripts')

<script type="text/javascript">
  function fetch_data(page, itempart, prodline, itemtype) {
    $.ajax({
      url: "/itmmstrcari?page=" + page + "&part=" + itempart + "&prod=" + prodline + "&type=" + itemtype,
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

    // alert('masuk');
    var itempart = $('#part').val();
    var prodline = $('#prod').val();
    var itemtype = $('#type').val();


    // var column_name = $('#hidden_column_name').val();
    // var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    document.getElementById('tmp_part').value = itempart;
    document.getElementById('tmp_prod').value = prodline;
    document.getElementById('tmp_type').value = itemtype;

    fetch_data(page, itempart, prodline, itemtype);
  });


  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var itempart = $('#tmp_part').val();
    var prodline = $('#tmp_prod').val();
    var itemtype = $('#tmp_type').val();

    fetch_data(page, itempart, prodline, itemtype);
  });

  $(document).on('click', '#btnrefresh', function() {
    var itempart = '';
    var prodline = '';
    var itemtype = '';
    var page = 1;

    document.getElementById('part').value = '';
    document.getElementById('prod').value = '';
    document.getElementById('type').value = '';
    document.getElementById('tmp_part').value = itempart;
    document.getElementById('tmp_prod').value = prodline;
    document.getElementById('tmp_type').value = itemtype;


    fetch_data(page, itempart, prodline, itemtype);
  });
</script>

@endsection
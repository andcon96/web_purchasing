@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Last 10 RFQ and PO Data</li>
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

<div class="form-group row col-12">
      <label for="itemcode" class="col-md-2 col-lg-1 col-form-label">{{ __('Item No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="itemcode" type="text" class="form-control" name="itemcode" 
          value="" autofocus autocomplete="off">
      </div>
      <label for="supplier" class="col-md-2 col-lg-2 col-form-label">{{ __('Supplier Code') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="supplier" type="text" class="form-control" name="supplier" 
          value="" autofocus autocomplete="off">
      </div>

      <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
        <input type="button" class="btn bt-action seconddata" 
        id="btnsearch" value="Search" />
        <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
      </div>
</div>

@include('rfq.tablelast10')

@include('rfq.tablelast10po')


<div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection

@section('scripts')
  

<script>
  $('#btnsearch').on('click',function(){
        var item = document.getElementById("itemcode").value;
        var supplier = document.getElementById("supplier").value;
        
        jQuery.ajax({
            type : "get",
            url : "{{URL::to("searchtop10menu") }}",
            data:{
              item : item,
              supplier : supplier,
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

        jQuery.ajax({
            type : "get",
            url : "{{URL::to("searchtop10menupo") }}",
            data:{
              item : item,
              supplier : supplier,
            },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
              //$('tbody').html(data);
              console.log(data);
              $(".tag-containerpo").empty().html(data);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
        });
  });

  $('#btnrefresh').on('click',function(){
      var item = '';
      var supplier = '';
      var sel = '';
      
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchtop10menu") }}",
          data:{
            item : item,
            supplier : supplier,
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

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("searchtop10menupo") }}",
          data:{
            item : item,
            supplier : supplier,
            sel : sel,
          },
          beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
              $('#loader').removeClass('hidden')
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-containerpo").empty().html(data);
          },
          complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
              $('#loader').addClass('hidden')
          },
      });

  });
</script>
@endsection
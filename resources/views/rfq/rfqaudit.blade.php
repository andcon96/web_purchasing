@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFQ Audit Trail</li>
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
<div class="form-group row">
      <input type='hidden' id="sessionid" value="{!! Session::get('userid') !!}">
      <label for="ponbr" class="col-md-2 col-lg-1 col-form-label text-md-right">{{ __('RFQ No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="ponbr" type="text" class="form-control" name="ponbr" 
          value="" autofocus>
      </div>
      <div class="offset-md-2 offset-lg-0 ml-3">
        <input type="button" class="btn bt-action seconddata" 
        id="btnsearch" value="Search" />
        
        <button class="btn bt-action seconddata" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
      </div>
</div>

@include('rfq.tablerfqaudit')


@endsection

@section('scripts')

<script>
    $(document).on('click','.pagination a', function(e){
        e.preventDefault();

        //alert('123');
        var page = $(this).attr('href').split('?page=')[1];
        var ponbr = document.getElementById("ponbr").value;

        //console.log(page);
        getData(page,ponbr);

    });

    function getData(page,ponbr){
        $.ajax({
            url: '/rfqauditsearch?page='+ page+'&nbr='+ponbr,
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

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqauditsearch") }}",
          data:{
            nbr : ponbr,
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

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqauditsearch") }}",
          data:{
            nbr : ponbr,
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
</script>
@endsection
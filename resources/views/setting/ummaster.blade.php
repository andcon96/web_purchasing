@extends('layout.layout')

@section('menu_name','Item Conversion Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Item Conversion Maintenance</li>
</ol>
@endsection

@section('content')

<!-- Page Heading -->
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

<div class=" d-flex"  style="margin-bottom:10px">
    <form method="get" action='/loadum'>
        {{ csrf_field() }}
        <button type="submit" id="btnload" class="btn bt-action" style="width:250px;" >Load UM</button>
    </form>  
        <label for="suppcode" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('UM Code') }}</label>
        <div class="col-md-4 col-lg-2">
            <input id="umsearch" type="text" class="form-control" name="umsearch" autocomplete="off" 
            value="" autofocus>
        </div>
        <div class="offset-md-2 offset-lg-0">
            <input type="button" class="btn bt-ref" 
            id="btnsearch" value="Search" style="margin-left:15px;" />
        </div>
        <div class="offset-md-0 offset-lg-0">
            <button class="btn bt-ref" id='btnrefresh' style="font-size:18px; margin-left: 10px"><i class="fa fa-sync"></i></button>
        </div>
</div>

@include('setting.tableummaster')



@endsection


@section('scripts')

<script type="text/javascript">
    $(document).ready(function() {
          $('form').on("submit",function(){
            $('#loader').removeClass('hidden')
            document.getElementById('btnloading').style.display = '';
            document.getElementById('btnsearch').style.display = 'none';
            document.getElementById('btnrefresh').style.display = 'none';
          });
    });

    $(document).on('click','.pagination a', function(e){
          e.preventDefault();

          //alert('123');
          var page = $(this).attr('href').split('?page=')[1];

          //console.log(page);
          getData(page);

    });

    function getData(page){
        $.ajax({
            url: '/ummastermenu?page='+ page,
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

      var um = document.getElementById("umsearch").value;

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("ummastersearch") }}",
          data:{
            um : um,
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

      var um = "";

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("ummastersearch") }}",
          data:{
            um : um,
          },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
          success:function(data){
            //$('tbody').html(data);
            document.getElementById('umsearch').value = '';
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
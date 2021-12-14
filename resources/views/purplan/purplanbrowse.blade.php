@extends('layout.layout')

@section('menu_name','Purchase Plan List')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
  <li class="breadcrumb-item active">Purchase Plan List</li>
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

<div class="form-group row">
      <label for="rfnumber" class="col-md-2 col-lg-2 col-form-label">{{ __('RFP/RFQ No.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="rfnumber" type="text" class="form-control" name="rfnumber" 
          value="" autofocus>
      </div>
      <label for="suppcode" class="col-md-2 col-lg-2 col-form-label">{{ __('Supp. Code.') }}</label>
      <div class="col-md-4 col-lg-3">
          <input id="suppcode" type="text" class="form-control" name="suppcode" 
          value="" autofocus>
      </div>
</div>
<div class="form-group row">
      <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('Due Date From') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
      <label for="dateto" class="col-md-2 col-lg-2 col-form-label">{{ __('Due Date To') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
</div>
<div class="form-group row">
      <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('Status') }}</label>
      <div class="col-md-4 col-lg-3">
          <select id="ppstatus" name="ppstatus" class="form-control" autocomplete>
              <option value="">--Select Data--</option>
              <option value="New">New</option>
              <option value="Close">Close</option>
          </select>
      </div>
      <label for="dateto" class="col-md-2 col-lg-2 col-form-label"></label>
      <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
        <input type="button" class="btn bt-ref" 
        id="btnsearch" value="Search" />
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
      </div>
</div>

<!--Table Purplan Browse -->
@include("purplan.table-ppbrowse")

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
          $( "#purdate" ).datepicker({
              dateFormat : 'dd/mm/yy'
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
              url: '/pagination/viewppbrowse?page='+ page,
              type: "get",
              datatype: "html" 
          }).done(function(data){
                console.log('Page = '+ page);
                  //alert(data);    
                $(".tag-container").empty().html(data);
                //location.hash = page;
                //console.log(data);
          }).fail(function(jqXHR, ajaxOptions, thrownError){
                Swal.fire({
                    icon: 'error',
                    text: 'No Response From Server',
                })
          });
    }

    $('#btnsearch').on('click',function(){
        var value = document.getElementById("rfnumber").value;
        var code = document.getElementById("suppcode").value;
        var datefrom = document.getElementById("datefrom").value;
        var dateto = document.getElementById("dateto").value;
        var status = document.getElementById("ppstatus").value;
        jQuery.ajax({
            type : "get",
            url : "{{URL::to("ppbrowsesearch") }}",
            data:{
              value : value,
              code : code,
              datefrom : datefrom,
              dateto : dateto,
              status : status,
            },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
              //$("#dataTable").html(data);
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
        
        jQuery.ajax({
            type : "get",
            url : "{{URL::to("ppbrowsesearch") }}",
            data:{
              value : value,
              code : code,
              datefrom : datefrom,
              dateto : dateto,
              status : status,
            },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
              //$('tbody').html(data);
              console.log(data);
              $(".tag-container").empty().html(data);
              document.getElementById("rfnumber").value = '';
              document.getElementById("suppcode").value = '';
              document.getElementById("datefrom").value = '';
              document.getElementById("dateto").value = '';
              document.getElementById("ppstatus"). value = '';
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
        });
    });
    
    document.querySelector("html").classList.add('js');

      var fileInput  = document.querySelector( ".input-file" ),  
          button     = document.querySelector( ".input-file-trigger" ),
          the_return = document.querySelector(".file-return");
            
        
    
    
  </script>

@endsection
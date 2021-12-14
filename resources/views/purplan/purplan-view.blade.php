@extends('layout.layout')

@section('menu_name','Purchase Plan Create')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
  <li class="breadcrumb-item active">Purchase Plan Create</li>
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
      <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('Date From') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
      <label for="dateto" class="col-md-2 col-lg-2 col-form-label">{{ __('Date To') }}</label>
      <div class="col-md-4 col-lg-3">
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                  required autofocus autocomplete="off">
      </div>
      <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
        <input type="button" class="btn bt-ref " 
        id="btnsearch" value="Search" />
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
      </div>
</div>

<!--Table-->
<form action="/viewdetails" method="post">
  {{ csrf_field() }}
	@include('purplan.table-view')

  <button type="submit" class="btn bt-action" name='action' value="confirm" id='btnconf'>Next</button>
</form>


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

  function getData(page){

    $.ajax({
        url: '/pagination/fetch_data?page='+ page,
        type: "get",
        datatype: "html" 
    }).done(function(data){
          console.log('Page = '+ page);

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


  $('#update').submit(function(event) {

        var purdate = document.getElementById("purdate").value.split('/');
        var new_purdate = new Date(purdate[2].concat('-',purdate[1],'-',purdate[0]));
        var today = new Date();

        var qtypur = document.getElementById("purqty").value; 

        var reg = /^\d+$/;
        var regqty = /^(\s*|\d+\.\d*|\d+)$/;

        if(!regqty.test(qtypur)){
            Swal.fire({
                icon: 'error',
                text: 'Qty Purchase must be number',
            }) 
            return false;  
        }else if(qtypur <= 0){
            Swal.fire({
                icon: 'error',
                text: 'Qty Purchase must be greater than 0',
            })
            return false;
        }else if(new_purdate < today){
            Swal.fire({
                icon: 'error',
                text: 'Purchase Date cannot be earlier than today',
            })
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
      var value = document.getElementById("rfnumber").value;
      var code = document.getElementById("suppcode").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("purplansearch") }}",
          data:{
            value : value,
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
          url : "{{URL::to("purplansearch") }}",
          data:{
            value : value,
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
            document.getElementById("rfnumber").value = '';
            document.getElementById("suppcode").value = '';
            document.getElementById("datefrom").value = '';
            document.getElementById("dateto").value = '';
          },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
      });
  });

  $(document).ready(function(){
        $("#linkpo").select2({
      });
  });

</script>

@endsection
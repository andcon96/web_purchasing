@extends('layout.layout')

@section('menu_name','Supplier Inventory Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Supplier Inventory Maintenance</li>
</ol>
@endsection

@section('content')

<div class=""> <!-- <div class="card shadow mb-4"> backup-->
  <div class="row"> <!-- <div class="card-body"> -->

    <div class="table-responsive col-lg-12 col-md-12 tag-container">
        
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session()->has('added'))
        <div class="alert alert-success  alert-dismissible fade show"  role="alert">
            {{ session()->get('updated') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

  </div>
</div>

<!-- ===============================search======================================== -->

        <div class="form-group row">
          <label for="itemnumber" class="col-md-1 col-lg-2 col-form-label">{{ __('Item Number') }}</label>
            <div class="col-md-2">
              <input id="itemnumber" type="text" class="form-control" name="itemnumber" value="" autofocus>
            </div>
          <!-- <label for="supp" class="col-md-1.5 col-form-label text-md-left">{{ __('Supplier') }}</label>
            <div class="col-md-2">
              <input id="supp" type="text" class="form-control" name="supp" value="" autofocus>
            </div> -->
            <div class="col-md-2">
              <input type="button" class="btn bt-ref" id="btnsearch" value="Search" />
            </div>
            <div class="col-md-2">
              <button  class="btn bt-ref col-md-6 col-lg-12 col-form-label" style="object-position: right;" data-toggle="modal" data-target="#createModal">Create Relation</button>
            </div>
        </div>
        @include('setting.tbsuppinv')
<!-- ===============================search======================================== -->
        
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/delete" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

            <input type="hidden" name="temp_id" id="temp_id" value="">

            <div class="container">
              <div class="row">
                Are you sure you want to delete:&nbsp; <strong><a name="temp_thist" id="temp_thist"></a></strong> &nbsp;?    
              </div>
            </div>
            
        </div>
      
        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger bt-action" id="d_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="createModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Relation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

           <form class="form-horizontal" action="/prosessupp" method="post">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="itempart" class="col-md-4 col-form-label text-md-right">{{ __('Item Number') }}</label>
                    <div class="col-md-7">
                       <select id="itempart" class="form-control role" name="itempart" style="font-size:16px;" required autofocus>
                          <option value="" style="font-size:16px !important;"> Select Data </option>
                          @foreach($itemmstr as $show)
                            <option value = '{{$show->xitemreq_part}}' style="font-size:16px !important;">
                              {{$show->xitemreq_part.' - '.$show->xitemreq_desc}}</option>
                          @endforeach
                           @if ($errors->has('itempart'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itempart') }}</strong>
                            </span>
                        @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                  <label for="alrtsupp" class="col-md-4 col-form-label text-md-right">{{ __('Supplier') }}</label>
                  <div class="col-md-7">
                     <select id="alrtsupp" class="form-control" name="alrtsupp"  required autofocus>
                          <option value=""> Select Data </option>
                          @foreach($supp as $supp)
                            <option value="{{ $supp->xalert_supp }}"> {{$supp->xalert_supp}} </option>
                          @endforeach
                        </select>
                  </div>
                </div> 
                <div class="modal-footer">
                  <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
                  <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                  </button>
                 </div>
          </form>
    </div>
  </div>
</div>


<script type="text/javascript">
   $(document).on('click','.deletesupp',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var trid = $(this).data('id');
     var trhist = $(this).data('role');

     document.getElementById("temp_id").value = trid;
     document.getElementById("temp_thist").innerHTML = trhist;

     });

</script>


<!-- ===============================Search Script================================ -->
<script type="text/javascript">

  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];

      //console.log(page);
      getData(page);

  });

      function getData(page){

        $.ajax({
            url: '/pagination/item_search?page='+ page,
            type: "get",
            datatype: "html" 
        }).done(function(data){
              console.log('Page = '+ page);

              $(".tag-container").empty().html(data);
              //location.hash = page;
              //console.log(data);
        }).fail(function(jqXHR, ajaxOptions, thrownError){
              alert('No response from server');
        });
      }
  
      $('#btnsearch').on('click',function()
      {
        var itemnumber = document.getElementById("itemnumber").value;
        // var value = document.getElementById("supp").value;         
        jQuery.ajax
        ({
            type : "get",
            url : "{{URL::to("supp_search") }}",
            data:{
              item_search : itemnumber,
              // supp : value,
            },
            success:function(data){
              $('#dataTable').html(data);
              console.log(data);
              // $(".tag-container").empty().html(data);
            }
        });
      });


$(document).ready(function(){
        $("#itempart").select2({
          width : '100%'
        });

          $('form').on("submit",function(){
            document.getElementById('btnclose').style.display = 'none';
            document.getElementById('itempart').style.display = 'none';
            document.getElementById('btnloading').style.display = '';
            document.getElementById('e_btnconf').style.display = 'none';
            document.getElementById('e_btnloading').style.display = '';
            document.getElementById('d_btnclose').style.display = 'none';
            document.getElementById('d_btnconf').style.display = 'none';
            document.getElementById('d_btnloading').style.display = '';
          });
    });

</script>


@endsection

@section('script')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>

@endsection
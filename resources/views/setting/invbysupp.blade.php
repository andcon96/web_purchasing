@extends('layout.layout')

@section('menu_name','Inventory By Supplier')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Inventory By Supplier</li>
</ol>
@endsection

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style type="text/css">
    tbody{
        font-size: 14px;

    }

    h1{
      color: black !important;
    }

    thead{
        background-color: #4e73df;
        text-align: left;
        color:white !important;
    }

    label, .page-item{
        font-size:18px !important;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    .datarow{
        margin-bottom: 5px !important;
    }


    #dataTable th{
        padding: 7px 5px 7px 5px;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:18px;
        font-weight: 600;
        padding:5px 0px 5px 5px;
    }

    .bt-action{
      font-size: 16px;
      width: 120px;
      background-color:#4e73df;
      color:white;
    }
    
    .bt-cancel{
      font-size: 16px;
      width: 120px;
      background-color:#A9A9A9;
      color:white;
    }

    .bt-action:hover, .bt-cancel:hover{
      color:black;
    }

    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }

     @media only screen and (max-width: 992px) and (min-width: 768px){
        .seconddata{
            margin-top:15px;
        }
    }

    @media only screen and (max-width: 768px) {
        #btnsearch{
            margin-top:15px;
        }
    }

</style>

<div class=""> <!-- <div class="card shadow mb-4"> backup-->
  <div class="row"> <!-- <div class="card-body"> -->

@if (count($errors) > 0)
  <div class="alert alert-danger">
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif

<div class="table-responsive col-lg-12 col-md-12 tag-container">
@if(session()->has('deleted'))
    <div class="alert alert-success">
        {{ session()->get('deleted') }}
    </div>
@endif
</div>

<div class="table-responsive col-lg-12 col-md-12 tag-container">
  @if(session()->has('added'))
    <div class="alert alert-success">
        {{ session()->get('added') }}
    </div>
@endif
</div>

<div class="table-responsive col-lg-12 col-md-12 tag-container">
@if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
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
            <div class="col-md-2">
              <input type="button" class="btn btn-info" id="btnsearch" value="Search" />
            </div>
        </div>
        @include('setting.tbinvbysupp')
<!-- ===============================search======================================== -->
        
      <!--Table-->

<!--     <div class="table-responsive col-lg-12 col-md-12 tag-container">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
             <th>Item Number</th>
             <th>Item Desc</th>
             <th>Supplier</th>
             <th>On Hand</th>
             <th width="7%">Delete</th>
          </tr>
        </thead>
        <tbody>         
          @foreach ($invbysupp as $show)
          <tr>
              <td>{{ $show->xitem_nbr }}</td> 
              <td>{{ $show->xitem_desc}}</td>
              <td>{{ $show->xsupp}}</td>
              <td>{{ $show->xinv_sft_stock}}</td>
              <td data-title="Delete" class="action">        
                <a href="" class="deletesupp" data-id="{{$show->xitem_nbr}}" data-role="{{$show->xitem_nbr}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
              </td>
          </tr>
          @endforeach     
        </tbody>
      </table>
    </div><br>
  </div>
</div> -->

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
          <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger bt-action" id="d_btnconf">Confirm</button>
          <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Relation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
  -->
 <!--           <form action="/proses" method="post">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="itempart" class="col-md-4 col-form-label text-md-right">{{ __('Item Number') }}</label>
                    <div class="col-md-7">
                       <select id="itempart" class="form-control" name="itempart"  required autofocus>
                          <option value=""> Select Data </option>
                          @foreach($itemmstr as $show)
                            <option value="{{ $show->xitem_part }}"> {{$show->xitem_part}} </option>
                          @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                  <label for="alrtsupp" class="col-md-4 col-form-label text-md-right">{{ __('Supplier') }}</label>
                  <div class="col-md-3">
                     <select id="alrtsupp" class="form-control" name="alrtsupp"  required autofocus>
                          <option value=""> Select Data </option>
                          @foreach($supp as $supp)
                            <option value="{{ $supp->xalert_supp }}"> {{$supp->xalert_supp}} </option>
                          @endforeach
                        </select>
                  </div>
                </div> 
                <div class="modal-footer">
                  <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success bt-action" id="btnconf">Confirm</button>
                  <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                  </button>
                 </div>
          </form>
    </div>
  </div>
</div> -->



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
<script>

  $(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];

      //console.log(page);
      getData(page);

  });

  function getData(page){
    $.ajax({
        url: '/po/fetch_data?page='+ page,
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

      var itemnumber = document.getElementById("itemnumber").value;
      // var supp = document.getElementById("supp").value;

      // var check_supplier = document.getElementById("supplier");
      // var supplier = "";
      // if(check_supplier){
      //     supplier = check_supplier.value;
      // }

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("supplsearch") }}",
          data:{
            item_search : itemnumber,
            // supplier : supp,
          },
          success:function(data){
            $('#dataTable').html(data);
            console.log(data);
            // $(".tag-container").empty().html(data);
          }
      });

  });

  // $(document).ready(function(){
  //       $("#linkpo").select2({
  //     });
  // });

</script>
<!-- ===============================Search Script================================ -->



@endsection
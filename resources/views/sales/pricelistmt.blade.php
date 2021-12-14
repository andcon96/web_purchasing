@extends('layout.layout')

@section('menu_name','Price List Maintenance')

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<style type="text/css">

    .input-file-container {
      position: relative;
      width: 225px;
    } 
    .js .input-file-trigger {
      display: block;
      padding: 14px 45px;
      background: #18695A;
      color: #fff;
      font-size: 1em;
      transition: all .4s;
      cursor: pointer;
    }
    .js .input-file {
      position: absolute;
      top: 0; left: 0;
      width: 225px;
      opacity: 0;
      padding: 14px 0;
      cursor: pointer;
    }
    .js .input-file:hover + .input-file-trigger,
    .js .input-file:focus + .input-file-trigger,
    .js .input-file-trigger:hover,
    .js .input-file-trigger:focus {
      background: #12433A;
      color: #FFF;
    }

    .file-return {
      margin: 0;
    }
    .file-return:not(:empty) {
      margin: 1em 0;
    }
    .js .file-return {
      font-style: italic;
      font-size: .9em;
      font-weight: bold;
    }
    .js .file-return:not(:empty):before {
      content: "Selected file: ";
      font-style: normal;
      font-weight: normal;
    }

    tbody{
        font-size: 14px;

    }

    label, .page-item{
        font-size:16px !important;
    }

    h1{
        color: black !important;
    }

    thead{
        background-color: #4e73df;
        text-align: left;
        color:white !important;
    }

    th{
        font-size: 18px;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:16px;
        font-weight: 600;
        padding: 5px 10px 5px 10px;
    }


    .bt-action{
      font-size: 16px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }

    label, .page-item{
        font-size:16px !important;
    }
  
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }

    .select2-results__option { 
      font-size: 16px;
    }

    .select2-selection__rendered {
      line-height: 31px !important;
      font-size:16px !important;
    }
    .select2-container .select2-selection--single {
        height: 36px !important;
      font-size:16px !important;
    }
    .select2-selection__arrow {
        height: 36px !important;
    }

    @media only screen and (max-width: 767px) {
        
    .seconddata{
        margin-top:15px;
        margin-left:12px;
        }
    }

</style>
  <!-- Page Heading -->
        @if(session()->has('updated'))
            <div class="alert alert-success">
                {{ session()->get('updated') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif


 <div class="form-group row">
    <button class="btn bt-action deleteModal" data-toggle="modal" data-target="#createModal" style="margin-left:10px;">Create New</button>
</div>
 @include('sales.tbpricelistmt')


<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Price List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="POST" action="/pricelist/createnew">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="custcode" class="col-md-3 col-form-label text-md-right">{{ __('Customer Code') }}</label>
                    <div class="col-md-7">
                        <input id="custcode" type="text" class="form-control" name="custcode" value="" required autofocus>
                        @if ($errors->has('custcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('custcode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemcode" class="col-md-3 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-7 col-lg-8">
                      <select id="itemcode" class="form-control role" name="itemcode" style="font-size:16px;" required autofocus>
                        <option value = '' style="font-size:16px !important;"> Choose itemcode </option>
                          @foreach($itemmstr as $show)
                            <option value = '{{$show->xitem_part}}' style="font-size:16px !important;">
                              {{$show->xitem_part.' - '.$show->xitem_desc}}</option>
                          @endforeach
                           @if ($errors->has('itemcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemcode') }}</strong>
                            </span>
                        @endif
                      </select>
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="custtype" class="col-md-3 col-form-label text-md-right">{{ __('Customer Type') }}</label>
                    <div class="col-md-7">
                        <input id="custtype" type="text" class="form-control" name="custtype" value="" required="" required autofocus>
                        @if ($errors->has('custtype'))
                            <span class="help-block">
                                <strong>{{ $errors->first('custtype') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="startdate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Start Date') }}</label>
                    <div class="col-md-7 col-lg-5">
                        <input id="startdate" type="date" class="form-control startdate" name="startdate" 
                        value=""  autofocus>
                        @if ($errors->has('startdate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('startdate') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="minorder" class="col-md-3 col-form-label text-md-right">{{ __('Minimum Order') }}</label>
                    <div class="col-md-7">
                        <input id="minorder" type="text" class="form-control" name="minorder" value=""  autofocus>
                        @if ($errors->has('minorder'))
                            <span class="help-block">
                                <strong>{{ $errors->first('minorder') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="listprice" class="col-md-3 col-form-label text-md-right">{{ __('List Price') }}</label>
                    <div class="col-md-7">
                        <input id="listprice" type="text" class="form-control" name="listprice" value=""  autofocus>
                        @if ($errors->has('listprice'))
                            <span class="help-block">
                                <strong>{{ $errors->first('listprice') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="discount" class="col-md-3 col-form-label text-md-right">{{ __('Discount') }}</label>
                    <div class="col-md-7">
                        <input id="discount" type="text" class="form-control" name="discount" value=""  autofocus>
                        @if ($errors->has('discount'))
                            <span class="help-block">
                                <strong>{{ $errors->first('discount') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                 <div class="form-group row">
                    <label for="endeffdate" class="col-md-3 col-form-label text-md-right">{{ __('End Effective Date') }}</label>
                    <div class="col-md-7">
                        <input id="endeffdate" type="text" class="form-control" name="endeffdate" value=""  autofocus>
                        @if ($errors->has('endeffdate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('endeffdate') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id='btnclose' data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success bt-action" id='btnconf'>Confirm</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
      </form>

    </div>
  </div>
</div>


<!-- ==============================Edit Modal================================ -->

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Datax</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" role="form" method="POST" action="/sales/update">
            {{ method_field('patch') }}
            {{ csrf_field() }}

            <input type="" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="e_cuscode" class="col-md-3 col-form-label text-md-right">{{ __('Customer Codex') }}</label>
                    <div class="col-md-7">
                        <input id="e_cuscode" type="text" class="form-control" name="e_cuscode" value="{{ old('xcust_code') }}" required autofocus>
                     
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="e_custtype" class="col-md-3 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-7">
                        <input id="e_custtype" type="text" class="form-control" name="e_custtype" value="" required autofocus>
                        @if ($errors->has('e_custtype'))
                            <span class="help-block">
                                <strong>{{ $errors->first('e_custtype') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Confirm</button>
              <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

<!-- ==============================Edit Modal================================ -->

  
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Price List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/pricelist/delete" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

            <input type="" name="delete_id" id="delete_id" value="">

            <div class="container">
              <div class="row">
                Are you sure you want to delete
                &nbsp; <strong><a name="temp_cust" id="temp_cust"></a></strong>?    
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

<script type="text/javascript">

    $(document).on('click','.editModal',function(){ // Click to only happen on announce links
     var uid    = $(this).data('id');
     var custcd = $(this).data('custcode');
     var custtp = $(this).data('custtype');

     document.getElementById("edit_id").value = uid;
     document.getElementById("e_cuscode").value = custcd;
     document.getElementById("e_custtype").value = custtp;
     });





    $(document).on('click','.deleteModal',function(){
       var uid = $(this).data('id');
       var custcd = $(this).data('xcust_code');
       var custtp = $(this).data('xcust_type');

       document.getElementById('delete_id').value = uid;
       document.getElementById('temp_cust').value = custcd;
       // document.getElementById('temp_supp').innerHTML = custtp;

    });


    $(document).ready(function(){
        $("#itemcode").select2({
          width : '100%'
        });

          $('form').on("submit",function(){
            document.getElementById('btnclose').style.display = 'none';
            document.getElementById('itemcode').style.display = 'none';
            document.getElementById('btnloading').style.display = '';
            document.getElementById('e_btnclose').style.display = 'none';
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
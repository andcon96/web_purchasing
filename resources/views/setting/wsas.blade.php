@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">WSA Qxtend Control</li>
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

    <div class="table-responsive col-lg-12 col-md-12">
              <form action="/wsasupdate" method="post" id="submit">
            {{ csrf_field() }}

            <div class="modal-header">
            </div>

            <div class="modal-body">

                <input type="hidden" name="qxenable" id="qxenable" value="{{$data->qx_enable}}">

                <div class="form-group row">
                    <label for="domain" class="col-md-3 col-form-label text-md-right">{{ __('Domain') }}</label>
                    <div class="col-md-7">
                        <input id="domain" type="text" class="form-control" name="domain" autocomplete="off"
                        value="{{$data->wsas_domain}}" autofocus> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="wsaurl" class="col-md-3 col-form-label text-md-right">{{ __('WSA URL') }}</label>
                    <div class="col-md-7">
                        <input id="wsaurl" type="text" class="form-control" autocomplete="off" name="wsaurl" value="{{$data->wsas_url}}">
                        <span id="errorcur" style="color:red"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="wsapath" class="col-md-3 col-form-label text-md-right">{{ __('WSA Path') }}</label>
                    <div class="col-md-7">
                        <input id="wsapath" type="text" class="form-control" autocomplete="off" name="wsapath" value="{{$data->wsas_path}}">
                        <span id="errorcur" style="color:red"></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cb_qxenable" class="col-md-6 col-form-label text-md-right">{{ __('Qxtend Enabled') }}</label>
                    <label class="switch" for="cb_qxenable" style="margin-left:10px">
                          <input type="checkbox" id="cb_qxenable" name="cb_qxenable" value="Yes" />
                          <div class="slider round"></div>
                    </label>    
                </div>

                <div class="form-group row" id='rowqxurl'>
                    <label for="qxurl" class="col-md-3 col-form-label text-md-right">{{ __('QX URL') }}</label>
                    <div class="col-md-7">
                        <input id="qxurl" type="text" class="form-control" name="qxurl" autocomplete="off"
                        value="{{$data->qx_url}}">
                    </div>
                </div>
                <div class="form-group row" id='rowqxpath'>
                    <label for="qxpath" class="col-md-3 col-form-label text-md-right">{{ __('QX Path') }}</label>
                    <div class="col-md-7">
                        <input id="qxpath" type="text" class="form-control" name="qxpath" value="{{$data->qx_path}}" autofocus autocomplete="off">
                        <span id="errorpo" style="color:red"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

            </div>

@endsection

@section('scripts')

<script type="text/javascript">

    $( document ).ready(function() {
        var qxtend = document.getElementById('qxenable').value;

        if(qxtend == "1"){
            document.getElementById("cb_qxenable").checked = true;  
            document.getElementById("qxurl").readOnly = false;
            document.getElementById("qxpath").readOnly = false;
        }else{
            document.getElementById("cb_qxenable").checked = false;
            document.getElementById("qxurl").readOnly = true;
            document.getElementById("qxpath").readOnly = true;
        }
    });

    $("#cb_qxenable").on("click",function(){
        let data = $(this).is(":checked");
        
        if(data === true){
            document.getElementById('qxenable').value = '1';
            document.getElementById("cb_qxenable").checked = true;  
            document.getElementById("qxurl").readOnly = false;
            document.getElementById("qxpath").readOnly = false;
            document.getElementById("qxurl").required = true;
            document.getElementById("qxpath").required = true;
        }else{
            document.getElementById('qxenable').value = '0';
            document.getElementById("cb_qxenable").checked = false;
            document.getElementById("qxurl").readOnly = true;
            document.getElementById("qxpath").readOnly = true;
            document.getElementById("qxurl").required = false;
            document.getElementById("qxpath").required = false;
            document.getElementById("qxurl").value = '';
            document.getElementById("qxpath").value = '';
        }
    });

    $('#submit').on("submit",function(){
        document.getElementById('btnconf').style.display = 'none';
        document.getElementById('btnloading').style.display = '';
    });

</script>
@endsection
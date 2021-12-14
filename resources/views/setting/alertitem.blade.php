@extends('layout.layout')

@section('menu_name','Item Alert Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Item Alert Maintenance</li>
</ol>
@endsection

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
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
        font-size:18px;
        font-weight: 600;
        padding: 5px 10px 5px 10px;
    }


    .bt-action{
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
  
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }


    @media only screen and (max-width: 800px) {
        
    /* Force table to not be like tables anymore */
    #dataTable table, 
    #dataTable thead, 
    #dataTable tbody, 
    #dataTable th, 
    #dataTable td, 
    #dataTable tr { 
        display: block; 
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    #dataTable thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    #dataTable tr { border: 1px solid #ccc; }

    #dataTable td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 40%; 
        white-space: normal;
        text-align:left;
    }

    #dataTable td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align:left;
        font-weight: bold;
    }

    /*
    Label the data
    */
    #dataTable td:before { 
        content: attr(data-title); 
        vertical-align: top;
        padding: 6px 0px 0px 0px;
    }
}   
</style>
    <!-- Page Heading -->
    @if(session()->has('updated'))
        <div class="alert alert-success">
            {{ session()->get('updated') }}
        </div>
    @endif
    <button  class="btn btn-info bt-action deleteUser" style="margin-left:10px" data-toggle="modal" data-target="#createModal">
    Create Alert</button>
    <br><br>
        <div class="table-responsive col-lg-12 col-md-12">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
             <th>Supplier</th>
             <th>Active</th>  
             <th>Item Group</th>
             <th>Item Type</th>  
             <th width="8%">Edit</th>
          </tr>
           </thead>
            <tbody>         
                @foreach ($alert as $show)
                  <tr>
                    <td>{{ $show->xalertitem_supp }}</td>
                    <td>{{ $show->xalertitem_active }}</td>
                    <td>{{ $show->xalertitem_group }}</td>
                    <td>{{ $show->xalertitem_type }}</td>
                    <td>
                      <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->xalertitem_id}}" data-role="{{$show->xalertitem_supp}}"><i class="fas fa-edit"></i></a>
                    </td>
                  </tr>
                @endforeach                      
            </tbody>
          </table>
        </div>
    
   
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="{{route('alertitem.update', 'test')}}" method="post">
            {{ method_field('patch') }}
            {{ csrf_field() }}

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7">
                        <input id="supname" type="text" class="form-control" name="supname" value="" disabled autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="active" class="col-md-3 col-form-label text-md-right">{{ __('Active') }}</label>
                    <div class="col-md-7">
                        <select id="active" name="active" style="margin-top:8px">
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                        @if ($errors->has('active'))
                            <span class="help-block">
                                <strong>{{ $errors->first('active') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemcode" class="col-md-3 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-7">
                        <input id="itemcode" type="text" class="form-control" name="itemcode" value="" autofocus>
                        @if ($errors->has('itemcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemcode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemtype" class="col-md-3 col-form-label text-md-right">{{ __('Item Type') }}</label>
                    <div class="col-md-7">
                        <input id="itemtype" type="text" class="form-control" name="itemtype" value="" autofocus>
                        @if ($errors->has('itemtype'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemtype') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemgroup" class="col-md-3 col-form-label text-md-right">{{ __('Item Group') }}</label>
                    <div class="col-md-7">
                        <input id="itemgroup" type="text" class="form-control" name="itemgroup" value="" autofocus>
                        @if ($errors->has('itemgroup'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemgroup') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                
                <div class="form-group row">
                    <label for="safetystock" class="col-md-3 col-form-label text-md-right">{{ __('Safety Stock') }}</label>
                    <div class="col-md-7">
                        <input id="safetystock" type="text" class="form-control" name="safetystock" value="" autofocus>
                        @if ($errors->has('safetystock'))
                            <span class="help-block">
                                <strong>{{ $errors->first('safetystock') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                  <h5><center><strong>Expired Item Alert Days</strong></center></h5>
                  <hr>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays1" type="text" class="form-control" placeholder="Days"name="alertdays1" value="{{ old('alert1') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail1" type="text" class="form-control" placeholder="Email"name="alertemail1" value="{{ old('alert1') }}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays2" type="text" class="form-control" placeholder="Days"name="alertdays2" value="{{ old('alert2') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail2" type="text" class="form-control" placeholder="Email"name="alertemail2" value="{{ old('alert2') }}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays3" type="text" class="form-control" placeholder="Days"name="alertdays3" value="{{ old('alert3') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail3" type="text" class="form-control" placeholder="Email"name="alertemail3" value="{{ old('alert3') }}"  autofocus>
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

<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Alert</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="POST" action="/alertitem/createnew">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7">
                        <select id="supname" type="text" class="form-control" name="supname" value="" required autofocus> 
                            @foreach($supp as $supp)
                                <option value="{{$supp->supp_id}}">{{$supp->supp_id}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="active" class="col-md-3 col-form-label text-md-right">{{ __('Active') }}</label>
                    <div class="col-md-7">
                        <select id="active" name="active" style="margin-top:8px">
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                        @if ($errors->has('active'))
                            <span class="help-block">
                                <strong>{{ $errors->first('active') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemcode" class="col-md-3 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-7">
                        <input id="itemcode" type="text" class="form-control" name="itemcode" value="" autofocus>
                        @if ($errors->has('itemcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemcode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemtype" class="col-md-3 col-form-label text-md-right">{{ __('Item Type') }}</label>
                    <div class="col-md-7">
                        <input id="itemtype" type="text" class="form-control" name="itemtype" value="" autofocus>
                        @if ($errors->has('itemtype'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemtype') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemgroup" class="col-md-3 col-form-label text-md-right">{{ __('Item Group') }}</label>
                    <div class="col-md-7">
                        <input id="itemgroup" type="text" class="form-control" name="itemgroup" value="" autofocus>
                        @if ($errors->has('itemgroup'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemgroup') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>


                <div class="form-group row">
                    <label for="safetystock" class="col-md-3 col-form-label text-md-right">{{ __('Safety Stock') }}</label>
                    <div class="col-md-7">
                        <input id="safetystock" type="text" class="form-control" name="safetystock" value="" autofocus>
                        @if ($errors->has('safetystock'))
                            <span class="help-block">
                                <strong>{{ $errors->first('safetystock') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                  <h5><center><strong>Alert Exp Days</strong></center></h5>
                  <hr>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays1" type="text" class="form-control" placeholder="Days"name="alertdays1" value="{{ old('alert1') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail1" type="text" class="form-control" placeholder="Email"name="alertemail1" value="{{ old('alert1') }}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays2" type="text" class="form-control" placeholder="Days"name="alertdays2" value="{{ old('alert2') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail2" type="text" class="form-control" placeholder="Email"name="alertemail2" value="{{ old('alert2') }}"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays3" type="text" class="form-control" placeholder="Days"name="alertdays3" value="{{ old('alert3') }}"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail3" type="text" class="form-control" placeholder="Email"name="alertemail3" value="{{ old('alert3') }}"  autofocus>
                    </div>
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
</div>

<script type="text/javascript">
    $(document).on('click','.editUser',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var uid = $(this).data('id');
     var supp = $(this).data('role');

     document.getElementById("edit_id").value = uid;
     document.getElementById("supname").value = supp;

     jQuery.ajax({
          type : "get",
          url : "{{URL::to("alertitemsearch") }}",
          data:{
            search : uid,
          },
          success:function(data){
            //alert(data);
            document.getElementById("itemcode").value = data[0]['xalertitem_code'];
            document.getElementById("itemtype").value = data[0]['xalertitem_type'];
            document.getElementById("itemgroup").value = data[0]['xalertitem_group'];
            document.getElementById("safetystock").value = data[0]['xalertitem_sfty_stock'];
            document.getElementById("alertdays1").value = data[0]['xalertitem_day1'];
            document.getElementById("alertdays2").value = data[0]['xalertitem_day2'];
            document.getElementById("alertdays3").value = data[0]['xalertitem_day3'];
            document.getElementById("alertemail1").value = data[0]['xalertitem_email1'];
            document.getElementById("alertemail2").value = data[0]['xalertitem_email2'];
            document.getElementById("alertemail3").value = data[0]['xalertitem_email3'];
          }
      });


     });

    $(document).ready(function() {
          $('form').on("submit",function(){
              document.getElementById('btnclose').style.display = 'none';
              document.getElementById('btnconf').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
              document.getElementById('e_btnclose').style.display = 'none';
              document.getElementById('e_btnconf').style.display = 'none';
              document.getElementById('e_btnloading').style.display = '';
          });
      });
</script>

@endsection

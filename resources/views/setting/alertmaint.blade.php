@extends('layout.layout')

@section('menu_name','Alert Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Alert Maintenance</li>
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

<div class="d-flex mb-3">
    <form method="post" id="loadsupp" action='/loadsupp'>
        {{ csrf_field() }}
        <button type="submit" class="btn bt-action" style="width:200px;" >Load Supplier</button>
    </form>  
        <label for="suppcode" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Supplier Code') }}</label>
        <div class="col-md-4 col-lg-2">
            <input id="suppcode" type="text" class="form-control" name="suppcode" autocomplete="off" 
            value="" autofocus>
        </div>
        <div class="offset-md-2 offset-lg-0">
            <input type="button" class="btn bt-ref" 
            id="btnsearch" value="Search" style="margin-left:15px;" />
        </div>
        <div class="offset-md-0 offset-lg-0">
            <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px"><i class="fa fa-sync"></i></button>
        </div>
</div>

@include('setting.tablesupplier')
    
   
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{route('alertcreate.update', 'test')}}" id="editsupp" method="post">

        {{ method_field('patch') }}
        {{ csrf_field() }}
        
        <input type="hidden" name="edit_id" id="edit_id">

        <div class="modal-body">
                <div class="form-group row">
                    <label for="supname" class="col-md-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7">
                        <input id="supname" type="text" class="form-control" name="supname" value="" disabled>
                        @if ($errors->has('supname'))
                            <span class="help-block">
                                <strong>{{ $errors->first('supname') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="active" class="col-md-3 col-form-label text-md-right">{{ __('Active') }}</label>
                    <div class="col-md-7">
                        <select id="active" name="active" class="form-control">
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
                    <label for="poapprove" class="col-md-3 col-form-label text-md-right">{{ __('PO Approve') }}</label>
                    <div class="col-md-7">
                        <select id="poapprove" name="poapprove" class="form-control">
                          <option value="Yes">Yes</option>
                          <option value="No">No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="emailpur" class="col-md-3 col-form-label text-md-right">{{ __('Purchasing') }}</label>
                    <div class="col-md-7">
                        <input id="emailpur" type="text" class="form-control" placeholder="Email,Email,Email" autocomplete="off" name="emailpur" value="" autofocus>
                        @if ($errors->has('emailpur'))
                            <span class="help-block">
                                <strong>{{ $errors->first('emailpur') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-md-3 col-form-label text-md-right">{{ __('Phone Number') }}</label>
                    <div class="col-md-7">
                        <input id="phone" type="text" class="form-control" placeholder="+628....." autocomplete="off" name="phone" value="" autofocus>
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                  <h5><center><strong>Alert Days</strong></center></h5>
                  <hr>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays1" type="text" class="form-control" placeholder="Days"name="alertdays1" value="{{ old('alert1') }}" autocomplete="off" autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail1" type="text" class="form-control" placeholder="Email,Email,Email"name="alertemail1" value="{{ old('alert1') }}" autocomplete="off"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays2" type="text" class="form-control" placeholder="Days"name="alertdays2" value="{{ old('alert2') }}" autocomplete="off" autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail2" type="text" class="form-control" placeholder="Email,Email,Email"name="alertemail2" value="{{ old('alert2') }}" autocomplete="off"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays3" type="text" class="form-control" placeholder="Days"name="alertdays3" value="{{ old('alert3') }}" autocomplete="off"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail3" type="text" class="form-control" placeholder="Email,Email,Email"name="alertemail3" value="{{ old('alert3') }}" autocomplete="off"  autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays4" type="text" class="form-control" placeholder="Days"name="alertdays4" value="{{ old('alert4') }}" autocomplete="off"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail4" type="text" class="form-control" placeholder="Email,Email,Email"name="alertemail4" value="{{ old('alert4') }}" autocomplete="off" autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="alertdays5" type="text" class="form-control" placeholder="Days"name="alertdays5" value="{{ old('alert5') }}" autocomplete="off"  autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="alertemail5" type="text" class="form-control" placeholder="Email,Email,Email"name="alertemail5" value="{{ old('alert5') }}" autocomplete="off" autofocus>
                    </div>
                </div>

                <div class="form-group">
                  <h5><center><strong>Idle Days</strong></center></h5>
                  <hr>
                </div>

                <div class="form-group row">
                    <div class="offset-md-1 col-md-3">
                        <input id="idledays" type="text" class="form-control" placeholder="Days"name="idledays" value="{{ old('idledays') }}" autocomplete="off" autofocus> 
                    </div>
                    <div class="col-md-7">
                        <input id="idleemail" type="text" class="form-control" placeholder="Email,Email,Email"name="idleemail" value="{{ old('idleemail') }}" autocomplete="off" autofocus>
                    </div>
                </div>
            </div>
      
        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
              <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection


@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#loadsupp').on("submit",function(){
            $('#loader').removeClass('hidden')
        });

        $('#editsupp').on("submit",function(){
            document.getElementById('e_btnclose').style.display = 'none';
            document.getElementById('e_btnconf').style.display = 'none';
            document.getElementById('e_btnloading').style.display = '';
        });

    });



    $(document).on('click','.editUser',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var uid = $(this).data('id');
     var supp = $(this).data('role');
     var nama = $(this).data('supp');

     document.getElementById("edit_id").value = uid;
     document.getElementById("supname").value = supp.concat(' - ',nama);

     jQuery.ajax({
          type : "get",
          url : "{{URL::to("alertsearch") }}",
          data:{
            search : uid,
          },
          success:function(data){
            //alert(data);

            if(data[0]['xalert_active'] == 'Yes'){
                document.getElementById("active").selectedIndex = "0";                
            }else{
                document.getElementById("active").selectedIndex = "1";
            }

            if(data[0]['xalert_po_app'] == 'Yes'){
                document.getElementById("poapprove").selectedIndex = "0";                
            }else{
                document.getElementById("poapprove").selectedIndex = "1";
            }

            document.getElementById("emailpur").value = data[0]['xalert_not_pur'];
            document.getElementById("alertdays1").value = data[0]['xalert_day1'];
            document.getElementById("alertdays2").value = data[0]['xalert_day2'];
            document.getElementById("alertdays3").value = data[0]['xalert_day3'];
            document.getElementById("alertdays4").value = data[0]['xalert_day4'];
            document.getElementById("alertdays5").value = data[0]['xalert_day5'];
            document.getElementById("alertemail1").value = data[0]['xalert_email1'];
            document.getElementById("alertemail2").value = data[0]['xalert_email2'];
            document.getElementById("alertemail3").value = data[0]['xalert_email3'];
            document.getElementById("alertemail4").value = data[0]['xalert_email4'];
            document.getElementById("alertemail5").value = data[0]['xalert_email5'];
            document.getElementById("idledays").value = data[0]['xalert_idle_days'];
            document.getElementById("idleemail").value = data[0]['xalert_idle_emails'];
            document.getElementById('phone').value = data[0]['xalert_phone'];
          }
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
            url: '/alertcreate?page='+ page,
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

      var suppcode = document.getElementById("suppcode").value;

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("suppmstrsearch") }}",
          data:{
            suppcode : suppcode,
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

      var suppcode = "";

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("suppmstrsearch") }}",
          data:{
            suppcode : suppcode,
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
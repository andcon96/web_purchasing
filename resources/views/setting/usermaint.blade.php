@extends('layout.layout')

@section('menu_name','Users Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
  <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
  <li class="breadcrumb-item active">User Maintenance</li>
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
<div class="alert alert-success  alert-dismissible fade show" role="alert">
  {{ session()->get('updated') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<ul>

  @if(count($errors) > 0)
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

</ul>

<input type="hidden" id="tmp_username" />
<input type="hidden" id="tmp_name" />

<div class="form-group row" style="margin-bottom:0px !important;margin-left:1px;">
  <div class="col-md-2">
    <button class="btn bt-action newUser mb-3" style="margin-left:10px;" data-toggle="modal" data-target="#createModal">
      Create User
    </button>
  </div>
  <label for="s_username" class="col-md-1 col-form-label">{{ __('Username') }}</label>
  <div class="col-md-2">
    <input id="s_username" type="text" class="form-control" name="s_username" autocomplete="off" autofocus>
  </div>

  <label for="s_name" class="col-md-1 col-form-label">{{ __('Name') }}</label>
  <div class="col-md-2">
    <input id="s_name" type="text" class="form-control" name="s_name" autocomplete="off" autofocus>
  </div>

  <div class="col-md-2 offset-md-1">
    <input type="button" class="btn bt-ref" id="btnsearch" value="Search" />
    <button class="btn bt-action" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
  </div>
</div>

<div class="table-responsive tag-container col-lg-12 col-md-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Role Type</th>
        <th>Status</th>
        <th width="7%">Edit</th>
        <th width="7%">Pass</th>
        <th width="7%">Active</th>
      </tr>
    </thead>
    <tbody>
    @include('setting.tableusermaint')
    </tbody>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
  </table>

</div>

<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="post" action="{{route('usermaint.update', 'test')}}">
        {{ method_field('patch') }}
        {{ csrf_field() }}

        <div class="modal-body">
          <div class="form-group row">
            <label for="username" class="col-md-3 col-form-label text-md-right">Username</label>
            <div class="col-md-5 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" autocomplete="off" maxlength="8" required autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="name" class="col-md-3 col-form-label text-md-right">Name</label>
            <div class="col-md-5">
              <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="role" class="col-md-3 col-form-label text-md-right">Role</label>
            <div class="col-md-7">
              <select id="role" class="form-control role" name="role" required autofocus>
                <option value=""> Select Data </option>
                <option value="Admin">Admin</option>
                <option value="Purchasing"> Internal </option>
                <option value="Supplier"> External </option>
              </select>
            </div>
          </div>
          <div class="form-group row" id="divDept" style="display: none;">
            <label for="deptselect" class="col-md-3 col-form-label text-md-right">Department</label>
            <div class="col-md-5">
              <select id="deptselect" class="form-control role" name="deptselect" required autofocus>
                @foreach($dept as $showdept)
                <option value="{{$showdept->xdept}}"> {{$showdept->xdept}} -- {{$showdept->xdept_desc}} </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="roletype" class="col-md-3 col-form-label text-md-right">Role Access</label>
            <div class="col-md-7">
              <select id="roletype" class="form-control roletype" name="roletype" required autofocus>
                <option value=""> Select Data </option>
              </select>
            </div>
          </div>
          <!--Supplier ID & Name Dispaly-->
          <div class="form-group row" id="supplierid" style="display:none;">
            <label for="suppid" class="col-md-3 col-form-label text-md-right">Supplier ID</label>
            <div class="col-md-5">
              <!--<input id="suppid" type="text" class="form-control" name="suppid" value="{{ old('suppid') }}"  autofocus>-->
              <select id="suppid" type="text" class="form-control" name="suppid">
                <option value=""> Select Data </option>
                @foreach($supp as $supp)
                <option value="{{$supp->xalert_supp}}"> {{$supp->xalert_supp}}</option>
                @endforeach
              </select>

            </div>
          </div>
          <div class="form-group row" id="suppliername" style="display:none;">
            <label for="suppname" class="col-md-3 col-form-label text-md-right">Supplier Name</label>
            <div class="col-md-5">
              <input id="suppname" type="text" class="form-control" name="suppname" value="{{ old('suppname') }}" readonly>
            </div>
          </div>


          <div class="form-group row">
            <label for="domain" class="col-md-3 col-form-label text-md-right">Domain</label>
            <div class="col-md-5">
              <select id="domain" class="form-control role" name="domain" required autofocus>
                <option value=''> Choose Domain </option>
                @foreach($domain as $domain)
                <option value='{{$domain->xdomain_code}}'> {{$domain->xdomain_code}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="email" class="col-md-3 col-form-label text-md-right">E-Mail</label>
            <div class="col-md-7">
              <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="password" class="col-md-3 col-form-label text-md-right">Password</label>
            <div class="col-md-5">
              <input id="password" type="password" class="form-control" name="password" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="password-confirm" class="col-md-3 col-form-label text-md-right">Confirm Password</label>
            <div class="col-md-5">
              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="post" action="/edituser">

        {{ csrf_field() }}
        
        <div class="modal-body">
          <input type="hidden" name='t_id' id='t_id' />
          <input type="hidden" name='role' id='t_role'>
          <input type="hidden" name='d_suppid' id='t_suppid'>
          <input type="hidden" name='d_suppname' id='t_suppname'>
          <input type="hidden" name="lastPage" value="{{$users->lastPage()}}" />
          <div class="form-group row">
            <label for="d_uname" class="col-md-3 col-form-label text-md-right">Username</label>
            <div class="col-md-7 {{ $errors->has('d_uname') ? 'has-error' : '' }}">
              <input id="d_uname" type="text" class="form-control" name="d_uname" value="{{ old('d_uname') }}" readonly autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="d_domain" class="col-md-3 col-form-label text-md-right">Domain</label>
            <div class="col-md-7">
              <input id="d_domain" type="text" class="form-control" name="d_domain" value="{{ old('domain') }}" readonly autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="d_supplier" class="col-md-3 col-form-label text-md-right">Supplier</label>
            <div class="col-md-7">
              <select id="d_supplier" type="text" class="form-control" name="d_supplier" value="{{ old('domain') }}" disabled autofocus>
                <option value=""> - </option>
                @foreach($supp2 as $supp2)
                <option value="{{$supp2->xalert_supp}}"> {{$supp2->xalert_supp}} -- {{$supp2->xalert_nama}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="d_name" class="col-md-3 col-form-label text-md-right">Name</label>
            <div class="col-md-7">
              <input id="d_name" type="text" class="form-control" autocomplete="off" name="name" value="{{ old('name') }}" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="d_email" class="col-md-3 col-form-label text-md-right">E-Mail</label>
            <div class="col-md-7">
              <input id="d_email" type="email" class="form-control" autocomplete="off" name="email" value="{{ old('email') }}" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="d_email" class="col-md-3 col-form-label text-md-right">Role Type</label>
            <div class="col-md-7">
              <select id="t_roletype" class="form-control roletype" name="roletype" required autofocus>
                <option value=""> Select Data </option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="t_dept" class="col-md-3 col-form-label text-md-right">Department</label>
            <div class="col-md-5">
              <select id="t_dept" class="form-control" name="t_dept" required autofocus>
                <option value=""> Select Department </option>
                @foreach($dept as $showdept)
                <option value="{{$showdept->xdept}}"> {{$showdept->xdept}} -- {{$showdept->xdept_desc}} </option>
                @endforeach
              </select>
            </div>
          </div>



        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id='e_btnclose' data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id='e_btnconf'>Save</button>
          <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Status User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/deleteuser" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

          <input type="hidden" name="temp_id" id="temp_id" value="">
          <input type="hidden" name="temp_active" id="temp_active">

          <div class="container">
            <div class="row">
              Are you sure you want to &nbsp; <a name="temp_status" id="temp_status"></a> &nbsp; user :&nbsp; <a name="temp_uname" id="temp_uname"></a> &nbsp;?
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

<div class="modal fade" id="changepassModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="post" action="/adminchangepass">

        {{ csrf_field() }}

        <div class="modal-body">
          <input type='hidden' name='c_id' id='c_id' />

          <div class="form-group row">
            <label for="c_uname" class="col-md-3 col-form-label text-md-right">Username</label>
            <div class="col-md-7 {{ $errors->has('d_uname') ? 'has-error' : '' }}">
              <input id="c_uname" type="text" class="form-control" name="c_uname" value="{{ old('d_uname') }}" readonly autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="c_password" class="col-md-3 col-form-label text-md-right">Password</label>
            <div class="col-md-6">
              <input id="c_password" type="password" class="form-control" name="c_password" required>
            </div>
          </div>
          <div class="form-group row">
            <label for="c_password-confirm" class="col-md-3 col-form-label text-md-right">Confirm Password</label>
            <div class="col-md-6">
              <input id="c_password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
          </div>


        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="c_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="c_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="c_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection

@section('scripts')

<script type="text/javascript">
  // $(document).on('click', '.pagination a', function(e) {
  //   e.preventDefault();

  //   //alert('123');
  //   var page = $(this).attr('href').split('?page=')[1];

  //   //console.log(page);
  //   getData(page);

  // });

  // function getData(page){
  //     $.ajax({
  //         url: '/user/getdata?page='+ page,
  //         type: "get",
  //         datatype: "html" 
  //     }).done(function(data){
  //             console.log('Page = '+ page);

  //             $(".tag-container").empty().html(data);

  //     }).fail(function(jqXHR, ajaxOptions, thrownError){
  //         Swal.fire({
  //             icon: 'error',
  //             text: 'No Response From Server',
  //         })
  //     });
  // }

  function fetch_data(page, username, name) {
    $.ajax({
      url: "/user/getdata?page=" + page + "&username=" + username + "&name=" + name,
      // beforeSend: function() { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
      //   $('#loader').removeClass('hidden')
      // },
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      },
      // complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
      //   $('#loader').addClass('hidden')
      // },

    })
  }


  $(document).on('click', '#btnsearch', function() {
    var username = $('#s_username').val();
    var name = $('#s_name').val();

    // var column_name = $('#hidden_column_name').val();
    // var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    document.getElementById('tmp_username').value = username;
    document.getElementById('tmp_name').value = name;


    fetch_data(page, username, name);
  });


  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var username = $('#tmp_username').val();
    var name = $('#tmp_name').val();


    fetch_data(page, username, name);
  });

  $(document).on('click', '#btnrefresh', function() {
    var username = '';
    var name = '';

    var page = 1;

    document.getElementById('s_username').value = '';
    document.getElementById('s_name').value = '';

    document.getElementById('tmp_username').value = username;
    document.getElementById('tmp_name').value = name;


    fetch_data(page, username, name);
  });


  $(document).on('click', '.newUser', function() {
    document.getElementById('username').value = '';
    document.getElementById('name').value = '';
    document.getElementById('domain').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password-confirm').value = '';
  });

  $(document).on('click', '.editUser', function() { // Click to only happen on announce links

    //alert('123');
    var uid = $(this).data('id');
    var username = $(this).data('uname');
    var name = $(this).data('name');
    var domain = $(this).data('domain');
    var email = $(this).data('email');

    var department = $(this).data('department');
    var role_type = $(this).data('roletype');
    var role = $(this).data('role');
    var suppid = $(this).data('suppid');
    var suppname = $(this).data('suppname');


    document.getElementById("t_id").value = uid;
    document.getElementById("d_uname").value = username;
    document.getElementById("d_name").value = name;
    document.getElementById("d_domain").value = domain;
    document.getElementById("d_email").value = email;
    document.getElementById('t_dept').value = department;
    document.getElementById("t_role").value = role;
    document.getElementById("t_suppid").value = suppid;
    document.getElementById("t_suppname").value = suppname;
    document.getElementById("d_supplier").value = suppid;

    if(role == 'Supplier'){
      // alert('ini external');
      $('#d_supplier').attr("disabled", false);
    }else{
      $('#d_supplier').attr("disabled", true);
    }


    jQuery.ajax({
      type: "get",
      url: "{{URL::to("searchoptionuser") }}",
      data: {
        search: role,
      },
      success: function(data) {
        console.log(data);
        $('#t_roletype').find('option').remove().end().append('<option value="">Select Data</option>');
        for (var i = 0; i < data.length; i++) {
          if(role_type == data[i].xxrole_role){
            // alert('masuk');
            $('#t_roletype').append('<option value="' + data[i].xxrole_role + '" selected>' + data[i].xxrole_role + '</option>');
          }else{
            // alert(data[i].xxrole_role);
            // alert(role_type);
            $('#t_roletype').append('<option value="' + data[i].xxrole_role + '">' + data[i].xxrole_role + '</option>');
          }
        }
      }
    });

  });

  $(document).ready(function() {
    $("#suppid").select2({
      width: '100%'
    });

    $("#role").change(function() {
      var value = $(this).val();
      //alert(value);
      if (value == "Purchasing") {
        //alert('123');
        //$('.supplier').hide();
        document.getElementById("supplierid").style.display = "none";
        document.getElementById("suppliername").style.display = "none";
        document.getElementById("divDept").style.display = "";

        $("#selectDept").prop('required', true);
        $("#suppname").prop('required', false);
        $("#suppid").prop('required', false);
      } else if (value == 'Supplier') {
        document.getElementById("supplierid").style.display = "";
        document.getElementById("suppliername").style.display = "";
        document.getElementById("divDept").style.display = "none";
        $("#selectDept").prop('required', false);
        $("#suppname").prop('required', true);
        $("#suppid").prop('required', true);
      } else if (value == 'Admin') {
        document.getElementById('supplierid').style.display = "none";
        document.getElementById('suppliername').style.display = "none";
        document.getElementById("divDept").style.display = "none";
        $("#selectDept").prop('required', false);
        $("#suppname").prop('required', false);
        $("#suppid").prop('required', false);
      }

      if (value == 'Purchasing') {
        // Ambil data Purhcasing taro di roletype 

        jQuery.ajax({
          type: "get",
          url: "{{URL::to("searchoptionuser") }}",
          data: {
            search: 'Purchasing',
          },
          success: function(data) {
            console.log(data);
            $('#roletype').find('option').remove().end().append('<option value="">Select Data</option>');
            for (var i = 0; i < data.length; i++) {
              $('#roletype').append('<option value="' + data[i].xxrole_role + '">' + data[i].xxrole_role + '</option>');
            }
          }
        });

      } else if (value == 'Supplier') {
        // Ambil data Supplier taro di roletype
        jQuery.ajax({
          type: "get",
          url: "{{URL::to("searchoptionuser") }}",
          data: {
            search: 'Supplier',
          },
          success: function(data) {
            //console.log(data);
            $('#roletype').find('option').remove().end().append('<option value="">Select Data</option>');
            for (var i = 0; i < data.length; i++) {
              $('#roletype').append('<option value="' + data[i].xxrole_role + '">' + data[i].xxrole_role + '</option>');
            }
          }
        });
      } else if (value == 'Admin') {
        jQuery.ajax({
          type: "get",
          url: "{{URL::to("searchoptionuser") }}",
          data: {
            search: 'Admin',
          },
          success: function(data) {
            //console.log(data);
            $('#roletype').find('option').remove().end().append('<option value="">Select Data</option>');
            for (var i = 0; i < data.length; i++) {
              $('#roletype').append('<option value="' + data[i].xxrole_role + '">' + data[i].xxrole_role + '</option>');
            }
          }
        });
      }
    });
    $('form').on("submit", function() {
      document.getElementById('btnclose').style.display = 'none';
      document.getElementById('btnconf').style.display = 'none';
      document.getElementById('btnloading').style.display = '';
      document.getElementById('e_btnclose').style.display = 'none';
      document.getElementById('e_btnconf').style.display = 'none';
      document.getElementById('e_btnloading').style.display = '';
      document.getElementById('d_btnclose').style.display = 'none';
      document.getElementById('d_btnconf').style.display = 'none';
      document.getElementById('d_btnloading').style.display = '';
      document.getElementById('c_btnclose').style.display = 'none';
      document.getElementById('c_btnconf').style.display = 'none';
      document.getElementById('c_btnloading').style.display = '';
    });
  });


  $(document).on('click', '.deleteUser', function() { // Click to only happen on announce links

    //alert('tst');
    var uid = $(this).data('id');
    var uname = $(this).data('role');
    var status = $(this).data('status');
    var active = $(this).data('active');

    document.getElementById("temp_id").value = uid;
    document.getElementById("temp_active").value = active;
    document.getElementById("temp_uname").innerHTML = uname;
    document.getElementById("temp_status").innerHTML = status;

  });

  $(document).on('click', '.changepass', function() { // Click to only happen on announce links

    var uid = $(this).data('id');
    var uname = $(this).data('uname');

    document.getElementById("c_id").value = uid;
    document.getElementById("c_uname").value = uname;
  });

  $(document).on('change', '#suppid', function() {
    data = document.getElementById('suppid').value;

    jQuery.ajax({
      type: "get",
      url: "{{URL::to("searchnamasupp") }}",
      data: {
        search: data,
      },
      success: function(data) {
        console.log(data);
        document.getElementById('suppname').value = data[0].xalert_nama;
      }
    });
  });
</script>

@endsection
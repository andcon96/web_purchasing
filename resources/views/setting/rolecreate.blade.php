@extends('layout.layout')

@section('menu_name','Role Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Role Maintenance</li>
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
  <div class="col-lg-12">
    <button  class="btn btn-info bt-action newRole" data-toggle="modal" data-target="#myModal">
      Create Role</button>
  </div>
  
    <div class="table-responsive col-lg-12 col-md-12 mt-3">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
          <th>Role</th>
         <th>Description</th>  
         <th width="8%">Edit</th>
         <th width="8%">Delete</th>
      </tr>
       </thead>
    <tbody>         
    @foreach ($rolecrt as $show)
      <tr>
        <td>{{ $show->xxrole_role }}</td>
                <td>{{ $show->xxrole_desc }}</td>
                <td>
                    <a href="" class="editModal" data-userid="{{$show->id}}" data-role="{{$show->xxrole_type}}"
                      data-domain= "{{$show->xxrole_domain}}" data-desc= "{{$show->xxrole_desc}}" data-class= "{{$show->xxrole_role}}" data-toggle='modal' data-target="#editModal"><i class="fas fa-edit"></i></button>
                </td>
                <td>
                  @if( $show->id != '2' )
                    <a href="" class="deleteRole" data-userid="{{$show->id}}" data-role="{{$show->xxrole_role}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></button>
                  @endif
                </td>
      </tr>
    @endforeach                      
        </tbody>
      </table>
    </div>

       
<!--Create Modal-->
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
      <!-- konten modal-->
      <div class="modal-content">
        <div class="modal-header">
              <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="panel-body">
          <!-- heading modal -->
          <form class="form-horizontal" role="form" method="POST" action="{{route('rolecreate.update', 'test')}}">
                  {{ method_field('patch') }}
                {{ csrf_field() }}
                      <div class="modal-body">
                        <div class="form-group row">
                            <label for="domain" class="col-md-3 col-form-label text-md-right">{{ __('Domain') }}</label>
                        <div class="col-md-7">
                              <select id="domain" class="form-control role" name="domain" required autofocus>
                                  <option value = ''> Choose Domain </option>
                                @foreach($domain as $domain)
                                  <option value = '{{$domain->xdomain_code}}'> {{$domain->xdomain_code}}</option>
                                @endforeach
                              </select>
                            <!--<input id="domain" type="text" class="form-control" name="domain" value="">-->
                        </div>
              </div>
              <div class="form-group row">
                            <label for="role" class="col-md-3 col-form-label text-md-right">{{ __('Role') }}</label>
                        <div class="col-md-7">
                            <select id="role" class="form-control role" name="role" required autofocus>
                                  <option value=""> Select Data </option>
                                  <option value="Admin"> Admin </option>
                                  <option value="Purchasing"> Internal </option>
                                  <option value="Supplier"> External </option>
                            </select>
                        </div>
              </div>
              <div class="form-group row">
                            <label for="class" class="col-md-3 col-form-label text-md-right">{{ __('Class') }}</label>
                        <div class="col-md-7">
                            <input id="class" type="text" class="form-control" name="class" autocomplete="off" value="" required>
                        </div>
              </div>
                        <div class="form-group row">
                            <label for="desc" class="col-md-3 col-form-label text-md-right">{{ __('Description') }}</label>
                        <div class="col-md-7">
                            <input id="desc" type="textarea" class="form-control" autocomplete="off" name="desc" value="" autocomplete="off">
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
</div>

<!--Edit Modal-->
<div class="modal fade" id="editModal"  tabindex="-1"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <!-- konten modal-->
      <div class="modal-content">
        <div class="modal-header">
              <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="panel-body">
          <!-- heading modal -->
          <form class="form-horizontal" role="form" method="POST" action="/updaterole">
            
                {{ csrf_field() }}
                      <div class="modal-body">
                        <input type="hidden" name="e_id" id="e_id">

                        <div class="form-group row">
                            <label for="e_domain" class="col-md-3 col-form-label text-md-right">{{ __('Domain') }}</label>
                        <div class="col-md-7">
                          <input id="e_domain" type="text" class="form-control" name="e_domain" readonly>
                        </div>
              </div>
              <div class="form-group row">
                            <label for="e_role" class="col-md-3 col-form-label text-md-right">{{ __('Role') }}</label>
                        <div class="col-md-7">
                            <input id="e_role" type="text" class="form-control" name="e_role" readonly>
                        </div>
              </div>
              <div class="form-group row">
                            <label for="e_class" class="col-md-3 col-form-label text-md-right">{{ __('Class') }}</label>
                        <div class="col-md-7">
                            <input id="e_class" type="text" class="form-control" name="e_class" value="" readonly>
                        </div>
              </div>
                        <div class="form-group row">
                            <label for="e_desc" class="col-md-3 col-form-label text-md-right">{{ __('Description') }}</label>
                        <div class="col-md-7">
                            <input id="e_desc" type="textarea" class="form-control" name="e_desc">
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
</div>

<!-- MODAL DELETE -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{route('rolecreate.destroy', 'test')}}" method="post">

        {{ method_field('delete') }}
        {{ csrf_field() }}

        <div class="modal-body">

            <input type="hidden" name="_method" value="delete">

            <input type="hidden" name="temp_id" id="temp_id" value="">

            <div class="container">
              <div class="row">
                Are you sure you want to delete Role :&nbsp; <strong><a name="temp_uname" id="temp_uname"></a></strong> &nbsp;?    
              </div>
            </div>
            
        </div>
      
        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="d_btnconf">Save</button>
          <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
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
      $('form').on("submit",function(){
          document.getElementById('btnclose').style.display = 'none';
          document.getElementById('btnconf').style.display = 'none';
          document.getElementById('btnloading').style.display = '';
          document.getElementById('e_btnclose').style.display = 'none';
          document.getElementById('e_btnconf').style.display = 'none';
          document.getElementById('e_btnloading').style.display = '';
          document.getElementById('d_btnclose').style.display = 'none';
          document.getElementById('d_btnconf').style.display = 'none';
          document.getElementById('d_btnloading').style.display = '';
      });
  });


  $(document).on('click','.newRole',function(){
      document.getElementById('role').value = '';
      document.getElementById('desc').value = ''; 
      document.getElementById('domain').selectedIndex = '0';         
  });

  $(document).on('click','.deleteRole',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var uid = $(this).data('userid');
     var uname = $(this).data('role');

     document.getElementById("temp_id").value = uid;
     document.getElementById("temp_uname").innerHTML = uname;

     });

  $(document).on('click','.editModal',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var uid = $(this).data('userid');
     var role = $(this).data('role');
     var kelas = $(this).data('class');
     var domain = $(this).data('domain');
     var desc = $(this).data('desc');

     
     document.getElementById("e_domain").value = domain;
     document.getElementById("e_id").value = uid;
     document.getElementById("e_role").value = role;
     document.getElementById("e_class").value = kelas;
     document.getElementById("e_desc").value = desc;

     });
</script>
@endsection
@extends('layout.layout')

@section('menu_name', 'Department Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Department Maintenance</li>
</ol>
@endsection

@section('content')

    <!-- page heading -->
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

    <ul>
        
    @if(count($errors) > 0)
       <div class = "alert alert-danger">
          <ul>
             @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
             @endforeach
          </ul>
       </div>
    @endif

    </ul>

    <div class="col-md-12 mb-4">
        <button  class="btn bt-action btn-info newUser" data-toggle="modal" data-target="#createModal">
        Create Dept.</button>
    </div>

    <div class="table-responsive col-lg-12 col-md-12">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Department Description</th>
                    <th width="8%">Edit</th>
                    <th width="8%">Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($depts as $show)
                <tr>
                    <td>{{$show->xdept}}</td>
                    <td>{{$show->xdept_desc}}</td>
                    <td>
                        <a href="" class="editModal" data-deptid="{{$show->id}}" data-name="{{$show->xdept}}"
                            data-desc="{{$show->xdept_desc}}" data-toggle="modal" data-target="#editModal"><i class="fas fa-edit"></i></a>
                    </td>
                    <td>
                        <a href="" class="deleteModal" data-deptid="{{$show->id}}" data-name="{{$show->xdept}}"
                            data-desc="{{$show->xdept_desc}}" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
        <!-- konten modal-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Create Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="panel-body">
                    <!-- heading modal -->
                        <form class="form-horizontal" role="form" method="POST" action="{{route('deptmaint.update', 'test')}}">
                            {{ method_field('patch') }}
                            {{ csrf_field() }}
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="xdept" class="col-md-4 col-form-label text-md-right">{{ __('Department') }}</label>
                                        <div class="col-md-7">
                                            <input id="xdept" type="text" class="form-control" name="xdept" autocomplete="off" value="" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="xdept_desc" class="col-md-4 col-form-label text-md-right">{{ __('Department Description') }}</label>
                                            <div class="col-md-7">
                                                <input id="xdept_desc" type="text" class="form-control" name="xdept_desc" autocomplete="off" value="" autofocus required>
                                            </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success bt-action" id="btncreate">Save</button>
                                    <button type="button" class="btn bt-action" id="btnloading" style="display:none">
                                    <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                                    </button>
                                </div>
                        </form> 
                    </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
        <!-- konten modal-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="panel-body">
                <!-- heading modal -->
                        <form class="form-horizontal" role="form" method="POST" action="/updatedept">
                            {{ csrf_field() }}
                                <div class="modal-body">

                                    <input type="hidden" name="e_id" id="e_id">

                                    <div class="form-group row">
                                        <label for="e_xdept" class="col-md-4 col-form-label text-md-right">{{ __('Department') }}</label>
                                        <div class="col-md-7">
                                            <input id="e_xdept" type="text" class="form-control" name="e_xdept" autocomplete="off" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="e_xdept_desc" class="col-md-4 col-form-label text-md-right">{{ __('Department Description') }}</label>
                                            <div class="col-md-7">
                                                <input id="e_xdept_desc" type="text" class="form-control" name="e_xdept_desc" autocomplete="off" autofocus required>
                                            </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success bt-action" id="e_btncreate">Save</button>
                                    <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                                    <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                                    </button>
                                </div>
                        </form> 
                    </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
        <!-- konten modal-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Delete Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="panel-body">
                <!-- heading modal -->
                        <form class="form-horizontal" role="form" method="POST" action="deletedept">
                            {{ csrf_field() }}
                                <div class="modal-body">
                                    
                                    <input type="hidden" name="temp_id" id="temp_id" value="">

                                    <div class="container">
                                        <div class="row">
                                            Are you sure want to delete Department &nbsp; <b><span name="deptname" id="deptname"></span> -- <span name="deptdesc" id="deptdesc"></span></b> ?
                                        </div>
                                    </div>
                                
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success bt-action" id="d_btndelete">Delete</button>
                                    <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
                                    <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                                    </button>
                                </div>
                        </form> 
                    </div>
            </div>
        </div>
    </div>


@endsection


@section('scripts')
    
<script>

$(document).on('click', '.editModal', function(e){
    var id = $(this).data('deptid');
    var namedept = $(this).data('name');
    var desc = $(this).data('desc');

    document.getElementById('e_id').value = id;
    document.getElementById('e_xdept').value= namedept;
    document.getElementById('e_xdept_desc').value = desc;
});

 $(document).on('click', '.deleteModal', function(e){
    var id = $(this).data('deptid');
    var deptname = $(this).data('name');
    var deptdesc = $(this).data('desc');
    
    document.getElementById('temp_id').value = id;
    document.getElementById('deptname').innerHTML = deptname;
    document.getElementById('deptdesc').innerHTML = deptdesc;
});


$('form').on("submit",function(){
    document.getElementById('btnclose').style.display = 'none';
    document.getElementById('btncreate').style.display = 'none';
    document.getElementById('btnloading').style.display = '';
    document.getElementById('d_btndelete').style.display = 'none';
    document.getElementById('d_btnclose').style.display = 'none';
    document.getElementById('d_btnloading').style.display = '';
    document.getElementById('e_btncreate').style.display = 'none';
    document.getElementById('e_btnclose').style.display = 'none';
    document.getElementById('e_btnloading').style.display = '';
});
</script>
@endsection

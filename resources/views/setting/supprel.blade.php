@extends('layout.layout')

@section('menu_name','Supplier-Item Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Supplier-Item Maintenance</li>
</ol>
@endsection

@section('content')
</style> 
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

          <button  class="btn bt-action deleteUser ml-3" data-toggle="modal" data-target="#createModal" style="margin-left:10px; width:200px">
          Create Relation</button>
        
        <div class="table-responsive col-lg-12 col-md-12 mt-3">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
             <th>Item Number</th>
             <th>Item Desc</th>
             <th>Supplier Code</th>  
             <th>Supplier Name</th>  
             <th width="10%">Edit</th>
             <th width="10%">Delete</th>
          </tr>
           </thead>
            <tbody>         
                @foreach ($alert as $show)
                  <tr>
                    <td>{{ $show->xsurel_part }}</td>
                    <td>{{ $show->xitemreq_desc }}</td>
                    <td>{{ $show->xalert_supp }}</td>
                    <td>{{ $show->xalert_nama }}</td>
                    <td>
                      <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->xsurel_id}}" data-part="{{$show->xsurel_part}}" data-supp="{{$show->xsurel_supp}}"><i class="fas fa-edit"></i></a>
                    </td>
                    <td>
                      <a href="" class="deleteUser" data-toggle="modal" data-target="#deleteModal" data-id="{{$show->xsurel_id}}" data-supp="{{$show->xalert_nama}}" data-part="{{$show->xsurel_part}}"><i class="fas fa-trash"></i></a>
                    </td>
                  </tr>
                @endforeach                      
            </tbody>
          </table>
        </div>


<div class="modal fade " id="editModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="{{route('supprel.update', 'test')}}" method="post">
            {{ method_field('patch') }}
            {{ csrf_field() }}

            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="itemcode" class="col-md-3 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-7">
                        <input id="itemcode" type="text" class="form-control" name="itemcode" value="" required autofocus>
                        @if ($errors->has('itemcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemcode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="itemsupp" class="col-md-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7">
                        <select id="itemsupp" class="form-control" name="itemsupp" required>
                          <option value=""> Select Data </option>
                          @foreach($supp as $supp1)
                            <option value="{{ $supp1->xalert_supp }}"> {{$supp1->xalert_supp}} - {{$supp1->xalert_nama}} </option>
                          @endforeach
                        </select>
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

<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Supplier Relation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="POST" action="/supprel/createnew">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="c_itemcode" class="col-md-4 col-form-label text-md-right">{{ __('Item Number') }}</label>
                    <div class="col-md-6">
                        <select id="c_itemcode" class="form-control role" name="c_itemcode" required autofocus>
                              <option value=""> Select Data </option>
                              @foreach($item as $item)
                                <option value="{{$item->xitemreq_part}}"> {{$item->xitemreq_part}} - {{$item->xitemreq_desc}} </option>
                              @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemsupp" class="col-md-4 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-6">
                        <select id="c_itemsupp" class="form-control" name="c_itemsupp"  required autofocus>
                          <option value=""> Select Data </option>
                          @foreach($supp as $supp)
                            <option value="{{ $supp->xalert_supp }}"> {{ $supp->xalert_supp }} - {{$supp->xalert_nama}} </option>
                          @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id='btnclose' data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success bt-action" id='btnconf'>Save</button>
              <button type="button" class="btn bt-action" id="btnloading" style="display:none">
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
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Supplier Relation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/supprel/delete" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

            <input type="hidden" name="delete_id" id="delete_id" value="">

            <div class="container">
              <div class="row">
                Delete relation for Item : 
                &nbsp; <strong><a name="temp_part" id="temp_part"></a></strong> 
                &nbsp;  Supplier : &nbsp; <strong><a name="temp_supp" id="temp_supp"></a></strong> &nbsp;?    
              </div>
            </div>
            
        </div>
      
        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id='d_btnclose' data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger bt-action" id='d_btnconf'>Save</button>
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
    $(document).on('click','.editUser',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var uid = $(this).data('id');
     var part = $(this).data('part');
     var supp = $(this).data('supp');

     document.getElementById("edit_id").value = uid;
     document.getElementById("itemcode").value = part;
     document.getElementById("itemsupp").value = supp;
     });

    $(document).on('click','.deleteUser',function(){
       var uid = $(this).data('id');
       var part = $(this).data('part');
       var supp = $(this).data('supp');

       document.getElementById('delete_id').value = uid;
       document.getElementById('temp_part').innerHTML = part;
       document.getElementById('temp_supp').innerHTML = supp;
    });

    $(document).ready(function(){
        $("#itemsupp").select2({
          width : '100%'
        });
        $("#c_itemsupp").select2({
          width : '100%'
        });
        $("#c_itemcode").select2({
          width : '100%'
        });

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
</script>

@endsection
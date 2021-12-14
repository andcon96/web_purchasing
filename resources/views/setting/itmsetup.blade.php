@extends('layout.layout')

@section('menu_name','Item Inventory Control')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Item Inventory Control</li>
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

<div>
   <div class="card-body ">
   <div class="row">
   <div class="col-xl-2">
    <form action="/itmcrt" method="get">
				<input disable type="hidden" name="supp" value= {{ Auth::user()->supp_id }} >
			<div>
				<button class="btn bt-action mb-3" type="submit" value="Create" style="width:150px !important" >Create Data</button>
			</div>
		</form>
      </div>
      <form action="/loaditm" id="loaditm" method="post">
       @csrf
				
			<div>
				<button class="btn bt-action mb-3" type="submit" value="Create" style="width:150px !important" >Load Data</button>
			</div>
		</form>
      </div>

      <div class="table-responsive">
         <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
	            <tr>
                  <th>Id</th>
                  <th>Item Number</th>
                  <th>Prod Line</th>
                  <th>Item Type</th>  
                  <th>Design Group</th> 
                  <th>Promo Group</th> 
                  <th>Group</th>
                  <th colspan="2">Option</th>
               </tr>
            </thead>
            <tbody>					
               @forelse ($itm as $show)
                  <tr>
                     <td>{{ $show->xitm_id }}</td>
                     <td>{{ $show->xitm_part }}</td>
                		<td>{{ $show->xitm_prod_line }}</td>
                     <td>{{ $show->xitm_type }}</td>
                     <td>{{ $show->xitm_design }}</td>
                     <td>{{ $show->xitm_promo }}</td>
                		<td>{{ $show->xitm_group }}</td>
                		<td> 
                        <form action="/itmedt" method="get">
                           {{ csrf_field() }}   
                           <input type="hidden" name="id"  class="form-control" value= '{{ $show->xitm_id }} ' > 
                           <input type="hidden" name="part"  class="form-control" value= '{{ $show->xitm_part }} ' > 
                           <input type="hidden"   name="line"  class="form-control" value={{ $show->xitm_prod_line }}> 
                           <input type="hidden"   name="type"  class="form-control" value={{ $show->xitm_type }}> 
                           <input type="hidden"   name="dsgn"  class="form-control" value={{ $show->xitm_design }}> 
                           <input type="hidden"   name="promo" class="form-control" value={{ $show->xitm_promo }}> 
                           <input type="hidden"   name="grp"   class="form-control" value={{ $show->xitm_group }}> 
                           
                          <button class='btn' style="color:#007bff" type="submit" value="EDIT" ><i class="fas fa-edit"></i>             
                        </form>               			
                  	</td>
                     <td> 
                     <a href="" class="deleteUser" 
                        data-toggle="modal" 
                        data-target="#deleteModal" 
                        data-id="{{ $show->xitm_id }}" > 
                     <i class="fas fa-trash"></i></a> 
                  	</td>  
                  </tr>
                @empty
                <tr>
                  <td colspan='12' class="text-danger">
                      <center><b>No Data Available</b></center>
                  </td>
                </tr>
               @endforelse			           
            </tbody>
         </table>
      </div>
   </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <b>Delete Data </b>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/itmdel" method="post">
        {{ csrf_field() }}
        <div class="modal-body">
            <input type="hidden" name="delete_id" id="delete_id" value="">

            <div class="container">
              <div class="row">
                Delete ID: 
                &nbsp; <strong><a name="xid" id="xid"></a></strong> 
                &nbsp;?  
              </div>
            </div>
            
        </div>
      
          <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id='d_btnclose' data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger bt-action" id='d_btnconf'>Delete</button>
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

    $('#loaditm').on('submit',function(){
        $('#loader').removeClass('hidden')
    });


    $(document).on('click','.deleteUser',function(){
       var uid = $(this).data('id');
       document.getElementById('delete_id').value = uid; 
       document.getElementById('xid').innerHTML = uid;       

    });


 
</script>
@endsection
@extends('layout.layout')

@section('menu_name','Transaction Synchronization')

@section('content')


@if (count($errors) > 0)
  <div class="alert alert-danger">
      <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif
<br/>

<div class=""> <!-- <div class="card shadow mb-4"> backup-->
<div class=""> <!-- <div class="card-body"> -->

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
        {{ session()->get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session()->has('added'))
    <div class="alert alert-success  alert-dismissible fade show"  role="alert">
        {{ session()->get('added') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session()->has('deleted'))
    <div class="alert alert-success  alert-dismissible fade show"  role="alert">
        {{ session()->get('deleted') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

    <button  class="btn bt-action ml-3" style="margin-left:10px;" data-toggle="modal" data-target="#createModal">Add</button>
      
    
      <!--Table-->

    <div class="table-responsive col-lg-12 col-md-12 tag-container mt-3">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
             <th>Transaction Type</th>
             <th>Code</th>
             <th width="7%">Action</th>
          </tr>
        </thead>
        <tbody>         
          @foreach ($thistinput as $show)
          <tr>
              <td>{{ $show->xtr_type }}</td> 
              <td>{{ $show->xtr_code }}</td>
              <td data-title="Delete" class="action">        
                <a href="" class="deleteHist" data-id="{{$show->xtr_id}}" data-role="{{$show->xtr_type}}" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-trash-alt"></i></a>
              </td>
          </tr>
          @endforeach     
        </tbody>
      </table>
    </div>
    
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/deletehist" method="post">

        {{ csrf_field() }}

        <div class="modal-body">

            <input type="hidden" name="temp_id" id="temp_id" value="">

            <div class="container">
              <div class="row">
                Are you sure you want to delete<strong><a name="temp_thist" id="temp_thist"></a></strong> &nbsp;?    
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


<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create New</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

           <form action="/trproses" method="post"> 
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="xtr_type" class="col-md-4 col-form-label text-md-right">{{ __('Transaction Type') }}</label>
                    <div class="col-md-5">
                        <select id="xtr_type" type="text" class="form-control-sm" name="xtr_type">
                          <option value=""> Select Data </option>
                          <option value="Purchase Order Maintenance"> Purchase Order Maintenance </option>
                          <option value="Purchase Order Booking"> Purchase Order Booking </option>
                          <option value="PO Return to Vendor"> PO Return to Vendor </option>
                          <option value="Purchase Order Receipt"> Purchase Order Receipt</option>
                          <option value="Loc Transfer-Issue"> Loc Transfer-Issue </option>
                          <option value="Loc Transfer-Receipt"> Loc Transfer-Receipt </option>
                          <option value="Issue Unplanned"> Issue Unplanned </option>
                          <option value="Unplanned Receipt"> Unplanned Receipt </option>
                          <option value="Inv Detail Maint-Issue"> Inv Detail Maint-Issue </option>
                          <option value="Inv Detail Maint-Receipt"> Inv Detail Maint-Receipt </option>
                          <option value="Sales Order Booking (Order)"> Sales Order Booking (Order) </option>
                          <option value="Sales Order Shipments"> Sales Order Shipments </option>
                          <option value="Sales Order Return"> Sales Order Return </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                  <label for="xtr_code" class="col-md-4 col-form-label text-md-right">{{ __('Code') }}</label>
                  <div class="col-md-3">
                    <input type="text" id="xtr_code" name="xtr_code" class="form-control-sm" readonly="" > </input>
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

@endsection

@section('scripts')


<!-- =================================Dropdown Value================================= -->
               
<script type="text/javascript"> 

  var vartype = document.getElementById('xtr_type')
  var varcode = document.getElementById('xtr_code')      
  
  vartype.addEventListener('change', function() {
    // var index = vartype.selectedIndex;
    var index = vartype.options[vartype.selectedIndex].value;    
    // alert(index);

    varcode.value = index;

      if(index == 'Purchase Order Maintenance'){
          varcode.value = 'ADD-PO';  
        }

      if(index == 'Purchase Order Booking'){
          varcode.value = 'ORD-PO';
        }

      if(index == 'PO Return to Vendor'){
          varcode.value = 'ISS-PRV';
        }

        if(index == 'Purchase Order Receipt'){
        varcode.value = 'RCT-PO';
      }
        if(index == 'Loc Transfer-Issue'){
        varcode.value = 'ISS-TR';
      }
        if(index == 'Loc Transfer-Receipt'){
        varcode.value = 'RCT-TR';
      }
        if(index == 'Issue Unplanned'){
        varcode.value = 'ISS-UNP';
      }
        if(index == 'Unplanned Receipt'){
        varcode.value = 'RCT-UNP';
      }
        if(index == 'Inv Detail Maint-Issue'){
        varcode.value = 'ISS-CHL';
      }
        if(index == 'Inv Detail Maint-Receipt'){
        varcode.value = 'RCT-CHL';
      }
        if(index == 'Sales Order Booking (Order)'){
        varcode.value = 'ORD-SO';
      }
        if(index == 'Sales Order Shipments'){
        varcode.value = 'ISS-SO';
      }
        if(index == 'Sales Order Return'){
        varcode.value = 'RCT-SOR';
      }
  })

</script>  
<!-- =================================Dropdown Value================================= -->

<script type="text/javascript">
   $(document).on('click','.deleteHist',function(){ // Click to only happen on announce links
     
     //alert('tst');
     var trid = $(this).data('id');
     // var trhist = $(this).data('role');

     document.getElementById("temp_id").value = trid;
     // document.getElementById("temp_thist").innerHTML = trhist;

     });

</script>

@endsection
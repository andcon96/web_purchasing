@extends('layout.layout')

@section('menu_name','Purchase Order Approval Detail')

@section('content')
<form action="/approvepo" method="post" id="update">
  {{csrf_field() }}
    <input type="hidden" name="edit_id" id="edit_id">
    <input type="hidden" name="apporder" id="apporder" value="{{$approver->xpo_app_order}}">

    <div class="modal-body">
        <div class="form-group row">
            <label for="po_nbr" class="col-md-2 col-form-label text-md-right">{{ __('PO No.') }}</label>
            <div class="col-md-4">
                <input id="po_nbr" type="text" class="form-control" name="po_nbr" value="{{$nopo->xpo_nbr}}" readonly autofocus>
            </div>
            <label for="createdate" class="col-md-2 col-form-label text-md-right">{{ __('Created Date') }}</label>
            <div class="col-md-4">
                <input id="createdate" type="text" class="form-control" name="createdate" value="{{$nopo->xpo_crt_date}}" readonly autofocus>
            </div>
        </div>
        <div class="form-group row">
            <label for="supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
            <div class="col-md-4">
                <input id="supplier" type="text" class="form-control" name="supplier" value="{{$nopo->xpo_vend}}" readonly autofocus>
            </div>
            <label for="duedate" class="col-md-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
            <div class="col-md-4">
                <input id="duedate" type="text" class="form-control" name="duedate" value="{{$nopo->xpo_due_date}}" readonly autofocus>
            </div>
        </div>

        <div class="form-group row" style="padding: 0px 12px 0px 20px;">
            <table id='poapproval' class='table supp-list'>
                <thead>
                    <tr>
                        <th style="width:5%">Line</th>
                        <th style="width:15%">Item No.</th>
                        <th style="width:30%">Description</th>
                        <th style="width:15%">Price</th>
                        <th style="width:10%">Qty</th>
                        <th style="width:15%">Total</th>
                        <th style="width:10%">History</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($data as $data)
                		<tr>
                			<td>{{$data->xpod_line}}</td>
                      <td>{{$data->xpod_part}}</td>
                			<td>{{$data->xpod_desc}}</td>
                			<td>{{number_format($data->xpod_price,2)}}</td>
                			<td>{{number_format($data->xpod_qty_ord,2)}}</td>
                			<td>{{number_format($total = $data->xpod_qty_ord * $data->xpod_price,2)}}</td>
                      <td>
                        <a href="" class='last10search' data-toggle='modal' data-target='#last10Modal'
                        data-itemcode="{{$data->xpod_part}}" data-itemdesc="{{$data->xpod_desc}}"><i class="fas fa-history"></i></a>
                      </td>
                		</tr>
                	@empty
                		<tr>
                			<td colspan="5" style="color:red"><center>No Data Available</center> </td>
                		</tr>
                	@endforelse
                </tbody>
            </table>
        </div>
        <div class="form-group row">
            <label for="e_reason" class="col-md-2 col-form-label text-md-right">{{ __('Reason') }}</label>
            <div class="col-md-9">
                <input id="e_reason" type="text" class="form-control" name="e_reason" value="" autofocus autocomplete="off">
            </div>
        </div>

        <div class="form-group row md-form" id="rowconfirm">
            <div class="col-md-12" style="text-align: center; margin-top:20px;">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="cbsubmitapp" required>
                  <label class="custom-control-label" for="cbsubmitapp">Confirm to submit</label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
      <input type="hidden" name="j_approver" id="j_approver" value="{{$approver->xpo_app_approver}}">
      <input type="hidden" name="j_altapprover" id="j_altapprover" value="{{$approver->xpo_app_alt_approver}}">
      <input type="hidden" name="user_session" id='user_session' value="{{Session::get('userid')}}">
      <a href="{{ url()->previous() }}" id="btnback" class="btn bt-action">Back</a> 
      <button type="submit" class="btn bt-action" name='action' value="reject" id="btnreject">Reject</button>
      <button type="submit" class="btn bt-action" name='action' value="confirm" id="btnconf">Approve</button>
      <button type="button" class="btn bt-action" id="btnloading" style="display:none">
        <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
      </button>
    </div>
</form>


<div class="modal fade" id="last10Modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Last 10 PO/RFQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

            <div class="modal-body">
                <div class="form-group row">
                    <label for="l_item" class="col-md-2 col-lg-2 col-form-label">{{ __('Item Part') }}</label>
                    <div class="col-md-5 col-lg-6">
                        <input id="l_item" type="text" class="form-control" name="l_item" value="" readonly autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12 col-md-12">
                        <table id='top10item' class='table supp-list'>
                            <thead>
                                <tr>
                                    <th style="width:15%">PO No.</th>
                                    <th style="width:15%">Supplier Code</th>
                                    <th style="width:15%">Supplier Name</th>
                                    <th style="width:15%">Price</th>
                                    <th style="width:15%">Create Date</th>
                                </tr>
                            </thead>
                            <tbody id='bodytop10item'>
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <table id='top10item' class='table supp-list'>
                            <thead>
                                <tr>
                                    <th style="width:15%">RFQ No.</th>
                                    <th style="width:15%">Supplier Code</th>
                                    <th style="width:15%">Supplier Name</th>
                                    <th style="width:15%">Price</th>
                                    <th style="width:15%">Create Date</th>
                                </tr>
                            </thead>
                            <tbody id='bodytop10rfq'>
                                
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" data-dismiss="modal" id='btnclose'>Cancel</button>
              </button>
            </div>


    </div>
  </div>
</div>

@endsection

@section('scripts')

<script>
  $('#update').submit(function(event) {
      document.getElementById('btnclose').style.display = 'none';
      document.getElementById('btnconf').style.display = 'none';
      document.getElementById('btnback').style.display = 'none';
      document.getElementById('btnreject').style.display = 'none';
      document.getElementById('btnloading').style.display = ''; 
  });


  $(document).ready(function(){
      var approver = document.getElementById('j_approver').value;
      var altapprover = document.getElementById('j_altapprover').value;
      var session = document.getElementById('user_session').value;
    
      if(session != approver && session != altapprover){
          document.getElementById('btnreject').style.display = 'none';
          document.getElementById('btnconf').style.display = 'none';
          document.getElementById('e_reason').readOnly = true;
          document.getElementById('rowconfirm').style.display = 'none';
      }else{
          document.getElementById('btnreject').style.display = '';
          document.getElementById('btnconf').style.display = '';
          document.getElementById('rowconfirm').style.display = '';
          document.getElementById('e_reason').readOnly = false;
      }
  });

  $(document).on('click','.last10search',function(){ // Click to only happen on announce links
   
   //alert('123');
     var part = $(this).data('itemcode');
     var desc = $(this).data('itemdesc');

     document.getElementById("l_item").value = part + " - " + desc;
     
     jQuery.ajax({
          type : "get",
          url : "{{URL::to("polast10search") }}",
          data:{
            search : part
          },
          success:function(data){
            //$('tbody').html(data);
            //console.log(data);
            $("#bodytop10item").empty().html(data);
          }
      });

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqlast10search") }}",
          data:{
            search : part
          },
          success:function(data){
            //$('tbody').html(data);
            //console.log(data);
            $("#bodytop10rfq").empty().html(data);
          }
      });

  });
</script>
@endsection
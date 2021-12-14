@extends('layout.layout')

@section('menu_name','Role Menu Maintenance')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Master</a></li>
    <li class="breadcrumb-item active">Role Menu Maintenance</li>
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
 <div class="table-responsive col-lg-12 col-md-12 mt-3">
              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
              <thead>
                  <tr>
                   <th>Role</th>
                   <th>Description</th>  
                   <th>Edit</th>
                  </tr>
             </thead>
            <tbody>         
            @foreach ($rolecrt as $show)
              <tr>
                  <td data-title="Role">{{ $show->xxrole_role }}</td>
                  <td data-title="Desc">{{ $show->xxrole_desc }}</td>
                  <td data-title="Edit" class="action">
                        @if($show->xxrole_role === 'Admin' or $show->xxrole_role === 'Superadm')
                        <a href="" class="editUser"vdata-id="{{$show->id}}" data-role="{{$show->xxrole_role}}"
                           data-desc="{{$show->xxrole_desc}}" data-flg="{{$show->xxrole_flag}}"><i class="fas fa-edit" style="opacity: 0.5; cursor:not-allowed;"></i></a>
                        @else
                        <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->id}}" data-role="{{$show->xxrole_role}}"
                           data-desc="{{$show->xxrole_desc}}" data-flg="{{$show->xxrole_flag}}"><i class="fas fa-edit"></i></a>
                        @endif
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

        <form action="{{route('role.update', 'test')}}" method="post">

          {{ method_field('patch') }}
          {{ csrf_field() }}
          
          <input type="hidden" name="edit_id" id="edit_id" value="">

          <div class="modal-body">
              <div class="form-group row">
                  <label for="role" class="col-md-3 col-form-label text-md-right">{{ __('Role') }}</label>
                  <div class="col-md-7">
                      <input id="role" type="text" class="form-control" name="role" value="" disabled>
                  </div>
              </div>
              <div class="form-group row">
                  <label for="desc" class="col-md-3 col-form-label text-md-right">{{ __('Desc') }}</label>
                  <div class="col-md-7">
                      <input id="desc" type="text" class="form-control" name="desc" value="" disabled>
                  </div>
              </div>

              <div class="form-group">
                <h5><center><strong>Menu Access</strong></center></h5>
                <hr>
              </div>

              <div class="form-group">
                <h6><center><strong>PO</strong></center></h5>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO List') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPOBrowse">
                    <input type="checkbox" id="cbPOBrowse" name="cbPOBrowse" value="PO01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO Receipt') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPOReceipt">
                    <input type="checkbox" id="cbPOReceipt" name="cbPOReceipt" value="PO02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO Approval') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPOApproval">
                    <input type="checkbox" id="cbPOApproval" name="cbPOApproval" value="PO03" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

                <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO Approval Utility') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbResetApp">
                    <input type="checkbox" id="cbResetApp" name="cbResetApp" value="PO05" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Last 10 RFQ & PO') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbLast10PO">
                    <input type="checkbox" id="cbLast10PO" name="cbLast10PO" value="PO04" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Audit Trail PO') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbAuditPO">
                    <input type="checkbox" id="cbAuditPO" name="cbAuditPO" value="PO06" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Audit Trail PO Approval') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbAuditPOApp">
                    <input type="checkbox" id="cbAuditPOApp" name="cbAuditPOApp" value="PO07" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group">
                <h6><center><strong>RFQ</strong></center></h5>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFQ Data Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRfqMain">
                    <input type="checkbox" id="cbRfqMain" name="cbRfqMain" value="RF01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFQ Approval') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRFQApp">
                    <input type="checkbox" id="cbRFQApp" name="cbRFQApp" value="RF02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFQ History Data') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRFQHistory">
                    <input type="checkbox" id="cbRFQHistory" name="cbRFQHistory" value="RF03" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Last 10 RFQ') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbLast10RFQ">
                    <input type="checkbox" id="cbLast10RFQ" name="cbLast10RFQ" value="RF04" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Audit Trail RFQ') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbAuditRFQ">
                    <input type="checkbox" id="cbAuditRFQ" name="cbAuditRFQ" value="RF06" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>


              <!-- UNTUK RFP heading -->
              <div class="form-group">
                <h6><center><strong>RFP</strong></center></h6>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Data Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRfpMain">
                    <input type="checkbox" id="cbRfpMain" name="cbRfpMain" value="RFP01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Approval') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRfpApp">
                    <input type="checkbox" id="cbRfpApp" name="cbRfpApp" value="RFP02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Audit Data') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbHistRfp">
                    <input type="checkbox" id="cbHistRfp" name="cbHistRfp" value="RFP04" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Approval Audit') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbAuditRfp">
                    <input type="checkbox" id="cbAuditRfp" name="cbAuditRfp" value="RFP05" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Approval Utility') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbresetRfp">
                    <input type="checkbox" id="cbresetRfp" name="cbresetRfp" value="RFP06" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              <!-- RFP END -->
              
              <!-- UNTUK Purplan heading -->
              <div class="form-group">
                <h6><center><strong>Purchase Plan</strong></center></h6>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Purchase Order List') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPPbrowse">
                    <input type="checkbox" id="cbPPbrowse" name="cbPPbrowse" value="PP01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Purchase Order Create') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPPcreate">
                    <input type="checkbox" id="cbPPcreate" name="cbPPcreate" value="PP02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <!-- Purplan END -->


              <div class="form-group">
                <h6><center><strong>Supplier</strong></center></h5>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO Confirmation') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbPOConf">
                    <input type="checkbox" id="cbPOConf" name="cbPOConf" value="SH01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Shipper Confirmation') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbShipConf">
                    <input type="checkbox" id="cbShipConf" name="cbShipConf" value="SH02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Shipper Browse') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbShipBrowse">
                    <input type="checkbox" id="cbShipBrowse" name="cbShipBrowse" value="SH04" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>


              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFQ Feed Back') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRFQFeed">
                    <input type="checkbox" id="cbRFQFeed" name="cbRFQFeed" value="SH03" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              <div class="form-group">
                <h6><center><strong>Inventory</strong></center></h5>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Safety Stock Data') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbStockData">
                    <input type="checkbox" id="cbStockData" name="cbStockData" value="IV01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Expired Inventory') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbExpInv">
                    <input type="checkbox" id="cbExpInv" name="cbExpInv" value="IV02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Slow Moving Inventory') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbSlowMov">
                    <input type="checkbox" id="cbSlowMov" name="cbSlowMov" value="IV03" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group">
                <h6><center><strong>Setting</strong></center></h5>
                <hr>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('User Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbUserMT">
                    <input type="checkbox" id="cbUserMT" name="cbUserMT" value="ST01" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Role Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRoleMT">
                    <input type="checkbox" id="cbRoleMT" name="cbRoleMT" value="ST02" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Role Menu Permissions') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRoleMenu">
                    <input type="checkbox" id="cbRoleMenu" name="cbRoleMenu" value="ST03" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Supplier Inventory Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbSuppMT">
                    <input type="checkbox" id="cbSuppMT" name="cbSuppMT" value="ST04" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Item Control') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbItem">
                    <input type="checkbox" id="cbItem" name="cbItem" value="ST05" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Inventory by Supplier') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbItemMT">
                    <input type="checkbox" id="cbItemMT" name="cbItemMT" value="ST06" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Supplier-Item Maintenance') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbSuppItem">
                    <input type="checkbox" id="cbSuppItem" name="cbSuppItem" value="ST07" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFQ / RFP Control') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbRFQControl">
                    <input type="checkbox" id="cbRFQControl" name="cbRFQControl" value="ST08" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('PO Approval Control') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbAppCont">
                    <input type="checkbox" id="cbAppCont" name="cbAppCont" value="ST09" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Site Control') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbSiteCon">
                    <input type="checkbox" id="cbSiteCon" name="cbSiteCon" value="ST10" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Alert Maint') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbLicense">
                    <input type="checkbox" id="cbLicense" name="cbLicense" value="ST11" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>
              
              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Transaction Sync') }}</label>
                  <div class="col-md-6">
                  <label class="switch" for="cbTrSync">
                    <input type="checkbox" id="cbTrSync" name="cbTrSync" value="ST12" />
                    <div class="slider round"></div>
                  </label>
                </div>  
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Dept Maint') }}</label>
                  <div class="col-md-6">
                    <label class="switch" for="cbDept">
                      <input type="checkbox" id="cbDept" name="cbDept" value="ST13"/>
                      <div class="slider round"></div>
                    </label>
                  </div>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('RFP Approval Control') }}</label>
                  <div class="col-md-6">
                    <label class="switch" for="cbRFPApprove">
                      <input type="checkbox" id="cbRFPApprove" name="cbRFPApprove" value="ST14"/>
                      <div class="slider round"></div>
                    </label>
                  </div>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('Item Conversion Maint') }}</label>
                  <div class="col-md-6">
                    <label class="switch" for="cbItemConv">
                      <input type="checkbox" id="cbItemConv" name="cbItemConv" value="ST15"/>
                      <div class="slider round"></div>
                    </label>
                  </div>
              </div>

              <div class="form-group row">
                <label for="level" class="col-md-6 col-form-label text-md-right">{{ __('UM Maintenance') }}</label>
                  <div class="col-md-6">
                    <label class="switch" for="cbUmMaint">
                      <input type="checkbox" id="cbUmMaint" name="cbUmMaint" value="ST16"/>
                      <div class="slider round"></div>
                    </label>
                  </div>
              </div>

        </div>
        
          <div class="modal-footer">
            <button type="button" class="btn btn-info bt-action" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success bt-action">Save</button>
          </div>

        </form>

      </div>
    </div>
</div>
   
@endsection

@section('scripts')
  

<!-- Pass Value Modal Edit & Checkbox Setting -->
<script type="text/javascript">
  
  $(document).on('click','.editUser',function(){ // Click to only happen on announce links
     
     //alert('tst');
     
     var idrole = $(this).data('id');
     var role = $(this).data('role');
     var desc = $(this).data('desc');

     //alert(idrole)

     document.getElementById("edit_id").value = idrole;
     document.getElementById("role").value = role;
     document.getElementById("desc").value = desc;


     jQuery.ajax({
          type : "get",
          url : "{{URL::to("menurole") }}",
          data:{
            search : idrole,
          },
          success:function(data){
            // /alert(data);
            var totmenu = data;
            
            // Centang Checkbox berdasarkan data
            if(totmenu.search("PO01") >= 0){
              document.getElementById("cbPOBrowse").checked = true;  
            }else{
              document.getElementById("cbPOBrowse").checked = false;
            }
            if(totmenu.search("PO02") >= 0){
              document.getElementById("cbPOReceipt").checked = true;  
            }else{
              document.getElementById("cbPOReceipt").checked = false;
            }
            if(totmenu.search("PO03") >= 0){
              document.getElementById("cbPOApproval").checked = true;  
            }else{
              document.getElementById("cbPOApproval").checked = false;
            }
            if(totmenu.search("PO04") >= 0){
              document.getElementById("cbLast10PO").checked = true;  
            }else{
              document.getElementById("cbLast10PO").checked = false;
            }
            if(totmenu.search("PO04") >= 0){
              document.getElementById("cbLast10PO").checked = true;  
            }else{
              document.getElementById("cbLast10PO").checked = false;
            }
            if(totmenu.search("PO05") >= 0){
              document.getElementById("cbResetApp").checked = true;  
            }else{
              document.getElementById("cbResetApp").checked = false;
            }
            if(totmenu.search("PO06") >= 0){
              document.getElementById("cbAuditPO").checked = true;  
            }else{
              document.getElementById("cbAuditPO").checked = false;
            }
            if(totmenu.search("PO07") >= 0){
              document.getElementById("cbAuditPOApp").checked = true;  
            }else{
              document.getElementById("cbAuditPOApp").checked = false;
            }


            if(totmenu.search("RF01") >= 0){
              document.getElementById("cbRfqMain").checked = true;  
            }else{
              document.getElementById("cbRfqMain").checked = false;
            }
            if(totmenu.search("RF02") >= 0){
              document.getElementById("cbRFQApp").checked = true;  
            }else{
              document.getElementById("cbRFQApp").checked = false;
            }
            if(totmenu.search("RF03") >= 0){
              document.getElementById("cbRFQHistory").checked = true;  
            }else{
              document.getElementById("cbRFQHistory").checked = false;
            }
            if(totmenu.search("RF04") >= 0){
              document.getElementById("cbLast10RFQ").checked = true;  
            }else{
              document.getElementById("cbLast10RFQ").checked = false;
            }
            if(totmenu.search("RF06") >= 0){
              document.getElementById("cbAuditRFQ").checked = true;  
            }else{
              document.getElementById("cbAuditRFQ").checked = false;
            }


            //RFP setting
            if(totmenu.search("RFP01")>=0){
              document.getElementById("cbRfpMain").checked = true;
            }else{
              document.getElementById("cbRfpMain").checked = false;
            }
            if(totmenu.search("RFP02") >= 0){
              document.getElementById("cbRfpApp").checked = true;  
            }else{
              document.getElementById("cbRfpApp").checked = false;
            }
            if(totmenu.search("RFP04") >= 0){
              document.getElementById("cbHistRfp").checked = true;  
            }else{
              document.getElementById("cbHistRfp").checked = false;
            }
            if(totmenu.search("RFP05") >= 0){
              document.getElementById("cbAuditRfp").checked = true;  
            }else{
              document.getElementById("cbAuditRfp").checked = false;
            }
            if(totmenu.search("RFP06") >= 0){
              document.getElementById("cbresetRfp").checked = true;  
            }else{
              document.getElementById("cbresetRfp").checked = false;
            }
            //RFP setting END
            
            //Purplan setting
            if(totmenu.search("PP01")>=0){
              document.getElementById("cbPPbrowse").checked = true;
            }else{
              document.getElementById("cbPPbrowse").checked = false;
            }
            if(totmenu.search("PP02") >= 0){
              document.getElementById("cbPPcreate").checked = true;  
            }else{
              document.getElementById("cbPPcreate").checked = false;
            }
            //Purplan setting END

            
            if(totmenu.search("IV01") >= 0){
              document.getElementById("cbStockData").checked = true;  
            }else{
              document.getElementById("cbStockData").checked = false;
            }

            if(totmenu.search("IV02") >= 0){
              document.getElementById("cbExpInv").checked = true; 
            }else{
              document.getElementById("cbExpInv").checked = false;
            }

            if(totmenu.search("IV03") >= 0){
              document.getElementById("cbSlowMov").checked = true; 
            }else{
              document.getElementById("cbSlowMov").checked = false;
            }




            if(totmenu.search("SH01") >= 0){
              document.getElementById("cbPOConf").checked = true;  
            }else{
              document.getElementById("cbPOConf").checked = false;
            }

            if(totmenu.search("SH02") >= 0){
              document.getElementById("cbShipConf").checked = true; 
            }else{
              document.getElementById("cbShipConf").checked = false;
            }

            if(totmenu.search("SH03") >= 0){
              document.getElementById("cbRFQFeed").checked = true;  
            }else{
              document.getElementById("cbRFQFeed").checked = false;
            }

            if(totmenu.search("SH04") >= 0){
              document.getElementById("cbShipBrowse").checked = true;  
            }else{
              document.getElementById("cbShipBrowse").checked = false;
            }




            if(totmenu.search("ST01") >= 0){
              document.getElementById("cbUserMT").checked = true;  
            }else{
              document.getElementById("cbUserMT").checked = false;
            }
            if(totmenu.search("ST02") >= 0){
              document.getElementById("cbRoleMT").checked = true;  
            }else{
              document.getElementById("cbRoleMT").checked = false;
            }
            if(totmenu.search("ST03") >= 0){
              document.getElementById("cbRoleMenu").checked = true;  
            }else{
              document.getElementById("cbRoleMenu").checked = false;
            }
            if(totmenu.search("ST04") >= 0){
              document.getElementById("cbSuppMT").checked = true;  
            }else{
              document.getElementById("cbSuppMT").checked = false;
            }
            if(totmenu.search("ST05") >= 0){
              document.getElementById("cbItem").checked = true;  
            }else{
              document.getElementById("cbItem").checked = false;
            }
            if(totmenu.search("ST06") >= 0){
              document.getElementById("cbItemMT").checked = true;  
            }else{
              document.getElementById("cbItemMT").checked = false;
            }
            if(totmenu.search("ST07") >= 0){
              document.getElementById("cbSuppItem").checked = true;  
            }else{
              document.getElementById("cbSuppItem").checked = false;
            }
            if(totmenu.search("ST08") >= 0){
              document.getElementById("cbRFQControl").checked = true;  
            }else{
              document.getElementById("cbRFQControl").checked = false;
            }
            if(totmenu.search("ST09") >= 0){
              document.getElementById("cbAppCont").checked = true;  
            }else{
              document.getElementById("cbAppCont").checked = false;
            }
            if(totmenu.search("ST10") >= 0){
              document.getElementById("cbSiteCon").checked = true;  
            }else{
              document.getElementById("cbSiteCon").checked = false;
            }
            if(totmenu.search("ST11") >= 0){
              document.getElementById("cbLicense").checked = true;  
            }else{
              document.getElementById("cbLicense").checked = false;
            }
            if(totmenu.search("ST12") >= 0){
              document.getElementById("cbTrSync").checked = true;  
            }else{
              document.getElementById("cbTrSync").checked = false;
            }
            if(totmenu.search("ST13") >= 0){
              document.getElementById("cbDept").checked = true;
            }else{
              document.getElementById("cbDept").checked = false;
            }
            if(totmenu.search("ST14") >= 0){
              document.getElementById("cbRFPApprove").checked = true;
            }else{
              document.getElementById("cbRFPApprove").checked = false;
            }
            if(totmenu.search("ST15") >= 0){
              document.getElementById("cbItemConv").checked = true;
            }else{
              document.getElementById("cbItemConv").checked = false;
            }
            if(totmenu.search("ST16") >= 0){
              document.getElementById("cbUmMaint").checked = true;
            }else{
              document.getElementById("cbUmMaint").checked = false;
            }

            

          }
      });
     
     });

</script>
@endsection
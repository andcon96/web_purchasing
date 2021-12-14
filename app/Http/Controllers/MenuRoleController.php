<?php

namespace App\Http\Controllers;

use App\XxroleMstr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class MenuRoleController extends Controller
{
    public function index()
    {
      $rolecrt = DB::select('select * from xxrole_mstrs');       
    	return view('/setting/role',['rolecrt'=>$rolecrt]);
    }
    
    public function update(Request $request)
    {     
        //dd($request->all());

        // Menu PO
        $cbPOBrowse = $request->input('cbPOBrowse');
        $cbPOReceipt = $request->input('cbPOReceipt');
        $cbPOApproval = $request->input('cbPOApproval');
        $cbLast10PO = $request->input('cbLast10PO');
        $cbAuditPOApp = $request->input('cbAuditPOApp');
        $cbAuditPO = $request->input('cbAuditPO');
        
        // Menu RFQ
        $cbRfqMain = $request->input('cbRfqMain');
        $cbRFQApp = $request->input('cbRFQApp');
        $cbRfqHistory = $request->input('cbRFQHistory');
        $cbLast10RFQ = $request->input('cbLast10RFQ');
        $cbResetApproval = $request->input('cbResetApp');
        $cbAuditRFQ = $request->input('cbAuditRFQ');

        //Menu RFP
        $cbRfpMain = $request->input('cbRfpMain');
        $cbRfpApp = $request->input('cbRfpApp');
        // $cbRFfpgeneratepo = $request->input('cbRFfpgeneratepo');
        $cbHistRfp = $request->input('cbHistRfp');
        $cbAuditRfp = $request->input('cbAuditRfp');
		$cbresetRfp = $request->input('cbresetRfp');

        
        // Menu Supp
        $cbPOConf = $request->input('cbPOConf');
        $cbShipConf = $request->input('cbShipConf');
        $cbRFQFeed = $request->input('cbRFQFeed');
        $cbShipBrowse = $request->input('cbShipBrowse');

        // Menu Ship
        $cbStockData = $request->input('cbStockData');
        $cbExpInv = $request->input('cbExpInv');
        $cbSlowMov = $request->input('cbSlowMov');
        
        // Menu Purplan
        $cbPPbrowse = $request->input('cbPPbrowse');
        $cbPPcreate = $request->input('cbPPcreate');

        // Menu Setting
        $cbUserMT = $request->input('cbUserMT');
        $cbRoleMT = $request->input('cbRoleMT');
        $cbRoleMenu = $request->input('cbRoleMenu');
        $cbSuppMT = $request->input('cbSuppMT');
        $cbItem = $request->input('cbItem');
        $cbItemMT = $request->input('cbItemMT');
        $cbSuppItem = $request->input('cbSuppItem');
        $cbRfqControl = $request->input('cbRFQControl');
        $cbAppCont = $request->input('cbAppCont');
        $cbSiteCon = $request->input('cbSiteCon');
        $cbLicense = $request->input('cbLicense');
        $cbTrSync = $request->input('cbTrSync');
        $cbDept = $request->input('cbDept');
        $cbRFPApprove = $request->input('cbRFPApprove');
        $cbItemConv = $request->input('cbItemConv');
        $cbUmMaint = $request->input('cbUmMaint');

        //dd($cbRfqControl);

        $data = $cbPOBrowse.$cbPOReceipt.$cbPOApproval.$cbLast10PO.$cbAuditPOApp.$cbAuditPO.$cbRfqMain.$cbRFQApp.$cbRfqHistory.$cbLast10RFQ.$cbResetApproval.$cbAuditRFQ.$cbRfpMain.$cbRfpApp.$cbHistRfp.$cbAuditRfp.$cbresetRfp.$cbPOConf.$cbShipConf.$cbRFQFeed.$cbShipBrowse.$cbStockData.$cbExpInv.$cbSlowMov.$cbUserMT.$cbRoleMT.$cbRoleMenu.$cbSuppMT.$cbItem.$cbItemMT.$cbSuppItem.$cbRfqControl.$cbAppCont.$cbSiteCon.$cbLicense.$cbTrSync.$cbDept.$cbRFPApprove.$cbItemConv.$cbUmMaint.$cbPPbrowse.$cbPPcreate;

        //dd($data);

        DB::table('xxrole_mstrs')
            ->where('id', $request->edit_id)
            ->update(['xxrole_flag' => $data]);

        // session()->flash("updated","Role Menu is Successfully Updated !");
        alert()->success('Success','Role Menu Is Succesfully Updated');
        
        return back();


    }


    public function destroy(Request $request)
    {
    	$roles = XxroleMstr::findOrFail($request->temp_id);
    	$roles->delete();
    	// session()->flash("deleted","Role Menu is Successfully Deleted !");
        alert()->success('Success','Role Menu is Succesfully Deleted');
    	return back();
    }

    public function search(Request $req)
    {
        if($req->ajax()){

            $output="";

            $jabatan=DB::table("xxrole_mstrs")->where("id",$req->search)
                                 ->get();

            if($jabatan)
            {
                foreach ($jabatan as $key => $jabatan) {
                    $output.= $jabatan->xxrole_flag;
                }
            }

            return Response($output);

        }
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default Auth Laravel

use App\Http\Controllers\RfpMaintenanceController;
use Illuminate\Http\Request;

Route::get('/', function () {
	if(Auth::check()){return Redirect::to('home');}
    return view('auth.login');
});

// // Google API
// Route::get('glogin',array('as'=>'glogin','uses'=>'GoogleUserController@googleLogin')) ;
// Route::get('google-user',array('as'=>'user.glist','uses'=>'GoogleUserController@listGoogleUser')) ;
// Route::post('upload-file',array('as'=>'upload-file','uses'=>'GoogleUserController@uploadFileUsingAccessToken')) ;
// Route::get('loadgdrive',array('as'=>'upload-gdrive','uses'=>'GoogleUserController@loadgdrive')) ;

Route::get('test', function() {
	$content = Storage::disk('google')->allFiles();
	$firstfile = $content[0];
	$metadata = Storage::disk('google')->getMetadata($firstfile);
	$filename = $metadata['name'];

	

	$response = Storage::disk('google')->download($firstfile,$filename);
	$response->send();

	// dd(Storage::disk('google')->allFiles()); --> list semua file
    // Storage::disk('google')->put('test.txt', 'Hello World'); --> bkin file baru
});

Route::post('uploadGoogle', function (Request $req){

	$filename = $req->file('file')->getClientOriginalName();
	Storage::disk('google')->putFileAs('',$req->file('file'), $filename);

	// get last uploaded file
	$contents = collect(Storage::cloud()->listContents('/', false))
				->where('type', '=', 'file')
				->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
				->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
				->sortBy('timestamp')
				->last();
	
	// dd($contents,$contents['path']); -> ambil pathnya buat dipke donlod. path bisa simpen DB

});

Route::group(['middleware' => ['auth']], function() {

	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

	// RFP  01092020
	route::get('/rfp', 'RfpMaintenanceController@index');


	// Purchase Plan 20/10/2020
	route::get('/purplan', function(){
		return view('/purplan/menupurplan');
	}); //25 januari 2021
	route::get('purplanbrowse', 'PurchasePlanController@purplanbrowse'); //25 Januari 2021
	route::get('ppbrowsesearch', 'PurchasePlanController@ppbrowsesearch'); //25 Januari 2021
	route::get('purplanview','PurchasePlanController@index');
	route::post('viewdetails','PurchasePlanController@viewdetails');
	route::get('viewdetailtmp','PurchasePlanController@viewdetailtmp')->name('viewdetailtmp');
	route::post('cimloadpplan','QxCimController@cimloadpplan');
	route::post('deletetemp','PurchasePlanController@deletetemp');
	route::post('edittemp','PurchasePlanController@edittemp');
	route::get('purplansearch','PurchasePlanController@purplansearch');
	route::get('/pagination/viewppbrowse', 'PurchasePlanController@viewppbrowse');

    // Role&User Create + Menu

	route::resource('rolecreate', 'RoleMaintController');
	route::resource('role', 'MenuRoleController');
	route::get('/menurole', 'MenuRoleController@search');
	route::resource('usermaint', 'UserMaintController');
	route::get('/user/getdata','UserMaintController@index');
	route::get('/userprof', 'UserMaintController@indprof');
	route::post('/userprof/update', 'UserMaintController@updateprof');
	route::get('/userchange', 'UserMaintController@indchangepass');
	route::post('/userchange/changepass', 'UserMaintController@changepass');
	route::post('/deleteuser','UserMaintController@deleteuser');
	route::post('/edituser','UserMaintController@updateuser');
	route::post('/adminchangepass','UserMaintController@adminchangepass');
	route::get('/searchoptionuser','UserMaintController@searchoptionuser');
	route::post('/updaterole','RoleMaintController@updaterole');
	route::get('/mt','UserMaintController@mtweb'); /*menu MT Super User 06072020*/
	route::post('/mt/createnew','UserMaintController@mtwebcreate'); /*menu MT Super User 06072020*/
	route::get('/rfqinputsearch','RfqMaintenanceController@rfqinputsearch'); /*Rfq Purchasing Search 06072020*/
	Route::get('/searchdetailrfq','RfqMaintenanceController@searchdetailrfq'); /*03082020 -- get nama supp value*/
	Route::post('/loadsupp','SurelMaintenanceController@loadsupplier'); /*05082020 -- Load Supplier Mstr*/
	Route::get('/suppmstrsearch','SurelMaintenanceController@suppmstrsearch'); /*05082020 -- Load Supplier Mstr*/

	// Alert Maint + Menu
	route::resource('alertcreate', 'AlertMaintController');
	route::post('/alertcreate/createnew', 'AlertMaintController@createnew');
	route::get('/alertsearch', 'AlertMaintController@search');

	route::resource('alertitem', 'AlertItemController');
	route::post('/alertitem/createnew', 'AlertItemController@createnew');
	route::get('/alertitemsearch', 'AlertItemController@search');

	// RFQ Maint
	route::resource('rfqmaint', 'RfqMaintenanceController');

	// Supplier Relation
	route::resource('supprel','SurelMaintenanceController');
	route::post('/supprel/createnew', 'SurelMaintenanceController@createnew');
	route::post('/supprel/delete', 'SurelMaintenanceController@delete');


	//PO

	Route::get('/po','PurchaseOrderMaintenance@viewmenupo');

	route::post('/loadpo','PurchaseOrderMaintenance@loadwsapo');
	route::get('/pobrowse','PurchaseOrderMaintenance@viewpo');
	Route::get('/po/fetch_data', 'PurchaseOrderMaintenance@viewpo');
	Route::get('/posearch', 'PurchaseOrderMaintenance@searchpo');
	
	Route::get('/poreceipt','PurchaseOrderMaintenance@indexreceipt')->name('poreceipt');
	Route::get('/po/fetch_receipt', 'PurchaseOrderMaintenance@indexreceipt');
	Route::post('/receiptsearch', 'PurchaseOrderMaintenance@searchreceipt'); /*09062020*/
	Route::get('/detailreceipt', 'PurchaseOrderMaintenance@detailreceipt');
	Route::post('/receiptupdate', 'QxCimController@receiptupdate');
	Route::post('/newreceiptrow','PurchaseOrderMaintenance@newreceiptrow');
	Route::get('/showreceiptrow','PurchaseOrderMaintenance@showreceiptrow')->name('showreceiptrow');
	Route::post('/deletenewreceipt','PurchaseOrderMaintenance@deleterow');
	Route::post('/updaterow','PurchaseOrderMaintenance@updaterow');
	Route::post('/receiptqad','QxCimController@receiptqad');



	Route::get('/emailconfpo','SendEmailController@send');
	Route::get('/confirmpo/{ponbr}/{line}','SendEmailController@confirm');
	Route::get('/poappcontrol','PurchaseOrderMaintenance@controlindex');
	Route::post('/poappcontrol/createnew','PurchaseOrderMaintenance@controlcreatenew');
	Route::get('/po/fetch_data_control', 'PurchaseOrderMaintenance@controlindex'); /*23062020*/
	Route::get('/searchpoapp', 'PurchaseOrderMaintenance@searchcontrol'); /*23062020*/
	Route::post('/poappcontrol/edit','PurchaseOrderMaintenance@editcontrol'); /*23062020*/
	Route::get('/poappbrowse','PurchaseOrderMaintenance@poappbrowse')->name('poappbrowse'); /*24062020 -- Home App Browse*/
	Route::get('/po/fetch_po_app', 'PurchaseOrderMaintenance@poappbrowse'); /*24062020 -- Paging*/
	Route::get('/poappsearch1', 'PurchaseOrderMaintenance@poappsearch'); /*25062020 -- Search*/
	Route::get('/searchhistapp', 'PurchaseOrderMaintenance@searchhistapp'); /*25062020 -- Search History Approval*/
	Route::get('/searchdetailapppo', 'PurchaseOrderMaintenance@searchdetailapppo'); /*26062020 -- Search Detail Approval */
	Route::post('/approvepo','PurchaseOrderMaintenance@approvepo'); /*26062020 -- Approve PO*/
	Route::get('/detailpoapp/{id}','PurchaseOrderMaintenance@detailpoapp'); /*23072020 -- Approve Detail Pindah Menu Sendiri*/
	Route::get('/resetapprove','PurchaseOrderMaintenance@resetapprove'); /*23072020 -- Reset Approve */
	Route::get('/po/resetpo','PurchaseOrderMaintenance@resetapprove'); /*24072020 -- Reset Approve Paging */
	Route::get('/searchresetapprove','PurchaseOrderMaintenance@searchresetapprove'); /*24072020 -- Reset Approve Search */
	Route::post('/resetpoapproval','PurchaseOrderMaintenance@resetpoapproval'); /*23072020 -- Reset Approve */
	Route::get('/poaudit','PurchaseOrderMaintenance@poaudit'); /*30072020 -- Audit Trail PO */
	Route::get('/poauditsearch','PurchaseOrderMaintenance@poauditsearch'); /*30072020 -- Search Audit Trail PO */
	Route::get('/poappaudit','PurchaseOrderMaintenance@poappaudit'); /*30072020 -- Audit Trail PO App */
	Route::get('/poappauditsearch','PurchaseOrderMaintenance@poappauditsearch'); /*30072020 -- Search Audit Trail PO App */
	Route::get('/rfqaudit','RfqMaintenanceController@rfqaudit'); /*30072020 -- Audit Trail RFQ */
	Route::get('/rfqauditsearch','RfqMaintenanceController@rfqauditsearch'); /*30072020 -- Search Audit Trail RFQ */
	Route::get('/searchnamasupp','UserMaintController@searchnamasupp'); /*03082020 -- get nama supp value*/




	Route::get('/tempcheck','PurchaseOrderMaintenance@temp_check');
	Route::get('/testingcheck','PurchaseOrderMaintenance@testing');
	Route::get('/testingstanley','PurchaseOrderMaintenance@testingstanley');
	
	// ============================FL====================================
    // Past Due Purchase Order //FL
	// Route::get('/pastduepo',  'PODasboardController@index');
	Route::get('/dash2', 'POBrwController2@index');
	Route::get('/pastduepo', 'PODasboardController@index');
	Route::get('/pastduesearch', 'PODasboardController@pastduesearch');
	Route::get('/pagination/pastduesearch','PODasboardController@pastduesearch');

	Route::get('/pastduepo2', 'PODasboardController@indexpastdue2');
	Route::get('/pastduesearch2', 'PODasboardController@pastduesearch2');
	Route::get('/pagination/pastduesearch2', 'PODasboardController@pastduesearch2');

	Route::get('/pastduepo3', 'PODasboardController@indexpastdue3');
	Route::get('/pastduesearch3', 'PODasboardController@pastduesearch3');
	Route::get('/pagination/pastduesearch3', 'PODasboardController@pastduesearch3');

	Route::get('/pagination/posearch2','PODasboardController@nbrofposearch2');
	Route::get('/posearch2','PODasboardController@nbrofposearch2');

	Route::get('/nbrofpo', 'PODasboardController@indexnbrofpo');

	Route::get('/nbrofpo1', 'PODasboardController@indexnbrofpo1');
	Route::get('/nbrofposearch', 'PODasboardController@nbrofposearch');
	Route::get('/pagination/nbrofposearch','PODasboardController@nbrofposearch');

	Route::get('/nbrofpo2', 'PODasboardController@indexnbrofpo2');
	Route::get('/nbrofposearch2', 'PODasboardController@nbrofposearch2');
	Route::get('/pagination/nbrofposearch2','PODasboardController@nbrofposearch2');

	Route::get('/nbrofpo3', 'PODasboardController@indexnbrofpo3');
	Route::get('/nbrofposearch3', 'PODasboardController@nbrofposearch3');
	Route::get('/pagination/nbrofposearch3','PODasboardController@nbrofposearch3');
	

	Route::get('/openpo', 'PODasboardController@indexopenpo');
	Route::get('/posearch3', 'PODasboardController@posearch3');
	Route::get('/pagination/posearch3','PODasboardController@posearch3');

	Route::get('/unpobysupp', 'PODasboardController@indexunpobysupp');
	Route::get('/unposearch', 'PODasboardController@unposearch');
	Route::get('/pagination/unposearch','PODasboardController@unposearch');

	Route::get('/upcoming', 'PODasboardController@indexupcomingdue');
	Route::get('/upcomingsearch', 'PODasboardController@upcomingsearch');
	Route::get('/pagination/upcomingsearch','PODasboardController@upcomingsearch');

	Route::get('/poappbrw', 'PODasboardController@indexpoappbrw');
	Route::get('/poappsearch', 'PODasboardController@poappsearch');
	Route::get('/pagination/poappsearch','PODasboardController@poappsearch');

	Route::get('/poappbrw2', 'PODasboardController@indexpoappbrw2');
	Route::get('/poappsearch2', 'PODasboardController@poappsearch2');
	Route::get('/pagination/poappsearch2','PODasboardController@poappsearch2');

	Route::get('/poappbrw3', 'PODasboardController@indexpoappbrw3');
	Route::get('/poappsearch3', 'PODasboardController@poappsearch3');
	Route::get('/pagination/poappsearch3','PODasboardController@poappsearch3');


	Route::get('/openrfq', 'PODasboardController@indexopenrfq');
	Route::get('/rfqsearch2',  'PODasboardController@rfqsearch2');
	Route::get('/pagination/rfqsearch2','PODasboardController@rfqsearch2');

	Route::get('/itemsearch', 'PODasboardController@index'); //FL

	
	// Transaction History //FL
	route::resource('thistinput', 'TrHistController@thistindex');
	Route::get('/addtrhist', 'TrHistController@addtrhist'); //FL
	Route::post('/trproses', 'TrHistController@trproses'); //FL

	Route::get('/thistinput', 'TrHistController@thistindex'); //FL
	route::post('/deletehist','TrHistController@deletehist');


	// Inventory By Supp //FL
	route::resource('tbinvbysupp', 'InvBySuppController@tbinvbysupp');
	Route::get('/invbysupp', 'InvBySuppController@index'); //FL
	Route::post('/prosesinv', 'InvBySuppController@prosesinv'); //FL
	route::post('/delete','InvBySuppController@delete');
	route::get('/supplsearch','InvBySuppController@supplsearch');
	

	// Supplier Inventory Maintenance //FL
	route::resource('tbsuppinv', 'SuppInvController@tbsuppinv');
	Route::get('/suppinv', 'SuppInvController@index'); //FL
	Route::post('/prosessupp', 'SuppInvController@prosessupp'); //FL
	route::post('/delete','SuppInvController@delete');
	route::get('/supp_search','SuppInvController@supp_search');	
	Route::get('/pagination/supp_search','SuppInvController@supp_search');


	// Price Lice Create //FL
    route::resource('/pricelist','PriceListMTController');
	Route::get('/pricelistmt', 'PriceListMTController@index'); //FL
	Route::post('/pricelist/createnew', 'PriceListMTController@createnew'); //FL
	Route::post('/pricelist/delete','PriceListMTController@delete');
	// Route::post('/pricelist/update','PriceListMTController@update');


	Route::get('/supp_search','PriceListMTController@supp_search');	

	// Route::get('/', 'PriceListMTController@index');

	
	// Route::get('/cari', 'PriceListMTController@loadData'); membuat data selalu load pada cmd
	// ============================FL====================================
		

	// RFQ Menu
	Route::get('/rfq',function() {
		return view('/rfq/allmenu');
	});

	Route::get('/pagination/viewrfq','RfqMaintenanceController@viewinput'); // 13012021
	Route::get('/searchumrfq', 'RfqMaintenanceController@searchum'); // 13012021
	Route::get('/searchumrfqedit', 'RfqMaintenanceController@searchumedit'); // 13012021

	Route::get('/suppsearch', 'RfqMaintenanceController@suppsearch');
	Route::get('/inputrfq', 'RfqMaintenanceController@viewinput');
	Route::post('/insertpurch', 'RfqMaintenanceController@insertpurch');
	Route::post('/updatepurch', 'RfqMaintenanceController@updatepurch');
	Route::get('/inputrfqsupp', 'RfqMaintenanceController@viewinputsupp');
	Route::post('/purchbid', 'RfqMaintenanceController@purchbid');
	Route::get('/downloadfile/{id}', 'RfqMaintenanceController@downloadfile')->name('downloadrfq');
	Route::get('/rfqapprove', 'RfqMaintenanceController@viewapprove');
	Route::get('/rfqsearch', 'RfqMaintenanceController@rfqsearch');
	//Route::get('/rfqsearch1', 'RfqMaintenanceController@rfqsearch1');
	Route::post('/purchupdate', 'QxCimController@purchupdate');
	Route::get('/pagination/fetch_data', 'RfqMaintenanceController@fetch_data');
	Route::get('/rfqhist','RfqMaintenanceController@viewhist');
	Route::get('/pagination/viewhist','RfqMaintenanceController@viewhist');
	Route::get('/searchhist','RfqMaintenanceController@searchhist');
	Route::get('/downloadfilesupp/{id}/{supp}', 'RfqMaintenanceController@downloadfiledet')->name('downloadrfqsupp');
	Route::get('/pagination/viewlistsupp','RfqMaintenanceController@viewlistsupp');
	Route::get('/searchsupp','RfqMaintenanceController@searchsupp');
	Route::post('cancelrfq','RfqMaintenanceController@cancelrfqpurch');
	
	Route::get('/searcholdsupp', 'RfqMaintenanceController@searcholdsupp');
	Route::get('/searcholdsuppdel', 'RfqMaintenanceController@searcholdsuppdel');
	Route::post('/addsupplierrfq', 'RfqMaintenanceController@addsupplierrfq');
	Route::get('/polast10search', 'RfqMaintenanceController@polast10search'); // 01072020 - Search Last 10 Price Item
	Route::get('/rfqlast10search', 'RfqMaintenanceController@rfqlast10search'); // 01072020 - Search Last 10 Price Item
	route::get('/rfqinputsearch','RfqMaintenanceController@rfqinputsearch'); /*Rfq Purchasing Search 06072020*/
	Route::get('/top10menu','RfqMaintenanceController@top10menu'); /*07072020 -- Top 10 Menu*/
	Route::get('/searchtop10menu','RfqMaintenanceController@searchtop10menu'); /*07072020 -- Top 10 Menu*/
	Route::get('/searchtop10menupo','RfqMaintenanceController@searchtop10menupo'); /*07072020 -- Top 10 Menu*/
	Route::get('/searchemail', 'RfqMaintenanceController@searchemail');
	
	// supp Menu
	

	Route::get('/supp', 'PoconfController@menu')->name('/po/poconf');
	route::get('/poconf','PurchaseOrderMaintenance@viewpocf');
	Route::get('/poconf/posearchcf', 'PurchaseOrderMaintenance@searchpocf'); //29072021
	Route::get('/po/fetch_datacf', 'PurchaseOrderMaintenance@viewpocf');
	Route::get('/poconfcari','PoconfController@cari')->name('/po/poconf');
	Route::get('/poddet','PoddetController@store')->name('/po/poddet');
	Route::get('/podemail','PoddetController@email');
	Route::get('/poddetcari','PoddetController@cari')->name('/po/poddet');
	Route::post('/podupd','PoddetController@insert')->name('/po/poddet');
	Route::get('/podedt','PodedtController@store')->name('/po/podedt');
	Route::post('/podedtupd','PodedtController@insert')->name('/po/podedt');
	Route::post('/podall','PoddetController@confirmall')->name('/po/poddet');
	Route::post('/podsave','PoddetController@insertall')->name('/po/poddet');
	Route::get('/popdf', 'PoconfController@pdf')->name('/po/poconf');

	Route::get('/domain', 'DomainController@index')->name('/setting/domain');
	Route::post('/domain', 'DomainController@create')->name('/setting/domain');

	Route::get('/sjmtbrw', 'SjmtController@browse');
    Route::get('/sjmt', 'SjmtController@index')->name('/sjmt');
    Route::get('/sjmtcancel', 'SjmtController@cancel');
	Route::get('/sjmtcari', 'SjmtController@search')->name('/sjmt');
	Route::get('/sjmtbrw/search', 'SjmtController@searchbrw'); // 30072021
	Route::get('/sjcrt', 'SjmtController@crt')->name('/sjcrt');
	Route::post('dynamic_dependent/fetch', 'SjmtController@line')->name('dynamicdependent.fetch');
	Route::get('/get-line-list', 'SjmtController@line')->name('/sjcrt');
	Route::get('/sjcrtdet', 'SjmtController@cari')->name('/sj/sjcrtdet');
	Route::post('/sjctsave', 'SjmtController@save')->name('/sj/sjcrtdet');
	Route::get('/sjmtedt', 'SjmtController@edit')->name('/sj/sjmtedt');
	Route::post('/sjmtupd', 'SjmtController@upd')->name('/sj/sjcrt');
	Route::post('/sjmtdel', 'SjmtController@delete')->name('/sj/sjmt');
    Route::post('/sjmtdeledt', 'SjmtController@deledt');
	Route::get('/dynamic_dependent', 'DynamicDependent@index');
	Route::post('dynamic_dependent/fetch', 'DynamicDependent@fetch')->name('dynamicdependent.fetch');
	route::post('/loadinv','InvbrwController@loadinv');
	route::post('/loaditm','InvbrwController@loaditm');
	route::post('/loadinvd','InvbrwController@loadinvd');
	Route::get('/locbrw', 'InvbrwController@index')->name('/inv/locbrw');
	Route::get('/itmbrw', 'InvbrwController@itmbrw')->name('/inv/locbrw');
	Route::get('/site', 'SiteController@view')->name('/setting/site');
	Route::post('/sitecreate', 'SiteController@create')->name('/setting/site');
	Route::post('/updatesite', 'SiteController@update')->name('/setting/site');
	Route::post('/sitedelete', 'SiteController@hapus')->name('/setting/site');
	Route::get('/dash', 'InvbrwController@dash')->name('/inv/locbrw');
	Route::get('/dashpurdet', 'InvbrwController@purdet');
	Route::get('/dashmandet', 'InvbrwController@mandet');
	Route::get('/expitem', 'InvbrwController@expitm');
    Route::get('/noinv', 'InvbrwController@noinv');
    Route::get('/dashpurdet1', 'InvbrwController@purdet1');
	Route::get('/dashpurdet2', 'InvbrwController@purdet2');
	Route::get('/dashpurdet3', 'InvbrwController@purdet3');
	Route::get('/dashpurdet4', 'InvbrwController@purdet4');
	Route::get('/dashpurdet5', 'InvbrwController@purdet5');
	Route::get('/dashmandet1', 'InvbrwController@mandet1');
	Route::get('/dashmandet2', 'InvbrwController@mandet2');
	Route::get('/dashmandet3', 'InvbrwController@mandet3');
	Route::get('/dashmandet4', 'InvbrwController@mandet4');
	Route::get('/dashmandet5', 'InvbrwController@mandet5');
	Route::get('/bstock', 'InvbrwController@bstock');
	Route::get('/podmail','SendEMailController@index');
    Route::post('/sendemailx', 'PoddetController@send');


	Route::get('/itmmenu', 'itmsetupController@menu');
    Route::get('/itmsetup', 'itmsetupController@index')->name('itmsetup');
	Route::get('/itmcrt', 'itmsetupController@create');
    Route::post('/itmsave', 'itmsetupController@save');
    Route::get('/itmedt', 'itmsetupController@edit');
    Route::get('/itmreqedt', 'itmsetupController@rfqedit');
    Route::post('/itmupd', 'itmsetupController@upd');
    Route::post('/itmrequpd', 'itmsetupController@rfqupd');
    Route::post('/itmdel', 'itmsetupController@hapus');
    Route::get('/itemrfqset', 'itmsetupController@rfqindex')->name('itemrfqset');
    Route::get('/itmreqcrt', 'itmsetupController@reqcreate');
    Route::post('/itmreqsave', 'itmsetupController@reqsave');
    Route::post('/itmreqdel', 'itmsetupController@reqhapus');
    Route::get('/itmmstr', 'itmsetupController@itmmstr');
    Route::get('/itmrfqmstr', 'itmsetupController@itmrfqmstr');
    Route::get('/itmmstredt', 'itmsetupController@itmmstredt');
    Route::post('/itmmstrupd', 'itmsetupController@itmmstrupd');
    Route::get('/itmmstrcari', 'itmsetupController@itmmstr');
    Route::get('/itmrfqmstrcari', 'itmsetupController@itmrfqmstr');
	Route::post('/loaditmreq', 'itmsetupController@loaditm');
	
	//Maintanance  Department
	route::resource('deptmaint', 'DepartmentController');
	route::post('/updatedept', 'DepartmentController@updatedept');
	route::post('/deletedept', 'DepartmentController@deletedept');


	//RFP Approval Menu
	route::get('/rfp', function(){
		return view('/rfp/allmenu');
	});

	route::get('/inputrfp', 'RfpMaintenanceController@viewinput');
	route::get('/searchshipto', 'RfpMaintenanceController@searchshipto');
	route::get('/searchitemdesc', 'RfpMaintenanceController@searchitemdesc');
	route::get('/searchum', 'RfpMaintenanceController@searchum');
	route::post('/insertrfp', 'RfpMaintenanceController@insertrfp');
	route::get('/inputrfp/rfpinputsearch', 'RfpMaintenanceController@rfpinputsearch');// 29072021
	// route::get('/rfpinputsearch/fetch_data', 'RfpMaintenanceController@rfpinputsearch');// 29072021
	route::get('/searchold', 'RfpMaintenanceController@editrfpmaint');
	route::post('/updaterfpmaint', 'RfpMaintenanceController@updaterfpmaint');
	Route::post('/cancelrfp','RfpMaintenanceController@cancelrfp');
	route::get('/searchroute', 'RfpMaintenanceController@searchroute');
	route::get('/rfpapproval', 'RfpMaintenanceController@viewrfpapp')->name('rfpapproval');
	route::get('/detailrfpapp/{id}', 'RfpMaintenanceController@detailrfpapp');
	route::post('/approverfp', 'QxCimController@approverfp');
	route::get('/rfpapproval/rfpappsearch', 'RfpMaintenanceController@rfpappsearch');// 29072021
	route::get('/routerfp', 'RfpMaintenanceController@routerfp');
	route::get('/searchpo', 'RfpMaintenanceController@searchpo');
	route::get('rfphist/histsearch', 'RfpMaintenanceController@histsearch');
	route::get('/rfphist', 'RfpMaintenanceController@viewhist');
	route::get('/rfpaudit', 'RfpMaintenanceController@viewapphist');
	route::get('/searchdets', 'RfpMaintenanceController@searchdets');
	route::get('/apphistsearch', 'RfpMaintenanceController@apphistsearch');
	route::get('/rfputil', 'RfpMaintenanceController@viewutil');
	route::get('/rfputil/utilrfpsearch', 'RfpMaintenanceController@utilrfpsearch'); // 29072021
	// route::get('/utilrfpsearch/fetch_data', 'RfpMaintenanceController@utilrfpsearch'); 29072021
	route::post('/resetrfpapp', 'RfpMaintenanceController@resetrfpapp');
	// route::get('/histsearch/fetch_data', 'RfpMaintenanceController@histsearch'); 29072021


   	//RFP Approval Control
   route::get('/rfpapprove', 'RfpMaintenanceController@controlindex');
   route::get('/rfpapprove/createnew', 'RfpMaintenanceController@createnewcontrol');
   route::post('/rfpapprove/edit', 'RfpMaintenanceController@editcontrol');
   route::get('/searchrfpapp', 'RfpMaintenanceController@searchcontrol');

   
   //Load Conversi 13-01-2021
   route::get('/itemconvmenu', 'AlertItemController@itemconvmenu');
   route::get('/ummastermenu', 'AlertItemController@ummastermenu');
   route::get('/itemconvsearch', 'AlertItemController@itemconvsearch');
   route::get('/ummastersearch', 'AlertItemController@ummastersearch');
   route::get('/loadconum','AlertItemController@loaditemconv');
   route::get('/loadum','AlertItemController@loadum');
	//    route::get('/loadconumbat','AlertItemController@loaditemconvbat');
	//    route::get('/loadumbat','AlertItemController@loadumbat');

	// WSA + Qxtend
	route::get('wsasmenu','WSAController@index');
	route::post('wsasupdate','WSAController@wsaupdate');
	

	// inventory Menu
	Route::get('/inv',function() {
		return view('/inv/invmenu');
	});

	// Send Email
	Route::post('/sendnotif','SendEmailController@send');
	Route::get('/notifrfq', function () {
	    return view('/rfq/notifrfq');
	});

	Route::get('/setting', function () {
	    return view('/setting/setting');
	});
	
   Route::get('/getumitem', 'RfpMaintenanceController@getumitem');
   
   Route::post('/mark-as-read', 'AlertMaintController@notifread')->name('notifread');
   Route::post('/mark-all-as-read', 'AlertMaintController@notifreadall')->name('notifreadall');

   // Menu Scanner
   route::get('/menuscan','BarcodeController@index');

   route::get('/testbroad', function () {
		event(new App\Events\StatusLiked('Someone'));
		return "Event has been sent!";
	});

	route::get('/budgetingapprove', 'UserMaintController@budgetapprove');
	route::post('/inputbudgetappr', 'UserMaintController@inputbudgetappr');


	// Send Wa

	route::post('sendwa','WhatsAppController@sendwa');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');




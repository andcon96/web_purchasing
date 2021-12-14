@extends('layout.layout')

@section('menu_name','RFP Conversion to PO')
@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFP Conversion to PO</li>
</ol>
@endsection


@section('content')
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	<!--CSS Khusus File Input Upload-->
	<style type="text/css">

	    .input-file-container {
	      position: relative;
	      width: 225px;
	    } 
	    .js .input-file-trigger {
	      display: block;
	      padding: 14px 45px;
	      background: #18695A;
	      color: #fff;
	      font-size: 1em;
	      transition: all .4s;
	      cursor: pointer;
	    }
	    .js .input-file {
	      position: absolute;
	      top: 0; left: 0;
	      width: 225px;
	      opacity: 0;
	      padding: 14px 0;
	      cursor: pointer;
	    }
	    .js .input-file:hover + .input-file-trigger,
	    .js .input-file:focus + .input-file-trigger,
	    .js .input-file-trigger:hover,
	    .js .input-file-trigger:focus {
	      background: #12433A;
	      color: #FFF;
	    }

	    .file-return {
	      margin: 0;
	    }
	    .file-return:not(:empty) {
	      margin: 1em 0;
	    }
	    .js .file-return {
	      font-style: italic;
	      font-size: .9em;
	      font-weight: bold;
	    }
	    .js .file-return:not(:empty):before {
	      content: "Selected file: ";
	      font-style: normal;
	      font-weight: normal;
	    }

	    tbody{
	        font-size: 14px;

	    }

	    label, .page-item{
	        font-size:16px !important;
	    }

	    h1{
	        color: black !important;
	    }

	    thead{
	        background-color: #4e73df;
	        text-align: left;
	        color:white !important;
	    }

	    th{
	        font-size: 18px;
	    }

	    tr:nth-child(even) {background-color: #f2f2f2;}

	    tr{
	      border-bottom: 1px solid #6D6F70 !important;
	    }

	    #dataTable thead,
	    #dataTable tbody,
	    #dataTable td{
	        vertical-align: middle;
	        color:#000000;
	        border: none;
	        font-size:16px;
	        font-weight: 600;
	        padding: 5px 10px 5px 10px;
	    }


	    .bt-action{
	      font-size: 16px;
	      width: 150px;
	      background-color:#4e73df;
	      color:white;
	    }

	    label, .page-item{
	        font-size:16px !important;
	    }
	  
	    tbody .fas{
	      margin-right: 5px;
	      margin-left: 5px;
	    }

	    .select2-results__option { 
	      font-size: 16px;
	    }

	    .select2-selection__rendered {
	      line-height: 31px !important;
	      font-size:16px !important;
	    }
	    .select2-container .select2-selection--single {
	        height: 36px !important;
	      font-size:16px !important;
	    }
	    .select2-selection__arrow {
	        height: 36px !important;
	    }

	    @media only screen and (max-width: 767px) {
	        
	    .seconddata{
	        margin-top:15px;
	        margin-left:12px;
	        }
		}
		
	</style>

	<script>
		$( function(){
			$('#datefrom').datepicker({
				dateFormat : 'yy-mm-dd'
			});
			$('#dateto').datepicker({
				dateFormat : 'yy-mm-dd'
			});
		});	
	</script>

	@if(session('errors'))
	    <div class="alert alert-danger">
	        @foreach($errors as $error)
	            <li>{{ $error }}</li>
	        @endforeach
	    </div>
	@endif

	@if(session('error'))
	    <div class="alert alert-danger" id="getError">
	        {{ session()->get('error') }}
	    </div>
	@endif

	@if(session()->has('updated'))
	    <div class="alert alert-success">
	        {{ session()->get('updated') }}
	    </div>
	@endif

	<form id="update" class="form-horizontal" method="POST" action="/approverfp">
    	{{csrf_field()}}
			<?php 
				// if($alert == null){
				// 	$nomor1 = '';
				// }else{
				// 	$nomor1 = $alert->xrfq_rfp_prefix.$date.'-'.$alert->xrfq_rfp_nbr;
				// }
            ?>
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="hidden" name="apporder" id="apporder" value="{{$approver->xrfp_app_order}}">
			<div class="modal-body">
				<div class="form-group row col-md-12">
    				<label for="supp" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Supplier :') }}</label>
					<div class="col-md-2">
						<input type="text" id="supp" name="supp" class="form-control" value="{{$norfp->xrfp_supp}}" required readonly autofocus autocomplete="off">
					</div>

	    			<label for="enduser" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('End User :') }}</label>
					<div class="col-md-2">
						<input type="text" id="enduser" name="enduser" class="form-control" value="{{$norfp->xrfp_enduser}}" autofocus readonly autocomplete="off">
					</div>

					<label for="site" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Site :') }}</label>
					<div class="col-md-2">
						<input type="text" id="site" name="site" class="form-control" value="{{$norfp->xrfp_site}}" autofocus readonly autocomplete="off">
					</div>
			    </div>

			    <div class="form-group row col-md-12">
				    <label for="shipto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Ship-to :') }}</label>
					<div class="col-md-2">
						<input type="text" id="shipto" name="shipto" class="form-control" value="{{$norfp->xrfp_shipto}}" autofocus readonly autocomplete="off">
					</div>

					<label for="u_dept" class="col-md-2 col-form-label text-md-right">{{ __('Dept :') }}</label>
					<div class="col-md-2">
						<input type="text" id="dept" name="dept" class="form-control" value="{{$norfp->xrfp_dept}}" readonly autocomplete="off">
					</div>

					<label for="rfpnumber" class="col-md-2 col-form-label text-md-right">{{ __('RFP Number :') }}</label>
			    	<div class="col-md-2">
						<input id="rfpnumber" type="text" class="form-control" name="rfpnumber" 
							value="{{$norfp->xrfp_nbr}}" readonly autofocus>
					</div>
				</div>

				<div class="table-responsive form-group row">
					<div class="col-md-12">
						<table id="rfpapp" class="table edit-list">
							<thead>
								<tr>
									<th style="width: 25%;">Item</th>
									<th style="width: 12%;">Need Date</th>
									<th style="width: 12%;">Due Date</th>
                                    <th style="width: 12%;">Qty Ordered</th>
                                    <th style="width: 10%;">UM</th>
									<th style="width: 10%;">Info</th>
								</tr>
							</thead>
				    		<tbody>
                                @forelse($data as $data)
                                <tr>
                                    <td>{{$data->itemcode}} -- {{$data->xitemreq_desc}}</td>
                                    <td>{{$data->need_date}}</td>
                                    <td>{{$data->due_date}}</td>
                                    <td>{{$data->qty_order}}</td>
                                    <td>{{$data->xitemreq_um}}</td>
                                    <td>
                                        <a href="" class="infopo" data-toggle='modal' data-target="#infopoModal"
                                            data-itemcode="{{$data->itemcode}}" data-itemdesc="{{$data->xitemreq_part}}">
                                            <i class="fas fa-info-circle fa-lg"></i>
                                        </a>
                                    </td>
                                    @empty
                		                <tr>
                	                        <td colspan="7" style="color:red"><center>No Data Available</center> </td>
        		                        </tr>
                                </tr>
                                @endforelse
							</tbody>
						</table>
					</div>
                </div>
                
                <div class="form-group row">
                    <label for="e_reason" class="col-md-2 col-form-label text-md-right">{{ __('Reason') }}</label>
                    <div class="col-md-9">
                        <input id="e_reason" type="text" class="form-control" name="e_reason" value="" autofocus autocomplete="off">
                    </div>
				</div>
				
				@if($nextapprover == 1)
					<div class="form-group row">
						<label for="convert" class="col-md-4 col-form-label text-md-right">{{ __('Convert to') }}</label>
						<div class="col-md-2">
							<select class="form-control" id="convertto" name="convertto" required>
								<option value="RFP">Just Approved</option>
								<option value="RFQ">RFQ</option>
								<option value="PP">Purchase Plan</option>
							</select>
						</div> 
					</div>
				@endif
			</div>

			<div class="modal-footer">
				<a href="{{url()->previous()}}" id="btnback" class="btn bt-action">Cancel</a>
                <input type="hidden" name="rfp_approver" id="rfp_approver" value="{{$approver->xrfp_app_approver}}">
                <input type="hidden" name="rfp_altapprover" id="rfp_altapprover" value="{{$approver->xrfp_app_alt_approver}}">
                <input type="hidden" name="user_session" id="user_session" value="{{Session::get('userid')}}">
                <button type="submit" class="btn btn-success bt-action" name="action" value="confirm" id="btnapp">Approve</button>
                <button type="submit" class="btn btn-success bt-action" name="action" value="reject" id="btnreject">Reject</button>
				<button type="button" class="btn bt-action" id="e_btnloading" style="display: none;">
					<i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;Loading
                </button>
    		</div>
    </form>
    
    <div class="modal fade" id="infopoModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Open PO Information</h5>
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
                                        <th style="width:15%">Qty Order</th>
                                        <th style="width:15%">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody id='bodytop10item'>
                                    
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

	<script>
		$(document).ready(function(){
            var approver = document.getElementById('rfp_approver').value;
            var altapprover = document.getElementById('rfp_altapprover').value;
            var session = document.getElementById('user_session').value;

            if(session != approver && session != altapprover){
                document.getElementById('btnapp').style.display = 'none';
                document.getElementById('btnreject').style.display = 'none';
                document.getElementById('e_reason').readOnly = true;
            }else{
                document.getElementById('btnapp').style.display = '';
                document.getElementById('btnreject').style.display = '';
                document.getElementById('e_reason').readOnly = false;
            }
        });
	</script>

@endsection

@section('script')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>

@endsection
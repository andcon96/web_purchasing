@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFQ Data Maintenance</li>
</ol>
@endsection

@section('content')

@if(session('errors'))
    <div class="alert alert-danger">
        @foreach($errors as $error)
            <li>{{ $error }}</li>
        @endforeach
    </div>
@endif

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

<button  class="btn bt-action" style="margin-left:10px;" data-toggle="modal" data-target="#createModal">
Create RFQ</button>
<button  class="btn bt-action" style="margin-left:10px;" data-toggle="modal" data-target="#prevModal">
Last 10 PO/RFQ</button>
<br><br>


<!--Search By RFQ Number-->
<div class="form-group row col-md-12">
    <label for="s_rfqnumber" class="col-md-2 col-form-label">{{ __('RFQ No.') }}</label>
    <div class="col-md-3">
        <input id="s_rfqnumber" type="text" class="form-control" name="s_rfqnumber" 
        value="" autofocus>
    </div>
    <label for="s_itemreq" class="col-md-2 col-form-label text-md-right">{{ __('Item No.') }}</label>
    <div class="col-md-3">
        <input id="s_itemreq" type="text" class="form-control" name="s_itemreq" 
        value="" autofocus>
    </div>
    <div class="offset-0">
        <input type="button" class="btn bt-ref" 
        id="btnsearch" value="Search" />
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px"><i class="fa fa-sync"></i></button>
    </div>
</div>

<!--Table-->
@include('rfq.loadpurch')
<br>

<div class="modal fade" id="createModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create RFQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form id="createnew" class="form-horizontal" enctype="multipart/form-data" method="POST" action="/insertpurch">
            {{ csrf_field() }}

            <?php if($alert == null){
                $nomor = '';
            }else{
                $nomor = $alert->xrfq_prefix.$date.'-'.$alert->xrfq_nbr;
            }
            ?>

            <div style="display:none;">
                <label for="rfqnumber" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="rfqnumber" type="text" class="form-control" name="rfqnumber" 
                        value="<?php echo $nomor; ?>" readonly autofocus>
                        @if ($errors->has('rfqnumber'))
                            <span class="help-block">
                                <strong>{{ $errors->first('rfqnumber') }}</strong>
                            </span>
                        @endif
                    </div>
            </div>
                    

            <div class="modal-body">

                <div class="form-group row">

                    <label for="itempart" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Item No.') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <select id="itempart" class="form-control itempart" name="itempart" style="font-size:16px;" required autofocus>
                            <option value="" style="font-size:16px !important;"> Select Data </option>
                            @foreach($part as $part)
                                <option value="{{ $part->xitemreq_part }}" style="font-size:16px !important;"> {{$part->xitemreq_part.'-'.$part->xitemreq_desc}} </option>
                            @endforeach
                        </select>
                        <!--
                        <input id="itempart" type="text" class="form-control" name="itempart" value="" required autofocus>
                        -->
                        @if ($errors->has('itempart'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itempart') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="site" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Site') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <select id="site" class="form-control site" name="site" style="font-size:16px;" required autofocus>
                            <option value="" style="font-size:16px !important;"> Select Data </option>
                            @foreach($site as $site)
                                <option value="{{ $site->xsite_site }}" style="font-size:16px !important;"> {{$site->xsite_site}} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('site'))
                            <span class="help-block">
                                <strong>{{ $errors->first('site') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="qtyreq" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Qty Req.') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="qtyreq" type="number" class="form-control seconddata" name="qtyreq" autocomplete="off" value="" required autofocus>
                        @if ($errors->has('qtyreq'))
                            <span class="help-block">
                                <strong>{{ $errors->first('qtyreq') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="startdate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Start Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="startdate" type="text" class="form-control startdate" name="startdate" 
                        value="{{ Carbon\Carbon::parse($date)->format('d/m/Y')  }}" readonly autofocus>
                        @if ($errors->has('duedate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('duedate') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="duedate" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <!--
                        <input id="duedate" type="date" class="form-control duedate" name="duedate" 
                        value="" required autofocus>
                        -->
                        <input type="text" id="duedate" class="form-control duedate seconddata" name='duedate' placeholder="DD/MM/YYYY"
                            required autofocus autocomplete="off">
                        @if ($errors->has('duedate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('duedate') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="remarks" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Remarks') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="remarks" class="form-control" name="remarks" maxlength="40" autofocus autocomplete="off"></input>
                        @if ($errors->has('remarks'))
                            <span class="help-block">
                                <strong>{{ $errors->first('remarks') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="pricemin" type="number" class="form-control" name="pricemin" value="" autocomplete="off" autofocus>
                        @if ($errors->has('pricemin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemin') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="pricemax" type="number" class="form-control seconddata" name="pricemax" value=""  autocomplete="off" autofocus>
                        @if ($errors->has('pricemax'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemax') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pricemax" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Supplier') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <table id='suppTable' class='table order-list'>
                            <thead>
                                <tr>
                                    <th style="width:60%">Supplier Name</th>
                                    <th style="width:40%">Action</th>
                                </tr>
                            </thead>
                            <tbody id='modalbody'>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <input type="button" class="btn btn-lg btn-block" 
                                        id="addrow" value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Upload') }}</label>
                    <div class="col-md-7 col-lg-8 input-file-container">  
                        <input type="hidden" id="filename" name="filename" value="">
                        <input class="input-file" id="file" type="file" name="file">
                        <label tabindex="0" for="file" class="btn btn-info input-file-trigger" style="font-size:16px;">Select a file</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right"></label>
                    <div class="col-md-7 col-lg-8 input-file-container">  
                        <p class="file-return"></p>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-8 offset-md-3" style="text-align: center;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmit" required>
                          <label class="custom-control-label" for="cbsubmit">Confirm to submit</label>
                        </div>
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

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Edit RFQ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{url('/updatepurch')}}" id="update">
                {{ csrf_field() }}

                <input type="hidden" name='rfqsite' id='rfqsite'>

                <div class="modal-body">
                    <?php 
                        if($alert == null){
                            $nomor1 = '';
                        }else{
                            $nomor1 = $alert->xrfq_prefix.$date.'-'.$alert->xrfq_nbr;
                        }
                    ?>

                    <div class="form-group row">
                        <label for="u_rfqnumber" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ No.') }}</label>
                        <div class="col-md-7 col-lg-3">
                            <input id="u_rfqnumber" type="text" class="form-control" name="u_rfqnumber" 
                            value="<?php echo $nomor1 ?>" readonly autofocus>
                            @if ($errors->has('rfqnumber'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('rfqnumber') }}</strong>
                                </span>
                            @endif
                        </div>

                        <label for="itempart" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Item No.') }}</label>
                        <div class="col-md-7 col-lg-3 seconddata">
                            <input id="u_itempart" type="text" class="form-control" name="u_itempart" 
                            readonly autofocus>
                            <!--
                            <input id="itempart" type="text" class="form-control" name="itempart" value="" required autofocus>
                            -->
                            @if ($errors->has('itempart'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('itempart') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="u_startdate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Start Date') }}</label>
                        <div class="col-md-7 col-lg-3">
                            <input id="u_startdate" type="date" class="form-control" name="u_startdate" 
                            value="" readonly autofocus>
                            @if ($errors->has('duedate'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('duedate') }}</strong>
                                </span>
                            @endif
                        </div>
                        <label for="u_duedate" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Due Date') }}</label>
                        <div class="col-md-7 col-lg-3 seconddata">
                            <input id="u_duedate" type="date" class="form-control" name="u_duedate" 
                            value="" readonly autofocus>
                            @if ($errors->has('duedate'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('duedate') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="u_remarks" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Remarks') }}</label>
                        <div class="col-md-7 col-lg-8">
                            <textarea id="u_remarks" class="form-control" name="u_remarks" rows="4" readonly="" autofocus></textarea>
                            @if ($errors->has('remarks'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('remarks') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="u_qtyreq" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Qty Requested') }}</label>
                        <div class="col-md-7 col-lg-3">
                            <input id="u_qtyreq" type="text" class="form-control" name="u_qtyreq" value=""  autocomplete="off" required autofocus>
                            @if ($errors->has('qtyreq'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('qtyreq') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="u_pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                        <div class="col-md-7 col-lg-3">
                            <input id="u_pricemin" type="text" class="form-control" name="u_pricemin" value=""  autocomplete="off" autofocus>
                            @if ($errors->has('pricemin'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('pricemin') }}</strong>
                                </span>
                            @endif
                        </div>
                        <label for="u_pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Price Max') }}</label>
                        <div class="col-md-7 col-lg-3 seconddata">
                            <input id="u_pricemax" type="text" class="form-control" name="u_pricemax" value=""  autocomplete="off" autofocus>
                            @if ($errors->has('pricemax'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('pricemax') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row offset-md-1 offset-lg-1 col-lg-11 col-md-11">
                        <table id='suppTable' class='table supp-list' style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th style="width:60%">Supplier</th>
                                    <th style="width:40%">Send Request</th>
                                </tr>
                            </thead>
                            <tbody id='oldsupplier'>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <input type="button" class="btn btn-lg btn-block" 
                                        id="addrowsupp" value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group row md-form">
                        <div class="col-12" style="text-align: center;">
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="cbsubmitedit" required>
                            <label class="custom-control-label" for="cbsubmitedit">Confirm to submit</label>
                            </div>
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

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Cancel RFQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" method="POST" action="/cancelrfq" id="delete">
            {{ csrf_field() }}

            <input type="hidden" name='cs_rfqnbr' id='cs_rfqsite'>

            <div class="modal-body">
                <div class="form-group row">
                    <label for="d_rfqnbr" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ No.') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_rfqnbr" type="text" class="form-control" name="d_rfqnbr" 
                        value="" readonly autofocus>
                    </div>

                    <label for="d_itempart" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Item No.') }}</label>
                    <div class="col-md-7 col-lg-3 seconddata">
                        <input id="d_itempart" type="text" class="form-control" name="d_itempart" 
                        readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="d_startdate" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Start Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_startdate" type="date" class="form-control" name="d_startdate" 
                        value="" readonly autofocus>
                        @if ($errors->has('duedate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('duedate') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="d_duedate" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3 seconddata">
                        <input id="d_duedate" type="date" class="form-control" name="d_duedate" 
                        value="" readonly autofocus>
                        @if ($errors->has('duedate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('duedate') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="d_remarks" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Remarks') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <textarea id="d_remarks" class="form-control" name="d_remarks" rows="4" readonly autofocus></textarea>
                        @if ($errors->has('remarks'))
                            <span class="help-block">
                                <strong>{{ $errors->first('remarks') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="d_qtyreq" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Qty Requested') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_qtyreq" type="text" class="form-control" name="d_qtyreq" value=""  autocomplete="off" readonly autofocus>
                        @if ($errors->has('qtyreq'))
                            <span class="help-block">
                                <strong>{{ $errors->first('qtyreq') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="d_pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="d_pricemin" type="text" class="form-control" name="d_pricemin" value=""  autocomplete="off" autofocus readonly>
                        @if ($errors->has('pricemin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemin') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="d_pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right seconddata">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3 seconddata">
                        <input id="d_pricemax" type="text" class="form-control" name="d_pricemax" value=""  autocomplete="off" autofocus readonly>
                        @if ($errors->has('pricemax'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemax') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row offset-md-1 offset-lg-1 col-lg-11 col-md-11">
                    <table id='suppTable' class='table supp-list'>
                        <thead>
                            <tr>
                                <th style="width:60%">Supplier</th>
                                <th style="width:40%">Send Request</th>
                            </tr>
                        </thead>
                        <tbody id='d_oldsupplier'>
                            
                        </tbody>
                    </table>
                </div>
                <div class="form-group row offset-md-1 col-lg-11 d-flex">
                    <label style="margin-top:8px;">Do You Want To Send Email to Supplier ?</label>
                    <div class="col-lg-5">
                        <select id="kirimemail" class="form-control" name="kirimemail" required autofocus>
                            <option value="">Select Data</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div> 
                </div>
                <div class="form-group row md-form">
                    <div class="col-md-12" style="text-align: center;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmitdel" required>
                          <label class="custom-control-label" for="cbsubmitdel">Confirm to submit</label>
                        </div>
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

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Email RFQ to Supplier</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" method="POST" action="/addsupplierrfq" id="addnewsup">
            {{ csrf_field() }}

            <div class="modal-body">

                <div class="form-group row">
                    <label for="add_rfqnbr" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="add_rfqnbr" type="text" class="form-control" name="add_rfqnbr" 
                        value="" readonly autofocus>
                    </div>

                </div>
                
                <div class="form-group row">
                    <label for="add_item" class="col-md-4 col-lg-3 col-form-label text-md-right seconddata">{{ __('Item') }}</label>
                    <div class="col-md-7 col-lg-8 seconddata">
                        <input id="add_item" type="text" class="form-control" name="add_item" 
                        readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row offset-md-1 offset-lg-1 col-lg-11 col-md-11">
                    <table id='suppTable' class='table supp-list'>
                        <thead>
                            <tr>
                                <th style="width:60%">Supplier</th>
                                <th style="width:40%">Status</th>
                            </tr>
                        </thead>
                        <tbody id='oldsupplier1'>
                            
                        </tbody>
                    </table>
                </div>

                <div class="form-group row md-form">
                    <div class="col-md-12" style="text-align: center;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmitadd" required>
                          <label class="custom-control-label" for="cbsubmitadd">Confirm to submit</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="a_btnclose" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success bt-action" id="a_btnconf">Save</button>
              <button type="button" class="btn bt-action" id="a_btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

<div class="modal fade" id="prevModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
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
                <label for="item10" class="col-2 col-sm-2 col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Item No.') }}</label>
                <div class="col-7 col-sm-7 col-md-7 col-lg-5">
                    <select id="item10" class="form-control item10 seconddata" name="item10" required autofocus>
                        @foreach($item as $item)
                        <option value="{{$item->xitemreq_part}}"> {{$item->xitemreq_part}} - {{$item->xitemreq_desc}} </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn bt-action btnsearchtop10" id='btnsearchtop10' style="width:100px;">Search</button>
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
                    <table id='top10rfq' class='table supp-list'>
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
          <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Close</button>
        </div>

    </div>
  </div>
</div>

<div class="modal fade" id="viewdetail"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Detail RFQ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

        <div class="modal-body">
            <div class="form-group row">
                <label for="item10" class="col-2 col-sm-2 col-md-2 col-lg-2 col-form-label text-md-right">{{ __('RFQ No.') }}</label>
                <div class="col-7 col-sm-7 col-md-7 col-lg-5">
                    <input type="text" class="form-control" id="rfqid" name="rfqid" readonly>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12 col-md-12" style="overflow-x: auto; display: block;white-space: nowrap;">
                    <table id='top10item' class='table supp-list'>
                        <thead>
                            <tr>
                                <th style="width:15%">Supp Code</th>
                                <th style="width:20%">Supp Name</th>
                                <th style="width:10%">Qty Req</th>
                                <th style="width:8%">Qty Pro</th>
                                <th style="width:8%">Qty Pur</th>
                                <th style="width:15%">Price Pro</th>
                                <th style="width:10%">Status</th>
                                <th style="width:15%">Date Purchase</th>
                            </tr>
                        </thead>
                        <tbody id='bodydetail'>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Close</button>
        </div>

    </div>
  </div>
</div>



@endsection




@section('scripts')

<script>    
    $(document).on('click','.pagination a', function(e){
        e.preventDefault();

        //alert('123');
        var page = $(this).attr('href').split('?page=')[1];
        var value = document.getElementById("s_rfqnumber").value;
        var code = document.getElementById("s_itemreq").value;

        //console.log(page);
        getData(page,value,code);

    });

    function getData(page,value,code){
        $.ajax({
            url: '/rfqinputsearch?page='+ page+'&rfq='+value+'&code='+code,
            type: "get",
            datatype: "html" 
        }).done(function(data){
                console.log('Page = '+ page);

                $(".tag-container").empty().html(data);

        }).fail(function(jqXHR, ajaxOptions, thrownError){
            Swal.fire({
                icon: 'error',
                text: 'No Response From Server',
            })
        });
    }


    $( function() {
        $( "#duedate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });

    function selectRefresh() {
      $('.selectpicker').selectpicker().focus();
    }
    
    $('#createnew').submit(function(event) {

        event.preventDefault(); //this will prevent the default submit

        var duedate = document.getElementById("duedate").value.split('/');

        var startdate = document.getElementById("startdate").value.split('/');

        var new_startdate = new Date(startdate[2].concat('-',startdate[1],'-',startdate[0]));
        var new_duedate = new Date(duedate[2].concat('-',duedate[1],'-',duedate[0]));

        var pricemin = document.getElementById("pricemin").value;
        var pricemax = document.getElementById("pricemax").value;

        var qtyreq = document.getElementById("qtyreq").value;
        //console.log(duedate);

        var reg = /^(\s*|\d+)$/;
        var regqty = /^(\s*|\d+\.\d*|\d+)$/;


        if(qtyreq == 0){
            alert('Qty Requested cannot be 0');
            return false;
        }else if(!regqty.test(qtyreq)){
            alert('Qty Requested Must be number');
            return false;
        }else if(new_startdate > new_duedate){
            alert("Due Date cannot be earlier than Start Date");
            return false;
        }else if(parseInt(pricemin) > parseInt(pricemax)){
            alert("Price Min is greater than Price Max");
            return false;
        }else if(!reg.test(pricemin)){
            alert("Price Min must be number or empty");
            return false;
        }else if(!reg.test(pricemax)){
            alert("Price max must be number or empty");
            return false;
        }else{
            document.getElementById('btnclose').style.display = 'none';
            document.getElementById('btnconf').style.display = 'none';
            document.getElementById('btnloading').style.display = '';
            $(this).unbind('submit').submit();
        }
    });

    $('#update').submit(function(event) {


        var qtyreq = document.getElementById("u_qtyreq").value;
        var pricemin = document.getElementById("u_pricemin").value;
        var pricemax = document.getElementById("u_pricemax").value;

        var reg = /^(\s*|\d+)$/;
        var regqty = /^(\s*|\d+\.\d*|\d+)$/;

        if(!regqty.test(qtyreq)){
            alert('Qty must be number or "."');
            return false;
        }else if(!reg.test(pricemin)){
            alert("Price Min must be number or empty");
            return false;
        }else if(!reg.test(pricemax)){
            alert("Price max must be number or empty");
            return false;
        }else if(parseInt(pricemin) > parseInt(pricemax)){
            alert('Price Min is greater than Price Max');
            return false;
        }else if(qtyreq == 0){
            alert('Qty cannot be 0');
            return false;
        }else{
            document.getElementById('e_btnclose').style.display = 'none';
            document.getElementById('e_btnconf').style.display = 'none';
            document.getElementById('e_btnloading').style.display = ''; 
            $(this).unbind('submit').submit();
        }
    });

    $('#delete').submit(function(event) {

        document.getElementById('d_btnclose').style.display = 'none';
        document.getElementById('d_btnconf').style.display = 'none';
        document.getElementById('d_btnloading').style.display = ''; 
        $(this).unbind('submit').submit();

    });

    $('#addnewsup').submit(function(event) {
        document.getElementById('a_btnclose').style.display = 'none';
        document.getElementById('a_btnconf').style.display = 'none';
        document.getElementById('a_btnloading').style.display = ''; 
        $(this).unbind('submit').submit();
 
    });

    $('#btnsearch').on('click',function(){
      var value = document.getElementById("s_rfqnumber").value;
      var code = document.getElementById("s_itemreq").value;

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqinputsearch") }}",
          data:{
            rfq : value,
            code : code,
          },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
            success:function(data){
                //$('tbody').html(data);
                console.log(data);
                $(".tag-container").empty().html(data);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
      });
    });

    $('#btnrefresh').on('click',function(){
      var value = '';
      var code = '';

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("rfqinputsearch") }}",
          data:{
            rfq : value,
            code : code,
          },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                $('#loader').removeClass('hidden')
            },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
          },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
                $('#loader').addClass('hidden')
            },
      });
    });


    $(document).on('change','#pricemin,#pricemax',function(){
        var pricemin = document.getElementById("pricemin").value;
        var pricemax = document.getElementById("pricemax").value;
        
        if(pricemin != '' && pricemax != ''){
            if(parseInt(pricemin) > parseInt(pricemax)){
                alert('Price Min is greater than Price Max');
                document.getElementById("pricemin").focus();
            }
        }
    }); 

    $(document).on('change','.itempart',function(){ // Click to only happen on announce links
     var value = $(this).val();

     jQuery.ajax({
          type : "get",
          url : "{{URL::to("suppsearch") }}",
          data:{
            search : value,
          },
          success:function(data){
            //alert(data);
            $('#modalbody').html(data);
          }
      });
    });

    $(document).on('click','.editdata',function(){ // Click to only happen on announce links
    
        var rfqnbr = $(this).data('rfqnbr');
        var startdate = $(this).data('startdate');
        var part = $(this).data('itemcode');
        var qty = $(this).data('qtyreq');
        var duedate = $(this).data('duedate');
        var remarks = $(this).data('remarks');
        var pricemin = $(this).data('pricemin');
        var pricemax = $(this).data('pricemax');
        var rfqsite = $(this).data('rfqsite');

        document.getElementById("u_rfqnumber").value = rfqnbr;
        document.getElementById("u_startdate").value = startdate;
        document.getElementById("u_itempart").value = part;
        document.getElementById("u_qtyreq").value = qty;

        document.getElementById("u_duedate").value = duedate;
        document.getElementById("u_remarks").value = remarks;
        document.getElementById("u_pricemin").value = pricemin;
        document.getElementById("u_pricemax").value = pricemax;
        document.getElementById("rfqsite").value = rfqsite;

        jQuery.ajax({
              type : "get",
              url : "{{URL::to("searcholdsupp") }}",
              data:{
                search : rfqnbr,
              },
              success:function(data){
                //alert(data);
                $('#oldsupplier').html(data);
              }
        });
    });

    $(document).on('click','.deletedata',function(){ // Click to only happen on announce links
    
        var cs_rfqnbr = $(this).data('rfqnbr');
        var startdate = $(this).data('startdate');
        var part = $(this).data('itemcode');
        var qty = $(this).data('qtyreq');
        var duedate = $(this).data('duedate');
        var remarks = $(this).data('remarks');
        var pricemin = $(this).data('pricemin');
        var pricemax = $(this).data('pricemax');
        var rfqsite = $(this).data('rfqsite');

        
        document.getElementById("d_rfqnbr").value = cs_rfqnbr;


        document.getElementById("d_startdate").value = startdate;
        document.getElementById("d_itempart").value = part;
        document.getElementById("d_qtyreq").value = qty;

        document.getElementById("d_duedate").value = duedate;
        document.getElementById("d_remarks").value = remarks;
        document.getElementById("d_pricemin").value = pricemin;
        document.getElementById("d_pricemax").value = pricemax;
        document.getElementById("rfqsite").value = rfqsite;

        jQuery.ajax({
              type : "get",
              url : "{{URL::to("searcholdsuppdel") }}",
              data:{
                search : cs_rfqnbr,
              },
              success:function(data){
                //alert(data);
                $('#d_oldsupplier').html(data);
              }
        });
    });

    $(document).on('click','.adddata',function(){ // Click to only happen on announce links
    
        var rfqnbr = $(this).data('rfqnbr');
        var itempart = $(this).data('itemcode');
        var itemdesc = $(this).data('itemdesc');
        
        //alert('123');
        document.getElementById('add_rfqnbr').value = rfqnbr;
        //document.getElementById('add_item').value = itempart.concat(' - ',itemdesc);
        document.getElementById('add_item').value = itempart + ' - ' + itemdesc;

        jQuery.ajax({
              type : "get",
              url : "{{URL::to("searchemail") }}",
              data:{
                search : rfqnbr,
              },
              success:function(data){
                //alert(data);
                $('#oldsupplier1').html(data);
              }
        });
    });

    $(document).on('click','.btnsearchtop10',function(){ // Click to only happen on announce links
        
        var data = document.getElementById('item10');
        var value = data[data.selectedIndex].value;

        jQuery.ajax({
              type : "get",
              url : "{{URL::to("polast10search") }}",
              data:{
                search : value
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
                search : value
              },
              success:function(data){
                //$('tbody').html(data);
                //console.log(data);
                $("#bodytop10rfq").empty().html(data);
              }
          });
        //document.getElementById('add_item').value = itempart.concat(' - ',itemdesc);
    });

    $(document).on('click','.viewdetail',function(){ // Click to only happen on announce links
    
        var rfqnbr = $(this).data('rfqnbr');
        
        document.getElementById('rfqid').value = rfqnbr;

        jQuery.ajax({
              type : "get",
              url : "{{URL::to("searchdetailrfq") }}",
              data:{
                search : rfqnbr,
              },
              success:function(data){
                //alert(data);
                $('#bodydetail').html(data);
              }
        });

    });



    $(document).ready(function () {
        $("#itempart").select2({
            width : '100%'
        });
        $("#item10").select2({
            width : '100%'
        });



        var counter = 0;

        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";


            cols += '<td>';
            cols += '<select id="suppname1[]" class="form-control selectpicker border border-dark" name="suppname[]" data-live-search="true" data-width="280px" required autofocus>';
            cols += '<option value= "">Select Data</option>';
            @foreach($users as $users)
            cols += '<option value="{{$users->xalert_supp}}"> {{$users->xalert_supp." - ".$users->xalert_nama}} </option>';
            @endforeach
            cols += '</select>';

            //cols += '<td data-title="suppname[]"><input type="text" class="form-control form-control-sm" name="suppname[]"/></td>';
            cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-md bt-action"  value="Delete"></td>';
            cols += '<td data-title="suppflg[]" style="display:none;"><input type="hidden" class="form-control form-control-sm" name="suppflg[]" value = "Yes"/></td>';
            cols += '</tr>'
            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;

            selectRefresh();
        });



        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            //counter -= 1
        });

        $("table.supp-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            //counter -= 1
        });

        $("#addrowsupp").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";


            cols += '<td>';
            cols += '<select id="suppname[]" class="form-control suppname selectpicker" name="suppname[]" data-live-search="true" required autofocus>';
            @foreach($supplier as $datasupp)
            cols += '<option value="{{$datasupp->xalert_supp}}"> {{$datasupp->xalert_supp." - ".$datasupp->xalert_nama}} </option>';
            @endforeach
            cols += '</select>';

            cols += '<td data-title="Action"><input type="button" class="ibtnDelsupp btn btn-md bt-action"  value="Delete"></td>';
            cols += '</tr>'
            newRow.append(cols);
            $("table.supp-list").append(newRow);
            counter++;

            selectRefresh();
        });



        $("table.supp-list").on("click", ".ibtnDelsupp", function (event) {
            $(this).closest("tr").remove();       
            //counter -= 1
        });
    });

    $(document).on('hide.bs.modal','#createModal,#editModal,#deleteModal,#addModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });


    document.querySelector("html").classList.add('js');

    var fileInput  = document.querySelector( ".input-file" ),  
        button     = document.querySelector( ".input-file-trigger" ),
        the_return = document.querySelector(".file-return");
          
    button.addEventListener( "keydown", function( event ) {  
        if ( event.keyCode == 13 || event.keyCode == 32 ) {  
            fileInput.focus();  
        }  
    });
    button.addEventListener( "click", function( event ) {
       fileInput.focus();
       return false;
    });  
    fileInput.addEventListener( "change", function( event ) {  
        the_return.innerHTML = this.value;  
    });  
</script>

@endsection
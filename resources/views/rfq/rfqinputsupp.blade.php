
@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFQ Feedback</li>
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

<div class="form-group row" style="color:red;margin-bottom:0px !important;margin-left:0px;">
    <label for="ponbr" class="col-form-label">{{ __('Total RFQ :') }}</label>
        <div class="col-md-1">
            <label class="col-form-label text-md-left">
            <?php echo $totalrfq; ?>
            </label>
        </div> 
    <label for="ponbr" class="col-form-label">{{ __('RFQ Without Response :') }}</label>
        <div class="col-md-1">
            <label class="col-form-label text-md-left">
            <?php echo $totnoresp; ?>
            </label>
        </div>
</div>

<!--Search By RFQ Number-->
<div class="form-group row datarow" >
        <label for="rfqnumber" class="col-md-2 col-lg-1 col-form-label">{{ __('RFQ') }}</label>
        <div class="col-md-4 col-lg-3">
            <input id="rfqnumber" type="text" class="form-control" name="rfqnumber" 
            value="" autofocus>
        </div>
        <label for="itemreq" class="col-md-2 col-lg-1 col-form-label">{{ __('Item') }}</label>
        <div class="col-md-4 col-lg-3">
            <input id="itemreq" type="text" class="form-control" name="itemreq" 
            value="" autofocus>
        </div>
        <label for="status" class="col-md-2 col-lg-1 col-form-label seconddata">{{ __('Status') }}</label>
        <div class="col-md-4 col-lg-3">
            <select  id="status" class="form-control status seconddata" name="status" autofocus>
                <option value="">--Choose--</option>
                <option value="0">New Request</option>
                <option value="1">Waiting Reply</option>
                <option value="2">Accepted</option>
            </select>
        </div>
</div>
<div class="form-group row datarow">
        <label for="datefrom" class="col-md-2 col-lg-1 col-form-label seconddata">{{ __('From') }}</label>
        <div class="col-md-4 col-lg-3 seconddata">
        <!--
            <input id="datefrom" type="date" class="form-control" name="datefrom" 
            value="" autofocus>
        -->
            <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                    required autofocus autocomplete="off">
        </div>
        <label for="dateto" class="col-md-2 col-lg-1 col-form-label seconddata">{{ __('To') }}</label>
        <div class="col-md-4 col-lg-3 seconddata">
        <!--  
            <input id="dateto" type="date" class="form-control" name="dateto" 
            value="" autofocus>
        -->
            <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                    required autofocus autocomplete="off">
        </div>
        <div class="offset-md-2 offset-lg-1 seconddata">
        <input type="button" class="btn bt-ref" 
        id="btnsearch" value="Search" style="margin-left:15px;" />
        </div>
        <div class="offset-md-0 offset-lg-0">
        <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
        </div>
</div>

<!--Table-->
@include('rfq.loadsupp')

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">RFQ Feedback</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/purchbid" enctype="multipart/form-data" method="post" id="update">
            {{ csrf_field() }}

            <input type="hidden" name="edit_id" id="edit_id">
            <input type="hidden" name="rfqsite" id="rfqsite">
            <input type="hidden" name="supplier" id="supplier">
            <input type="hidden" name="startdate" id="startdate">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="rfqnbr" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="rfqnbr" type="text" class="form-control" name="rfqnbr" value="" readonly autofocus>
                        @if ($errors->has('rfqnbr'))
                            <span class="help-block">
                                <strong>{{ $errors->first('rfqnbr') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemcode" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Item') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="itemcode" type="text" class="form-control" name="itemcode" value="" readonly autofocus>
                        @if ($errors->has('itemcode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itemcode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="pricemin" type="text" class="form-control" name="pricemin" value="" readonly autofocus>
                        @if ($errors->has('pricemin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemin') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="pricemax" type="text" class="form-control" name="pricemax" value="" readonly autofocus>
                        @if ($errors->has('pricemax'))
                            <span class="help-block">
                                <strong>{{ $errors->first('pricemax') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="qtyreq" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Qty Req.') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="qtyreq" type="text" class="form-control" name="qtyreq" value="" readonly autofocus>
                        @if ($errors->has('qtyreq'))
                            <span class="help-block">
                                <strong>{{ $errors->first('qtyreq') }}</strong>
                            </span>
                        @endif
                    </div>
                  
                    <label for="duedate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="duedate" type="date" class="form-control" name="duedate" value="" readonly autofocus autocomplete="off">
                        @if ($errors->has('duedate'))
                            <span class="help-block">
                                <strong>{{ $errors->first('duedate') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="qtypro" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Propose Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="qtypro" type="text" class="form-control" name="qtypro" value="" autocomplete="off" required autofocus>
                        @if ($errors->has('qtypro'))
                            <span class="help-block">
                                <strong>{{ $errors->first('qtypro') }}</strong>
                            </span>
                        @endif
                    </div>
                    <label for="prodate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Propose Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <!--<input id="prodate" type="date" class="form-control" name="prodate" value="" required autofocus>-->
                        <input type="text" id="prodate" class="form-control" name='prodate' autocomplete="off" placeholder="DD/MM/YYYY"
                            required autofocus autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="proprice" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="proprice" type="text" class="form-control" name="proprice" value="" autocomplete="off" required autofocus>
                        @if ($errors->has('proprice'))
                            <span class="help-block">
                                <strong>{{ $errors->first('proprice') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="remarks" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Remarks') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="remarks" class="form-control" name="remarks" maxlength="40" autofocus autocomplete="off"></input>
                    </div>
                </div>
                <div class="form-group row">
	            	<label for="file" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Upload') }}</label>
	            	<div class="col-md-7 col-lg-8 input-file-container">  
			            <input type="hidden" id="filename" name="filename" value="">
					    <input class="input-file" id="file" type="file" name="file">
					    <label tabindex="0" for="file" class="btn btn-info input-file-trigger">Select a file</label>
					</div>
	            </div>
	            <div class="form-group" style="margin-top:0px;padding-top:0px">
	            	<div class="offset-lg-3 offset-md-4">
					 	   <p class="file-return offset-md-4 offset-lg-4"></p>
	            	</div>
	            </div>

                <div class="form-group row md-form">
                    <div class="col-md-8 offset-md-3" style="text-align: center; margin-top:20px;">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" id="cbsubmitapp" required>
                          <label class="custom-control-label" for="cbsubmitapp">Confirm to submit</label>
                        </div>
                    </div>
                </div>
            
            </div>

            <div class="modal-footer">
              <button type="button" class="btn bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn bt-action" id="e_btnconf">Save</button>
              <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

<div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">View Feedback</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form>
            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-body">
                <div class="form-group row">
                    <label for="read_rfqnbr" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('RFQ Number') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="read_rfqnbr" type="text" class="form-control" name="read_rfqnbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="read_itemcode" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Item') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="read_itemcode" type="text" class="form-control" name="read_itemcode" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="read_qtyreq" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Qty Requested') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_qtyreq" type="text" class="form-control" name="read_qtyreq" value="" readonly autofocus>
                    </div>

                    <label for="read_duedate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Due Date') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_duedate" type="text" class="form-control" name="read_duedate" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="read_pricemin" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price Min') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_pricemin" type="text" class="form-control" name="read_pricemin" value="" readonly autofocus>
                    </div>
                    <label for="read_pricemax" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Price Max') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_pricemax" type="text" class="form-control" name="read_pricemax" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="read_qtypro" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Propose Qty') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_qtypro" type="text" class="form-control" name="read_qtypro" value="" readonly autofocus>
                    </div>
                    <label for="read_prodate" class="col-md-4 col-lg-2 col-form-label text-md-right">{{ __('Propose Date') }}</label>
                    <div class="col-md-5 col-lg-3">
                        <input id="read_prodate" type="text" class="form-control" name="read_prodate" value="" readonly autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="read_proprice" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Price') }}</label>
                    <div class="col-md-7 col-lg-3">
                        <input id="read_proprice" type="text" class="form-control" name="read_proprice" value="" readonly autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="read_remarks" class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Remarks') }}</label>
                    <div class="col-md-7 col-lg-8">
                        <input id="read_remarks" class="form-control" name="remarks" maxlength="40" readonly autofocus></input>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" data-dismiss="modal">Close</button>
            </div>
       </form>

    </div>
  </div>
</div>


<div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $( function() {
            $( "#prodate" ).datepicker({
                dateFormat : 'dd/mm/yy'
            });
            $( "#datefrom" ).datepicker({
                dateFormat : 'dd/mm/yy'
            });
            $( "#dateto" ).datepicker({
                dateFormat : 'dd/mm/yy'
            });
        });
        $(document).on('click','.pagination a', function(e){
        e.preventDefault();

        //alert('123');
        var page = $(this).attr('href').split('?page=')[1];

        //console.log(page);
        getData(page);

        });

        function getData(page){

            $.ajax({
                url: '/pagination/viewlistsupp?page='+ page,
                type: "get",
                datatype: "html" 
            }).done(function(data){
                console.log('Page = '+ page);

                $(".tag-container").empty().html(data);
                //location.hash = page;
                //console.log(data);
            }).fail(function(jqXHR, ajaxOptions, thrownError){
                Swal.fire({
                    icon: 'error',
                    text: 'No Response From Server',
                })
            });
        }

        $(document).on('hide.bs.modal','#editModal',function(){
            if(confirm("Are you sure, you want to close?")) return true;
            else return false;
        });

        $('#btnsearch').on('click',function(){
        var value = document.getElementById("rfqnumber").value;
        var code = document.getElementById("itemreq").value;
        var datefrom = document.getElementById("datefrom").value;
        var dateto = document.getElementById("dateto").value;
        
        
        var status = document.getElementById("status");
        var value_status = status.options[status.selectedIndex].value;


        jQuery.ajax({
            type : "get",
            url : "{{URL::to("searchsupp") }}",
            data:{
                rfq : value,
                code : code,
                datefrom : datefrom,
                dateto : dateto,
                status : value_status,
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
        var datefrom = '';
        var dateto = '';
        var value_status = '';


        jQuery.ajax({
            type : "get",
            url : "{{URL::to("searchsupp") }}",
            data:{
                rfq : value,
                code : code,
                datefrom : datefrom,
                dateto : dateto,
                status : value_status,
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

        $('#update').submit(function(event) {


            var prodate = document.getElementById("prodate").value.split('/');
            var new_prodate = new Date(prodate[2].concat('-',prodate[1],'-',prodate[0]));
            var today = new Date();

            var qtypro = document.getElementById("qtypro").value;
            var pricepro = document.getElementById("proprice").value;   

            var reg = /^\d+$/;
            var regqty = /^(\s*|\d+\.\d*|\d+)$/;



            if(!regqty.test(qtypro)){
                Swal.fire({
                    icon: 'error',
                    text: 'Qty Propose must be number',
                })
                return false;  
            }else if(!reg.test(pricepro)){
                Swal.fire({
                    icon: 'error',
                    text: 'Price must be number',
                })
                return false;
            }else if(pricepro <= 0){
                Swal.fire({
                    icon: 'error',
                    text: 'Price must be greater than 0',
                })
                return false;
            }else if(qtypro <= 0){
                Swal.fire({
                    icon: 'error',
                    text: 'Qty Propose must be greater than 0r',
                })
                return false;
            }else if(new_prodate < today){
                Swal.fire({
                    icon: 'error',
                    text: 'Propose cannot be earlier than today',
                })
                return false;
            }else{
                document.getElementById('e_btnclose').style.display = 'none';
                document.getElementById('e_btnconf').style.display = 'none';
                document.getElementById('e_btnloading').style.display = ''; 
                $(this).unbind('submit').submit();
            }
        });

        $(document).on('click','.editUser',function(e){ // Click to only happen on announce links
            e.preventDefault();
            //alert('tst');
            var uid = $(this).data('id');
            var part = $(this).data('part');
            var nbr = $(this).data('nbr');
            var qty = $(this).data('qty');
            var date = $(this).data('date');
            var pricemin = $(this).data('pricemin');
            var pricemax = $(this).data('pricemax');
            var supplier = $(this).data('supplier');
            var site = $(this).data('site');
            var startdate = $(this).data('startdate');
            var partdesc = $(this).data('partdesc');
            var proqty = $(this).data('proqty');
            var proprice = $(this).data('proprice');
            var prodate = $(this).data('prodate');
            var proremarks = $(this).data('proremarks');

            //console.log(part.concat(' - ',partdesc));
            //alert(part + ' - ' + partdesc);

            //return false;
            document.getElementById("edit_id").value = uid;
            document.getElementById("itemcode").value = part + ' - ' + partdesc;
            document.getElementById("rfqnbr").value = nbr;
            document.getElementById("qtyreq").value = qty;
            document.getElementById("duedate").value = date;
            document.getElementById("pricemin").value = pricemin;
            document.getElementById("pricemax").value = pricemax;
            document.getElementById("supplier").value = supplier;
            document.getElementById("rfqsite").value = site;
            document.getElementById("startdate").value = startdate;
            document.getElementById("qtypro").value = proqty;
            document.getElementById("prodate").value = prodate;
            document.getElementById("proprice").value = proprice;
            document.getElementById("remarks").value = proremarks;

        });

        $(document).on('click','.checkuser',function(){ // Click to only happen on announce links
        
            //alert('tst');
            var uid = $(this).data('id');
            var part = $(this).data('part');
            var nbr = $(this).data('nbr');
            var qty = $(this).data('qty');
            var date = $(this).data('date');
            var pricemin = $(this).data('pricemin');
            var pricemax = $(this).data('pricemax');
            var proqty = $(this).data('proqty');
            var proprice = $(this).data('proprice');
            var prodate = $(this).data('prodate');
            var proremarks = $(this).data('proremarks');
            var partdesc =$(this).data('partdesc');
            var partdesc = $(this).data('partdesc');

            document.getElementById("edit_id").value = uid;
            //document.getElementById("read_itemcode").value = part.concat(' - ',partdesc);
            document.getElementById("read_itemcode").value = part + ' - ' + partdesc;
            document.getElementById("read_rfqnbr").value = nbr;
            document.getElementById("read_qtyreq").value = qty;
            document.getElementById("read_duedate").value = date;
            document.getElementById("read_pricemin").value = pricemin;
            document.getElementById("read_pricemax").value = pricemax;
            document.getElementById("read_proprice").value = proprice;
            document.getElementById("read_prodate").value = prodate;
            document.getElementById("read_qtypro").value = proqty;
            document.getElementById("read_remarks").value = proremarks;

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
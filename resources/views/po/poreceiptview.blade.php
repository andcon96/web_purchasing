@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order Receipt</li>
</ol>
@endsection


@section('content')

@if(session('error'))
	<div class="alert alert-danger" id="getError">
		{{ session()->get('error') }}
	</div>
@endif

@if(session()->has('updated'))
    <div class="alert alert-success">
      {!! Session::get('updated') !!}
    </div>
@endif
<BR>

@include('po.tableporeceipt')

<form method="post" action="{{url('receiptqad')}}" id='submit'>
  {{csrf_field()}}

   <div class="form-group row md-form">
        <div class="col-md-12" style="text-align: center;">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="cbsubmit" required>
              <label class="custom-control-label" for="cbsubmit">Confirm to submit</label>
            </div>
        </div>
    </div>

  <input type="submit" name="submit" id='s_btnconf' value='Submit' class="btn bt-action float-right" style="margin-top:10px">
  <button type="button" class="btn btn-info float-right" id="s_btnloading" style="display:none;margin-top: 10px;">
    <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
  </button>
  
</form> 

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Purchase Order Receipt</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/updaterow" method="post" id="update">
            {{ csrf_field() }}

            <div class="modal-body">
                <input type="hidden" name="rcpid" id="rcpid">
                <!--
                <div class="form-group row">
                    <label for="m_sj" class="col-md-2 col-form-label text-md-right">{{ __('Surat Jalan') }}</label>
                    <div class="col-md-4">
                        <input id="m_sj" type="text" class="form-control" name="m_sj" value="" readonly autofocus>
                    </div>
                </div>
                -->
                <div class="form-group row">
                    <label for="m_ponbr" class="col-md-2 col-form-label text-md-right">{{ __('PO No.') }}</label>
                    <div class="col-md-4">
                        <input id="m_ponbr" type="text" class="form-control" name="m_ponbr" value="" readonly autofocus>
                    </div>

                    <label for="m_line" class="col-md-2 col-form-label text-md-right">{{ __('Line') }}</label>
                    <div class="col-md-4">
                        <input id="m_line" type="text" class="form-control" name="m_line" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_itemcode" class="col-md-2 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-4">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>

                    <label for="m_itemdesc" class="col-md-2 col-form-label text-md-right">{{ __('Item Description') }}</label>
                    <div class="col-md-4">
                        <input id="m_itemdesc" type="text" class="form-control" name="m_itemdesc" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyord" class="col-md-2 col-form-label text-md-right">{{ __('Qty Order') }}</label>
                    <div class="col-md-4">
                        <input id="m_qtyord" type="text" class="form-control" name="m_qtyord" value="" readonly autofocus>
                    </div>

                    <label for="m_qtyship" class="col-md-2 col-form-label text-md-right">{{ __('Qty Ship') }}</label>
                    <div class="col-md-4">
                        <input id="m_qtyship" type="text" class="form-control" name="m_qtyship" value="" readonly autofocus>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label for="m_qtyopen" class="col-md-2 col-form-label text-md-right">{{ __('Qty Open') }}</label>
                    <div class="col-md-4">
                        <input id="m_qtyopen" type="text" class="form-control" name="m_qtyopen" value="" readonly autofocus>
                    </div>
                </div>
                -->
                <div class="form-group row">
                      <label for="effdate" class="col-md-2 col-form-label text-md-right">{{ __('Effective Date') }}</label>
                      <div class="col-md-4">
                          <input id="effdate" type="text" class="form-control" name="effdate" 
                          value="" placeholder="DD/MM/YYYY" autocomplete="off" required autofocus>
                      </div>

                      <label for="shipdate" class="col-md-2 col-form-label text-md-right">{{ __('Ship Date') }}</label>
                      <div class="col-md-4">
                          <input id="shipdate" type="text" class="form-control" name="shipdate" 
                          value="" placeholder="DD/MM/YYYY" autocomplete="off" required autofocus>
                      </div>
                </div>
                
                <div class="form-group row">
                    <!--
                    <label for="m_site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
                    <div class="col-md-4">
                        <select class="form-control" name="m_site" id="m_site" required>
                              <option value = ""> Select Data </option>
                          @foreach($site as $site)
                              <option value = "{{$site->xsite_site}}">{{$site->xsite_site}}</option>
                          @endforeach
                        </select>
                        <input id="m_site" type="text" class="form-control" name="m_site" value="" autocomplete="off" readonly autofocus>
                    </div>
                    -->
                    <label for="m_loc" class="col-md-2 col-form-label text-md-right">{{ __('Loc') }}</label>
                    <div class="col-md-4">
                        <input id="m_loc" type="text" class="form-control" name="m_loc" value="" autocomplete="off" required autofocus>
                    </div>
                    <label for="m_lot" class="col-md-2 col-form-label text-md-right">{{ __('Lot/Serial') }}</label>
                    <div class="col-md-4">
                        <input id="m_lot" type="text" class="form-control" name="m_lot" value="" maxlength="8" autocomplete="off" autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    
                    <label for="m_ref" class="col-md-2 col-form-label text-md-right">{{ __('References') }}</label>
                    <div class="col-md-4">
                        <input id="m_ref" type="text" class="form-control" name="m_ref" value="" maxlength="40" autocomplete="off" autofocus></input>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyrec" class="col-md-2 col-form-label text-md-right">{{ __('Qty Receipt') }}</label>
                    <div class="col-md-2 d-flex">
                        <input id="m_qtyrec" type="text" class="form-control" name="m_qtyrec" value="" autocomplete="off" required autofocus>
                        <p id="d_um" style="margin-left: 15px;margin-top: 7px;"></p>
                    </div>
                        <input type="hidden" name="m_um" id="m_um">
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

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">PO Receipt</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/deletenewreceipt" method="post" id='delete'>
            {{ csrf_field() }}

            <div class="modal-body">
               Delete Line ?
               <input type="hidden" name="t_deleteid" id="t_deleteid">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success bt-action" id="d_btnconf">Save</button>
              <button type="button" class="btn btn-info bt-action" id="d_btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>


@endsection


@section('scripts')

<script>
    $( function() {
        $( "#effdate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
        $( "#shipdate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
    
  $(document).on('hide.bs.modal','#detailModal,#deleteModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });

  $('#update').submit(function(event) {
        var regqty = /^(\s*|\d+\.\d*|\d+)$/;
        var qtyreq = document.getElementById("m_qtyrec").value;

        if(!regqty.test(qtyreq)){
            alert('Qty Requested Must be number or "." ');
            return false;
        }else{
          document.getElementById('btnclose').style.display = 'none';
          document.getElementById('btnconf').style.display = 'none';
          document.getElementById('btnloading').style.display = '';
        }

    });


  $('#delete').submit(function(event){
        document.getElementById('d_btnclose').style.display = 'none';
        document.getElementById('d_btnconf').style.display = 'none';
        document.getElementById('d_btnloading').style.display = ''; 
  });


  $('#submit').submit(function(event){
        document.getElementById('s_btnconf').style.display = 'none';
        document.getElementById('s_btnloading').style.display = ''; 
  });

  $(document).on('click','.editUser',function(){ // Click to only happen on announce links
   
   var id = $(this).data('id');
   var suratjalan = $(this).data('sj');
   var ponbr = $(this).data('nbr');
   var line = $(this).data('line');
   var part = $(this).data('part');
   var desc = $(this).data('desc');
   var qtyord = $(this).data('qtyord');
   var qtyship = $(this).data('qtyship');
   var qtyopen = $(this).data('qtyopen');
   var effdate = $(this).data('effdate');
   var shipdate = $(this).data('shipdate');
   var qtyrcvd = $(this).data('qtyrcvd');
   //var um = $(this).data('um');
   var site = $(this).data('site');
   //var loc = $(this).data('loc');
   //var lot = $(this).data('lot');
   var ref = $(this).data('ref');
   

    if(effdate == ''){
      var new_shipdate = '';
      var new_effdate = '';
    }else{
      var split_effdate = effdate.split('-');
      var split_shipdate = shipdate.split('-');

      var new_effdate = split_effdate[2].concat('/',split_effdate[1],'/',split_effdate[0]);
      var new_shipdate = split_shipdate[2].concat('/',split_shipdate[1],'/',split_shipdate[0]);
    }   



    document.getElementById('rcpid').value = id;
    //document.getElementById("m_sj").value = suratjalan;
    document.getElementById("m_ponbr").value = ponbr;
    document.getElementById("m_line").value = line;
    document.getElementById("m_itemcode").value = part;
    document.getElementById("m_itemdesc").value = desc;
    document.getElementById("m_qtyord").value = qtyord;
    //document.getElementById("m_qtyopen").value = qtyopen;
    document.getElementById("m_qtyship").value = qtyship;

    document.getElementById("effdate").value = new_effdate;
    document.getElementById("shipdate").value = new_shipdate;
    document.getElementById("m_qtyrec").value = qtyrcvd;
    //document.getElementById("m_site").value = site;
    document.getElementById("m_ref").value = ref;

   jQuery.ajax({
          type : "get",
          url : "{{URL::to("detailreceipt") }}",
          data:{
            suratjalan : suratjalan,
            ponbr : ponbr,
            line : line
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            document.getElementById("d_um").innerHTML = data[0]['xpod_um'];
            document.getElementById("m_um").value = data[0]['xpod_um'];
            document.getElementById("m_loc").value = data[0]['xpod_loc'];
            document.getElementById("m_lot").value = data[0]['xpod_lot'];
          }
      });   
  });


  $(document).on('click','.deleteUser',function(){ // Click to only happen on announce links
   
   var id = $(this).data('sj');

   document.getElementById("t_deleteid").value = id;

  });

  $(document).on('change','#m_qtyrec',function(){
     var qtyship = document.getElementById("m_qtyship").value;
     var qtyrec = document.getElementById("m_qtyrec").value
     
      if(parseInt(qtyrec) > parseInt(qtyship)){
          setTimeout(function(){
              alert("Qty Received is greater than Qty Ship");
              document.getElementById("m_qtyrec").focus();
              return false;
          },1);
      }

  });
</script>

@endsection
@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">Purchase Order Receipt</li>
</ol>
@endsection


@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
        {{ session()->get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session()->has('updated'))
    <div class="alert alert-success">
        {{ session()->get('updated') }}
    </div>
@endif


<form action="/receiptsearch" method="post">
    {{csrf_field()}}
    <div class="form-group row">
            <label for="sjnbr" class="col-form-label text-md-right" style="margin-left:25px">{{ __('Shipper Number') }}</label>
            <div class="col-xl-2 col-lg-2 col-md-8 col-sm-12 col-xs-12">
                <select class="form-control" id="sjnbr" name="sjnbr" required>
                    <option value="">Select Data</option>
                @foreach($sjopen as $sjopen)
                    <option value="{{$sjopen->xsj_id}}">{{$sjopen->xsj_id}}</option>
                @endforeach
                </select>
            </div>
            <label for="receiptdate" class="col-form-label text-md-right" style="margin-left:25px">{{ __('Receipt Date') }}</label>
            <div class="col-xl-2 col-lg-2 col-md-8 col-sm-12 col-xs-12">
                <input id="receiptdate" type="text" class="form-control" name="receiptdate" 
                value="{{ Carbon\Carbon::parse($date)->format('d-m-Y')  }}" readonly>
            </div>

            <div class="offset-md-3 offset-lg-0 offset-xl-0 offset-sm-0 offset-xs-0" id='btn'>
            <input type="submit" class="btn bt-ref" 
            id="btnsearch" value="Search" style="margin-left:15px;" />
            </div>
    </div>            
</form>
        
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">PO Receipt</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form action="/receiptupdate" method="post" id="update">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="form-group row">
                    <label for="m_sj" class="col-md-4 col-form-label text-md-right">{{ __('Surat Jalan') }}</label>
                    <div class="col-md-5">
                        <input id="m_sj" type="text" class="form-control" name="m_sj" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_ponbr" class="col-md-4 col-form-label text-md-right">{{ __('PO No.') }}</label>
                    <div class="col-md-5">
                        <input id="m_ponbr" type="text" class="form-control" name="m_ponbr" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_line" class="col-md-4 col-form-label text-md-right">{{ __('Line') }}</label>
                    <div class="col-md-5">
                        <input id="m_line" type="text" class="form-control" name="m_line" value="" readonly autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_itemcode" class="col-md-4 col-form-label text-md-right">{{ __('Item Code') }}</label>
                    <div class="col-md-5">
                        <input id="m_itemcode" type="text" class="form-control" name="m_itemcode" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_itemdesc" class="col-md-4 col-form-label text-md-right">{{ __('Item Description') }}</label>
                    <div class="col-md-5">
                        <input id="m_itemdesc" type="text" class="form-control" name="m_itemdesc" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyord" class="col-md-4 col-form-label text-md-right">{{ __('Qty Order') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyord" type="text" class="form-control" name="m_qtyord" value="" readonly autofocus>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyship" class="col-md-4 col-form-label text-md-right">{{ __('Qty Ship') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyship" type="text" class="form-control" name="m_qtyship" value="" readonly autofocus>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label for="m_qtyopen" class="col-md-4 col-form-label text-md-right">{{ __('Qty Open') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyopen" type="text" class="form-control" name="m_qtyopen" value="" readonly autofocus>
                    </div>
                </div>
                -->
                <div class="form-group row">
                      <label for="effdate" class="col-md-4 col-form-label text-md-right">{{ __('Effective Date') }}</label>
                      <div class="col-md-5">
                          <input id="effdate" type="text" class="form-control" name="effdate" 
                          value="" placeholder="DD/MM/YYYY" required autofocus>
                      </div>
                </div>
                <div class="form-group row">
                      <label for="shipdate" class="col-md-4 col-form-label text-md-right">{{ __('Ship Date') }}</label>
                      <div class="col-md-5">
                          <input id="shipdate" type="text" class="form-control" name="shipdate" 
                          value="" placeholder="DD/MM/YYYY" required autofocus>
                      </div>
                </div>
                
                <div class="form-group row">
                    <label for="m_qtyrec" class="col-md-4 col-form-label text-md-right">{{ __('Qty Receipt') }}</label>
                    <div class="col-md-5">
                        <input id="m_qtyrec" type="text" class="form-control" name="m_qtyrec" value="" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_um" class="col-md-4 col-form-label text-md-right">{{ __('UM') }}</label>
                    <div class="col-md-5">
                        <input id="m_um" type="text" class="form-control" name="m_um" value="" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_site" class="col-md-4 col-form-label text-md-right">{{ __('Site') }}</label>
                    <div class="col-md-5">
                        <input id="m_site" type="text" class="form-control" name="m_site" value="" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_loc" class="col-md-4 col-form-label text-md-right">{{ __('Loc') }}</label>
                    <div class="col-md-5">
                        <input id="m_loc" type="text" class="form-control" name="m_loc" value="" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_lot" class="col-md-4 col-form-label text-md-right">{{ __('Lot/Serial') }}</label>
                    <div class="col-md-5">
                        <input id="m_lot" type="text" class="form-control" name="m_lot" value="" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="m_ref" class="col-md-4 col-form-label text-md-right">{{ __('References') }}</label>
                    <div class="col-md-5">
                        <textarea id="m_ref" type="text" class="form-control" rows='5' name="m_ref" value="" autofocus></textarea>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success bt-action" id="btnconf">Confirm</button>
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

<script>

    $("#sjnbr").select2({
        width : '100%'
    });
    $("#sjnbr").select2('open');

    $( "#effdate" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });

    $( "#shipdate" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });

    $(document).on('hide.bs.modal','#detailModal',function(){
        if(confirm("Are you sure, you want to close?")) return true;
        else return false;
    });

    $('#update').submit(function(event) {
        document.getElementById('btnclose').style.display = 'none';
        document.getElementById('btnconf').style.display = 'none';
        document.getElementById('btnloading').style.display = ''; 
    });


    $(document).on('click','.editUser',function(){ // Click to only happen on announce links

        //alert('123');
        var suratjalan = $(this).data('sj');
        var ponbr = $(this).data('nbr');
        var line = $(this).data('line');
        var part = $(this).data('part');
        var desc = $(this).data('desc');
        var qtyord = $(this).data('qtyord');
        var qtyship = $(this).data('qtyship');
        //var qtyopen = $(this).data('qtyopen');

        document.getElementById("m_sj").value = suratjalan;
        document.getElementById("m_ponbr").value = ponbr;
        document.getElementById("m_line").value = line;
        document.getElementById("m_itemcode").value = part;
        document.getElementById("m_itemdesc").value = desc;
        document.getElementById("m_qtyord").value = qtyord;
        //document.getElementById("m_qtyopen").value = qtyopen;
        document.getElementById("m_qtyship").value = qtyship;

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
            document.getElementById("m_um").value = data[0]['xpod_um'];
            document.getElementById("m_loc").value = data[0]['xpod_loc'];
            document.getElementById("m_lot").value = data[0]['xpod_lot'];
            }
        });


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
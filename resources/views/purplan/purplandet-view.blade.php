@extends('layout.layout')

@section('menu_name','Purchase Order Create')

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
    <div class="alert alert-success  alert-dismissible fade show"  role="alert">
        {{ session()->get('updated') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!--Table-->
<div class="col-md-12">
	@include('purplan.tabledet-view')
</div>

	
	<div class="form-group row mt-3">
	      <label for="idrow" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('RFP/RFQ No.') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="idrow" type="text" class="form-control" name="idrow" 
	          value="" readonly autofocus>
	      </div>
	      <label for="supplier" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Supplier') }}</label>
	      <div class="col-md-4 col-lg-4">
	          <input id="supplier" type="text" class="form-control" name="supplier" 
	          value="" readonly autofocus>
	      </div>
	</div>

	<div class="form-group row mt-3">
	      <label for="line" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Line.') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="line" type="text" class="form-control" name="line" 
	          value="" readonly autofocus>
	      </div>
	      <label for="item" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Item') }}</label>
	      <div class="col-md-4 col-lg-4">
	          <input id="item" type="text" class="form-control" name="item" 
	          value="" readonly autofocus>
	      </div>
	</div>

	<div class="form-group row mt-3">
	      <label for="qtyreq" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Qty Req.') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="qtyreq" type="text" class="form-control" name="qtyreq" 
	          value="" readonly autofocus>
	      </div>
	      <label for="qtypro" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Qty Pro.') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="qtypro" type="text" class="form-control" name="qtypro" 
	          value="" readonly autofocus>
	      </div>
	</div>

	<div class="form-group row mt-3">
	      <label for="duedate" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Due Date') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="duedate" type="text" class="form-control" name="duedate" 
	          value="" readonly autofocus>
	      </div>
	      <label for="prodate" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Pro Date') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="prodate" type="text" class="form-control" name="prodate" 
	          value="" readonly autofocus>
	      </div>
	</div>

	<div class="form-group row mt-3">
	      <label for="qtypur" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Qty Purch.') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="qtypur" type="number" class="form-control" name="qtypur" 
	          value="" autocomplete="off" readonly autofocus>
	      </div>
	      <label for="price" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Price') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="price" type="number" min="0" class="form-control" name="price" 
	          value="" autocomplete="off" readonly autofocus>
	      </div>
	</div>

	<div class="form-group row mt-3">
	      <label for="purdate" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __('Pur Date') }}</label>
	      <div class="col-md-4 col-lg-2">
	          <input id="purdate" type="text" class="form-control" name="purdate" 
	          value="" autocomplete="off" readonly autofocus>
        </div>
        <label for="btnupdate" class="col-md-2 col-lg-2 text-md-right col-form-label">{{ __(' ') }}</label>
	      <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
	        <input type="submit" class="btn bt-action seconddata" 
	        id="btnupdate" value="Update" />
	      </div>
	</div>

 
  <form method="post" action="/cimloadpplan">
    {{ csrf_field() }}

    <div class="form-group row mt-3 float-right">
          <div class="col-md-12 custom-control custom-checkbox">
              <hr>
              <input type="hidden" id='iddb' name="iddb">
              <input type="checkbox" class="custom-control-input" id="cbsubmit" required>
              <label class="custom-control-label align-middle mr-2" for="cbsubmit">Confirm to Generate PO</label>
              <input type="submit" id="btnSubmit" name="btnSubmit" class="btn bt-action">
          </div>
    </div>
  </form>

  
  <div id="loader" class="lds-dual-ring hidden overlay"></div>
  
@endsection


@section('scripts')

<script>
  $( function() {
      $( "#datefrom" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
      $( "#dateto" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
      $( "#purdate" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
  });

  $(document).on('click','.editdata',function(e){

      let rfno = $(this).data('rfnumber');
      let suppcode = $(this).data('suppcode');
      let suppdesc = $(this).data('suppdesc');
      let line = $(this).data('line');
      let itemcode = $(this).data('itemcode');
      let itemdesc = $(this).data('itemdesc');
      let qtyreq = $(this).data('qtyreq');
      let qtypro = $(this).data('qtypro');
      let qtypur = $(this).data('qtypur');
      let price = $(this).data('price');
      let duedate = $(this).data('duedate');
      let prodate = $(this).data('prodate');
      let purdate = $(this).data('purdate');
      let idrow = $(this).data('id')

      document.getElementById('iddb').value = idrow;
      document.getElementById('idrow').value = rfno;
      document.getElementById('supplier').value = suppcode + ' - ' + suppdesc;
      document.getElementById('line').value = line;
      document.getElementById('item').value = itemcode + ' - ' + itemdesc;
      document.getElementById('qtyreq').value = qtyreq;
      document.getElementById('qtypro').value = qtypro;
      document.getElementById('qtypur').value = qtypur;
      document.getElementById('price').value = price;
      document.getElementById('duedate').value = duedate;
      document.getElementById('prodate').value = prodate;
      document.getElementById('purdate').value = purdate;

      document.getElementById('purdate').readOnly = false;
      document.getElementById('qtypur').readOnly = false;
      document.getElementById('price').readOnly = false;

      e.preventDefault();
  });

  $(document).on('click','#deletetmp',function(e){

    let id = $(this).data('id');
    let rfnumber = $(this).data('rfnumber');
    let row = $(this).data('iteration');

    jQuery.ajax({
          type : "POST",
          url : "{{URL::to("deletetemp") }}",
          data:{
          _token : "{{ csrf_token() }}",
            id : id,
            rfnumber : rfnumber
          },
          beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
              $('#loader').removeClass('hidden')
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $("#dataTable").empty().html(data);
          },
          complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
              $('#loader').addClass('hidden')
          },
      });
      e.preventDefault();
  });

  $(document).on('click','#btnupdate',function(e){
    let rfnumber = document.getElementById('idrow').value;
    let price = document.getElementById('price').value;
    let purdate = document.getElementById('purdate').value;
    let qtypur = document.getElementById('qtypur').value;
    let id = document.getElementById('iddb').value;

    jQuery.ajax({
          type : "post",
          url : "{{URL::to("edittemp") }}",
          data:{
          _token : "{{ csrf_token() }}",
            id : id,
            rfnumber : rfnumber,
            price : price,
            purdate : purdate,
            qtypur : qtypur,
          },
          beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
              $('#loader').removeClass('hidden')
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $("#dataTable").empty().html(data);
            document.getElementById('iddb').value = '';
            document.getElementById('idrow').value = '';
            document.getElementById('supplier').value = '';
            document.getElementById('line').value = '';
            document.getElementById('item').value = '';
            document.getElementById('qtyreq').value = '';
            document.getElementById('qtypro').value = '';
            document.getElementById('qtypur').value = '';
            document.getElementById('price').value = '';
            document.getElementById('duedate').value = '';
            document.getElementById('prodate').value = '';
            document.getElementById('purdate').value = '';
          },
          complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
              $('#loader').addClass('hidden')
          },
      });
  });

</script>

@endsection
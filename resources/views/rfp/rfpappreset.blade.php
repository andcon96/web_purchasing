@extends('layout.layout')

@section('breadcrumbs')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{url('/')}}">Transaksi</a></li>
    <li class="breadcrumb-item active">RFP Approval Utility</li>
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
		<div class="alert alert-success  alert-dismissible fade show"  role="alert">
			{{ session()->get('updated') }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	@endif

  <input type="hidden" id="tmprfpnumber"/>
	<input type="hidden" id="tmpsupplier"/>
	<input type="hidden" id="tmpenduser"/>
	<input type="hidden" id="tmpdatefrom"/>
	<input type="hidden" id="tmpdateto"/>


<div class="form-group row col-md-12">
      <label for="s_rfpnumber" class="col-md-2 col-form-label text-md-right">{{ __('RFP No.') }}</label>
      <div class="col-md-3">
          <input id="s_rfpnumber" type="text" class="form-control" name="s_rfpnumber" 
          value="" autofocus autocomplete="off">
      </div>
      <label for="s_supplier" class="col-md-2 col-form-label text-md-right">{{ __('Supplier') }}</label>
      <div class="col-md-3">
          <input id="s_supplier" type="text" class="form-control" name="s_supplier" 
          value="" autofocus autocomplete="off">
      </div>
  </div>

  <div class="form-group row col-md-12">
    <label for="s_enduser" class="col-md-2 col-form-label text-md-right">{{ __('End User') }}</label>
    <div class="col-md-3">
      <input id="s_enduser" type="text" class="form-control" name="s_enduser" autofocus autocomplete="off">
    </div>
  </div>

  <div class="form-group row col-md-12">
    <label for="datefrom" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('From') }}</label>
      <div class="col-md-4 col-lg-3">
            <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="YYYY-MM-DD"
                  required autofocus autocomplete="off">
        </div>
      <label for="dateto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('To') }}</label>
        <div class="col-md-4 col-lg-3">
          <input type="text" id="dateto" class="form-control" name='dateto' placeholder="YYYY-MM-DD"
                  required autofocus autocomplete="off">
    </div>
      
    <div class="offset-0">
          <input type="button" class="btn bt-ref" 
      id="btnsearch" value="Search" />
      <button class="btn bt-ref" id='btnrefresh' style="margin-left: 10px; width: 40px !important"><i class="fa fa-sync"></i></button>
    </div>
</div>

<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    	<thead>
	      	<tr>
		    	<!-- <th style="width:5%;">No.</th> -->
		     	<th style="width:10%;">RFP No.</th>
				  <th style="width:15%;">Request By</th>
				  <th style="width:15%;">Supplier</th>
		     	<th style="width:10%;">End User</th>
		     	<th style="width:10%;">Due date</th>
		     	<th style="width:10%;">Reset</th>
		  	</tr>
	   	</thead>
		<tbody>
      @include('rfp.tablerfpreset')
	  </tbody>
	</table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	<input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
	<input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">RFP Approval Utility</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <form class="form-horizontal" method="POST" action="/resetrfpapp" id="update">
            {{ csrf_field() }}

            <div class="modal-body">
                <div class="d-flex">
                   Reset Approval RFP : <p id='tmp_rfpnbr' style="margin-left:15px;"></p>
                   <input type="hidden" name="t_rfpnbr" id="t_rfpnbr">
                  <div class="custom-control custom-checkbox" style="margin-left: 15px">
                          <input type="checkbox" class="custom-control-input" id="cbsubmit" required>
                          <label class="custom-control-label" for="cbsubmit">Confirm to Reset RFP</label>
                   </div>
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Reset</button>
              <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
              </button>
            </div>
       </form>

    </div>
  </div>
</div>

<div id="loader" class="lds-dual-ring hidden overlay"></div>

@endsection


@section('scripts')

  <script>
      $( function() {
          $( "#datefrom" ).datepicker({
              dateFormat : 'yy-mm-dd'
          });
      });
        $( function() {
          $( "#dateto" ).datepicker({
              dateFormat : 'yy-mm-dd'
          });
      });


      function fetch_data(page, rfpnumber, supplier, requestby, datefrom, dateto) {
        $.ajax({
          url: "/rfputil/utilrfpsearch?page=" + page + "&rfp=" + rfpnumber + "&supp=" + supplier + "&enduser=" + requestby + "&datefrom=" + datefrom + "&dateto=" + dateto,
          beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
            $('#loader').removeClass('hidden')
          },
          success: function(data) {
            console.log(data);
            $('tbody').html('');
            $('tbody').html(data);
          },
          complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
            $('#loader').addClass('hidden')
          },

        })
      }


    $(document).on('click', '#btnsearch', function() {
      var rfpnumber  = $('#s_rfpnumber').val(); 
      var supplier    = $('#s_supplier').val(); 
      // var status = $('#s_status').val();
      var requestby = $('#s_enduser').val();
      var datefrom = $('#datefrom').val();
	    var dateto = $('#dateto').val();

      // var column_name = $('#hidden_column_name').val();
      // var sort_type = $('#hidden_sort_type').val();
      var page = 1;

      document.getElementById('tmprfpnumber').value  = rfpnumber;
      document.getElementById('tmpsupplier').value = supplier;
      // document.getElementById('tmpstatus').value = status;
      document.getElementById('tmpenduser').value = requestby;
      document.getElementById('tmpdatefrom').value = datefrom;
	  document.getElementById('tmpdateto').value = dateto;

      fetch_data(page, rfpnumber, supplier, requestby, datefrom, dateto);
    });

  
    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();

      var rfpnumber  = $('#tmprfpnumber').val(); 
      var supplier    = $('#tmpsupplier').val(); 
      // var status = $('#tmpstatus').val();
      var requestby = $('#tmpenduser').val();
      var datefrom = $('#tmpdatefrom').val();
	    var dateto = $('#tmpdateto').val();
      
      fetch_data(page, rfpnumber, supplier, requestby, datefrom, dateto);
    });

	$(document).on('click', '#btnrefresh', function() {
	    var rfpnumber  = ''; 
      var supplier    = ''; 
      // var status = '';
      var requestby = '';
      var datefrom = '';
	    var dateto = ''; 
      var page = 1;

      document.getElementById('s_rfpnumber').value = '';
      document.getElementById('s_supplier').value = '';
      // document.getElementById('s_status').value = '';
      document.getElementById('s_enduser').value = '';
      document.getElementById('datefrom').value = '';
      document.getElementById('dateto').value = '';
	    document.getElementById('tmprfpnumber').value  = rfpnumber;
      document.getElementById('tmpsupplier').value = supplier;
      // document.getElementById('tmpstatus').value = status;
      document.getElementById('tmpenduser').value = requestby;
      document.getElementById('tmpdatefrom').value = datefrom;
	    document.getElementById('tmpdateto').value = dateto;

      fetch_data(page, rfpnumber, supplier, requestby, datefrom, dateto);
    });

    //   $(document).on('click','.pagination a', function(e){
    //     e.preventDefault();

    //     //alert('123');
    //     var page = $(this).attr('href').split('?page=')[1];

    //     //console.log(page);
    //     getData(page);

    //   });

    // function getData(page){
    //   var rfpnumber = document.getElementById('s_rfpnumber').value;
    //   var supplier = document.getElementById('s_supplier').value;
    //   var requestby = document.getElementById('s_enduser').value;
    //   var datefrom = document.getElementById('datefrom').value;
    //   var dateto = document.getElementById('dateto').value;
    //   $.ajax({
    //     url: '/utilrfpsearch/fetch_data?page='+ page,
    //     type: "get",
    //     data : {
    //         rfp : rfpnumber,
    //         supp : supplier,
    //         datefrom : datefrom,
    //         dateto : dateto,
    //         enduser : requestby,
    //     },
    //     datatype: "html" 
    //   }).done(function(data){
    //     console.log('Page = '+ page);

    //     $(".tag-container").empty().html(data);

    //   }).fail(function(jqXHR, ajaxOptions, thrownError){
    //     Swal.fire({
    //               icon: 'error',
    //               text: 'No Response From Server',
    //           })
    //   });
    // }

    // $('#btnsearch').on('click', function(){
    //     var rfpnumber = document.getElementById('s_rfpnumber').value;
    //     var supplier = document.getElementById('s_supplier').value;
    //     var enduser = document.getElementById('s_enduser').value;
    //     var datefrom = document.getElementById('datefrom').value;
    //     var dateto = document.getElementById('dateto').value;


    //     jQuery.ajax({
    //       type : "get",
    //       url : "{{URL::to("utilrfpsearch")}}",
    //       data : {
    //         rfp : rfpnumber,
    //         supp : supplier,
    //         datefrom : datefrom,
    //         dateto : dateto,
    //         enduser : enduser,
    //       },
    //       beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    //           $('#loader').removeClass('hidden')
    //       },
    //       success:function(data){
    //         console.log(data);
    //         $(".tag-container").empty().html(data);
    //       },
    //       complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
    //           $('#loader').addClass('hidden')
    //       },
    //     });
    // });

    // $('#btnrefresh').on('click', function(){
    //     var  rfp = '';
    //     var supp = '';
    //     var enduser = '';
    //     var datefrom = '';
    //     var dateto = '';

    //     jQuery.ajax({
    //       type : "get",
    //       url : "{{URL::to("utilrfpsearch")}}",
    //       data : {
    //         rfp : rfp,
    //         supp : supp,
    //         enduser : enduser,
    //         datefrom : datefrom,
    //         dateto : dateto,
    //       },
    //       beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
    //           $('#loader').removeClass('hidden')
    //       },
    //       success:function(data){
    //         console.log(data);
    //         document.getElementById('s_rfpnumber').value = '';
    //         document.getElementById('s_supplier').value = '';
    //         document.getElementById('s_enduser').value = '';
    //         document.getElementById('datefrom').value = '';
    //         document.getElementById('dateto').value = '';
    //         $(".tag-container").empty().html(data);
    //       },
    //       complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
    //           $('#loader').addClass('hidden')
    //       },
    //     });

    // });

    $(document).on('click','.confirmReset',function(){ // Click to only happen on announce links
      
          var rfpnbr = $(this).data('rfpnbr');
          var t_rfpnbr = $(this).data('rfpnbr');

          document.getElementById("tmp_rfpnbr").innerHTML = rfpnbr;
          document.getElementById("t_rfpnbr").value = t_rfpnbr;
    });

    $(document).on('submit', '#update', function(e) {
          document.getElementById('e_btnclose').style.display = 'none';
          document.getElementById('e_btnconf').style.display = 'none';
          document.getElementById('e_btnloading').style.display = '';
    });

  </script>

@endsection
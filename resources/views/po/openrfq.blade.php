@extends('layout.layout')

@section('menu_name','Open RFQ')

@section('content')

<!-- ===============================search======================================== -->


  <div class="form-group row">
    <label for="openrfq" class="col-md-2 col-lg-2 col-form-label">{{ __('RFQ No.') }}</label>
      <div class="col-md-2">
        <input id="openrfq" type="text" class="form-control" name="openrfq" value="" autofocus>
      </div>
      <div class="col-md-2">
        <input type="button" class="btn bt-action" id="btnsearch" value="Search" />
      </div>
  </div>
  @include('po.tbopenrfq')

<!-- ===============================search======================================== -->


@endsection


@section('scripts')
<script type="text/javascript">

$(document).on('click','.pagination a', function(e){
      e.preventDefault();

      //alert('123');
      var page = $(this).attr('href').split('?page=')[1];

      //console.log(page);
      getData(page);

      });

      function getData(page){

        $.ajax({
            url: '/pagination/rfqsearch2?page='+ page,
            type: "get",
            datatype: "html" 
        }).done(function(data){
              console.log('Page = '+ page);

              $(".tag-container").empty().html(data);
              //location.hash = page;
              //console.log(data);
        }).fail(function(jqXHR, ajaxOptions, thrownError){
              alert('No response from server');
        });
      }

  $('#btnsearch').on('click',function()
  {

      var openrfq = document.getElementById("openrfq").value;

      jQuery.ajax
      ({
          type : "get",
          url : "{{URL::to("rfqsearch2") }}",
          data:
          {
            rfq_search : openrfq,
            // supplier : supp,
          },
          success:function(data)
          {
            // $('#dataTable').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
          }
      });
  }); 

</script>

@endsection

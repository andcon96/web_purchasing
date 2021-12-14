@extends('layout.layout')

@section('menu_name','PO Approval Browse')

@section('content')
<!-- ===============================search======================================== -->

    <div class="form-group row">
      <label for="poorder" class="col-md-1 col-lg-2 col-form-label">{{ __('Purchase Order') }}</label>
        <div class="col-md-2">
          <input id="poorder" type="text" class="form-control" name="poorder" value="" autofocus>
        </div>
        <div class="col-md-2">
          <input type="button" class="btn bt-action" id="btnsearch" value="Search" />
        </div>
    </div>
    @include('po.tbpoappbrw')
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
            url: '/pagination/poappsearch?page='+ page,
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

      var poorder = document.getElementById("poorder").value;

      jQuery.ajax
      ({
          type : "get",
          url : "{{URL::to("poappsearch") }}",
          data:
          {
            po_search : poorder,
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

@extends('layout.layout')

@section('menu_name','Number Of Purchase Order')

@section('content')
<!-- ===============================search======================================== -->


        <div class="form-group row">
          <label for="nbrpo" class="col-md-1 col-lg-2 col-form-label">{{ __('Purchase Order') }}</label>
            <div class="col-md-2">
              <input id="nbrpo" type="text" class="form-control" name="nbrpo" value="" autofocus>
            </div>
            <div class="col-md-2">
              <input type="button" class="btn bt-action" id="btnsearch" value="Search" />
            </div>
        </div>
        @include('po.tbnbrofpo')
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
            url: '/pagination/nbrofposearch2?page='+ page,
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

      var nbrpo = document.getElementById("nbrpo").value;

      jQuery.ajax
      ({
          type : "get",
          url : "{{URL::to("nbrofposearch2") }}",
          data:
          {
            nbrpo_search2 : nbrpo,
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
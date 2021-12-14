@extends('layout.layout')

@section('menu_name','Past Due Purchase Order')

@section('content')
        
    <!-- ===============================search======================================== -->

        <div class="form-group row">
          <label for="poorder" class="col-md-1 col-lg-2 col-form-label">{{ __('Purchase Order') }}</label>
            <div class="col-md-2">
              <input id="poorder" type="text" class="form-control" name="poorder" value="" autofocus>
             <!--  <input id="poorder2" type="" class="form-control" name="poorder2" value="{{ $id = 'b' }}" autofocus> -->
            </div>
            <div class="col-md-2">
              <input type="button" class="btn bt-action" id="btnsearch" value="Search" />
            </div>
        </div>
        @include('po.tbpastduepo')

    <!-- ===============================search======================================== -->


    

@endsection

@section('scripts')
<!--Script Modal & Search AJAX-->
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
      url: '/pagination/pastduesearch3?page='+ page,
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
        // var poorder2 = document.getElementById("poorder2").value;

        jQuery.ajax
        ({
            type : "get",
            url : "{{URL::to("pastduesearch3") }}",
            data:
            {
              po_search3 : poorder,
              // po_search2 : poorder2,
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
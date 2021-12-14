@extends('layout.layout')

@section('menu_name','Unconfirm PO By Supplier')

@section('content')
<!--Script Untuk Search Ajax-->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<!--CSS Untuk Table Responsive-->
<style type="text/css">
    tbody{
        font-size: 14px;

    }

    h1{
      color: black !important;
    }

    thead{
        background-color: #4e73df;
        text-align: left;
        color:white !important;
    }

    label, .page-item{
        font-size:18px !important;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    .datarow{
        margin-bottom: 5px !important;
    }


    #dataTable th{
        padding: 7px 5px 7px 5px;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:16px;
        font-weight: 600;
        padding:5px 0px 5px 5px;
    }

    .bt-action{
      font-size: 16px;
      width: 120px;
      background-color:#4e73df;
      color:white;
    }
    
    .bt-cancel{
      font-size: 16px;
      width: 120px;
      background-color:#A9A9A9;
      color:white;
    }

    .bt-action:hover, .bt-cancel:hover{
      color:black;
    }

    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }

     @media only screen and (max-width: 992px) and (min-width: 768px){
        .seconddata{
            margin-top:15px;
        }
    }

    @media only screen and (max-width: 768px) {
        #btnsearch{
            margin-top:15px;
        }
    }

</style>

    <!-- ===============================search======================================== -->


        <div class="form-group row">
          <label for="unpo" class="col-md-1 col-lg-2 col-form-label">{{ __('Purchase Order') }}</label>
            <div class="col-md-2">
              <input id="unpo" type="text" class="form-control" name="unpo" value="" autofocus>
            </div>
            <div class="col-md-2">
              <input type="button" class="btn btn-info" id="btnsearch" value="Search" />
            </div>
        </div>
        @include('po.tbunpobysupp')

    <!-- ===============================search======================================== -->

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
            url: '/pagination/unposearch?page='+ page,
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
    
              var unpo = document.getElementById("unpo").value;
    
              jQuery.ajax
              ({
                  type : "get",
                  url : "{{URL::to("unposearch") }}",
                  data:
                  {
                    unposrc : unpo,
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


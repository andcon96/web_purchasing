@extends('layout.layout')
  
@section('content')
  <style type="text/css">
    @media (min-width: 1000px) {
      .chart-bar {
        height: 210px;

      }

    }

   
    .fa-sync:hover{
      color: red !important;
      cursor: pointer;
    }

    .heading
    {
        font-size: 20px !important;
        color: black;
    }

    div.a {
      text-align: center;
      font-size: 15px !important;
      word-spacing: 80px;
      text-indent:20px;
      font-style: normal;
      /*font-weight: bold;*/

    }

    div.b {
      text-align: center;
      font-size: 18px !important;
      word-spacing: 83px;
      text-indent:38px;
      height: 6px; /*lebar canvas ke bawah*/
      font-weight: bold;
     /* vertical-align: text-bottom;*/
      align-items: flex-end;
    }


    /*Number of Purchase Order*/
    div.c {
      text-align: center;
      font-size: 18px !important;
      word-spacing: 110px;
      text-indent:10px;
      height: 20px; /*lebar canvas ke bawah*/
      /*    color: green;*/
      /* font-style: normal;*/
      font-weight: bold;
       /*font-family: "Times New Roman", Times, serif;*/
    }

    div.d {
      text-align: center;
      font-size: 10px !important;
      word-spacing: 55px;
      text-indent:35px;
      color: green;
      font-style: normal;

    }

    div.d1 {
      text-align: center;
      font-size: 10px !important;
      word-spacing: 40px;
      text-indent:35px;
      color: green;
      font-style: normal;
      /*font-family: "Times New Roman", Times, serif;*/
    }

    div.d2 {
      text-align: center;
      font-size: 10px !important;
      word-spacing: 80px;
      text-indent:10px;
      font-weight: bold;
      height: 2px;

    }

    div.d3 {
      text-align: center;
      font-size: 15px !important;
      height: 27px;
      font-weight: bold;

    }

    div.a1 {
      text-align: center;
      font-size: 120 !important;
      color: green;
     /* font-style: normal;*/
      font-weight: bold;
      /*font-family: "Times New Roman", Times, serif;*/
    }

    /*======lebar canvas Purchase Order Approval Status===========*/
    div.a2 {
      text-align: center;
      font-size: 10px !important;
      font-weight: bold;
      height: 31px; /*lebar canvas ke bawah*/
    
    /*======lebar canvas Purchase Order Approval Status===========*/
    
    /* Garis Pemisah */
    hr.rounded {
      border-top: 3px solid #bbb;
      border-radius: 5px;
    }
    
    a:link {
      color: white;
      background-color: transparent;
      text-decoration: none;
    }
    a:visited {
      color: black;
      background-color: transparent;
      text-decoration: none;
    }
    a:hover {
      color: blue;
      background-color: transparent;
      text-decoration: underline;
    }
    a:active {
      color: red;
      background-color: transparent;
      text-decoration: underline;
    }

  </style>


  <!--Error Message-->
  <div class="row">
        @if(session('errors'))
          <div class="alert alert-danger">
            @foreach($errors as $error)
              <li>{{ $error }}</li>
            @endforeach
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger" id="getError">
            {{ session()->get('error') }}
          </div>
        @endif

        @if(session('updated'))
          <div class="alert alert-success" id="getUpdated">
            {{ session()->get('updated') }}
          </div>
        @endif
  </div>

  <!-- <div class="container"> -->
  <div class="row">
    <div class="col-lg-4">
      <div class="card border-left-info shadow">
        <div class="card-header">
          <h6 class="header-text m-0 font-weight-bold" style="text-align: center;">PO Approval Status
            <i class="fas fa-sync fa-sm fa-fw text-gray-400" style="float: right;" onclick="chart1()"></i>
          </h6>
        </div>
        <div class="card-body">      
          <div class="no-gutters align-items-center">
          
              <div class="d3 header-text m-0 ">Total Unapproved: <?php $obj = json_decode($expitem2,true); echo $obj[0]['total'] ; ?> </div>

            <div>
              <div class=""><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div></div></div><div class="chartjs-size-monitor-shrink"><div></div></div></div>
              <canvas width="570" height="100" class="chartjs-render-monitor" id="myChart1" style="width: 374; height:201px; display: block;"></canvas>
            </div>

            <div class="a2">
              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="green" /> <!-- Sampai sini -->
              </svg> Approved PO : <?php $obj = json_decode($expitem1,true); echo $obj[0]['total'] ; ?>  <br>

              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="yellow" /> <!-- Sampai sini -->
              </svg> Unapproved PO > 3 Days : <?php $obj = json_decode($expitem3,true); echo $obj[0]['total'] ; ?>  <br>

              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="red" /> <!-- Sampai sini -->
              </svg> Unapproved PO > 7 Days : <?php $obj = json_decode($expitem4,true); echo $obj[0]['total'] ; ?>  <br>
            </div>

            </div>
          </div>        
        </div> <!-- end of class="card-body" -->
      </div>
    </div> <!-- end of class="col-lg-4 -->

    <div class="col-lg-4">
      <div class="card border-left-info shadow">
        <div class="card-header">
          <h6 class="header-text m-0 font-weight-bold" style="text-align: center;">Past Due Purchase Order
            <i class="fas fa-sync fa-sm fa-fw text-gray-400" style="float: right;" onclick="chart2()"></i>
          </h6>
        </div>
        <div class="card-body">
          <div class="no-gutters align-items-center">

            <div class="d3 header-text m-0 ">Total Past Due PO: <?php $obj = json_decode($pastduepo,true); echo $obj[0]['total'] ; ?> </div>

                  <div>
                      <div class="chart-bar pt-4 pb-2"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div></div></div><div class="chartjs-size-monitor-shrink"><div></div></div></div>
                     <canvas width="370" height="100" class="chartjs-render-monitor" id="myChart2" style="width: 374; height: 100px; display:   block;"></canvas>
                  </div>
                  <div class="b"> 
                    <i style="color:black" > <?php $obj = json_decode($item1,true); echo $obj[0]['total'] ; ?> </i>
                  
                    <i style="color:black"> <?php $obj = json_decode($item2,true); echo $obj[0]['total'] ; ?> </i>
                  
                    <i style="color:black"> <?php $obj = json_decode($item3,true); echo $obj[0]['total'] ; ?> </i>
                  </div>
                   <p> </p>
                    <!-- <i style="color:black;"> &nbsp;</i> -->
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-4 -->

    <div class="col-lg-4">
      <div class="card border-left-info shadow">
        <div class="card-header">
          <h6 class="header-text m-0 font-weight-bold" style="text-align: center;">Number of Purchase Order 
          <i class="fas fa-sync fa-sm fa-fw text-gray-400" style="float: right;" onclick="chart3()"></i>
          </h6>
        </div>
        <div class="card-body">

          <div class="no-gutters align-items-center">
            <div>
              <div class="chart-bar pt-4 pb-2"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div></div></div><div class="chartjs-size-monitor-shrink"><div></div></div></div>
                      <canvas width="370" height="100" class="chartjs-render-monitor" id="myChart3" style="width: 374; height: 100px; display:   block;"></canvas>
              </div>  
              <div class="c"> 
                <i style="color:black;"> <?php $obj = json_decode($potot1,true); echo $obj[0]['total'] ; ?> </i>
                <i style="color:black;" > <?php $obj = json_decode($potot2,true); echo $obj[0]['total'] ; ?> </i>
                <i style="color:black;"> <?php $obj = json_decode($potot3,true); echo $obj[0]['total'] ; ?> </i>
              </div>
              <br>
              <div class="d2">                     
                <i style="color:black;"> <?php $obj = json_decode($potot1,true); echo number_format($obj[0]['poamt']);?></i>
                <i style="color:black;"> <?php $obj = json_decode($potot2,true); echo number_format($obj[0]['poamt']);?></i>
                <i style="color:black;"> <?php $obj = json_decode($potot3,true); echo number_format($obj[0]['poamt']);?></i>
              </div>
            </div>
          </div>
        </div> <!-- end of card body -->
      </div>
    </div> <!-- end of class="col-lg-4 -->

  </div>
  <!-- </div> --> <!-- end of container -->

  <hr class="rounded"> <!-- garis pemisah -->

  <div class="row">
    <div class="col-lg-3">
      <div class="card card border shadow">
        <div class="card-header py-3 flex-row align-items-center justify-content-between" style="text-align: center; background-color: #E0FFFF">
          <div class="text-center" style="text-align: center;">
            <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <h6 class="font-weight-bold"><a style="color:black; font-weight: bold;" href="/unpobysupp">Unconfirm PO By Supplier</a></h6>
          </div>
        </div> 
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="a1"> 
                    <h4 style="color:black; font-weight: bold; " > <?php $obj = json_decode($unpobysupp,true); echo $obj[0]['total'] ; ?> </h4>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

    <div class="col-lg-3">
      <div class="card card border shadow">
        <div class="card-header py-3 flex-row align-items-center justify-content-between" style="text-align: center; background-color: #E0FFFF">
          <div class="text-center" style="text-align: center; color: #ff0000">
            <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <h6 class="font-weight-bold"><a style="color:black; font-weight: bold;"href="/upcoming">PO Due in 7 Days</a></h6>
          </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="a1"> 
                    <h4 style="color:black; font-weight: bold;"> <?php $obj = json_decode($upcomingpo,true); echo $obj[0]['total'] ; ?> </h4>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

    <div class="col-lg-3">
      <div class="card card border shadow">
        <div class="card-header py-3 flex-row align-items-center justify-content-between" style="text-align: center; background-color: #E0FFFF">
          <div class="text-center" style="text-align: center; color: #ff0000">
           <h6 class="font-weight-bold"><a style="color:black; font-weight: bold; " href="/openpo">Open Purchase Order</a></h6>
          </div> 
        </div>
        <div class="card-body">
          <div class="a1"> 
            <h4 style="color:black; font-weight: bold;"> <?php $obj = json_decode($openpo,true); echo $obj[0]['total'] ; ?> </h4>
          </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

    <div class="col-lg-3">
      <div class="card border shadow">
        <div class="card-header py-3  flex-row align-items-center justify-content-between" style="text-align: center; background-color: #E0FFFF">
          <div class="text-center" style="text-align: center;">
            <!-- <h6 class="font-weight-bold">Open RFQ </h6>  -->
            <h6 class="font-weight-bold"><a style="color:black; font-weight: bold; "href="/openrfq" >Open RFQ</a></h6>
          </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="a1"> 
                    <h4 style="color:black; font-weight: bold;"> <?php $obj = json_decode($openrfq,true); echo $obj[0]['total'] ; ?> </h4>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

                   

  </div> <!-- End of Row -->
@endsection

@section('scripts')


<!--Chart 1-->
<script type="text/javascript">

    function chart1(){
          jQuery.ajax({
              type : "get",
              url : "{{URL::to("refExpItem") }}",
              success:function(data){
                  

                  var exp1 = data['expitem1'][0]['total'];
                  var exp2 = data['expitem2'][0]['total'];
                  var exp3 = data['expitem3'][0]['total'];
                  var exp4 = data['expitem4'][0]['total'];

                  myExpItem.data.datasets[0].data[0] = exp1;
                  myExpItem.data.datasets[0].data[1] = exp2;
                  myExpItem.data.datasets[0].data[2] = exp3;
                  myExpItem.data.datasets[0].data[3] = exp4;
                  myExpItem.update();
              }
          });
    }

    function chart2(){
          jQuery.ajax({
              type : "get",
              url : "{{URL::to("refExpItem") }}",
              success:function(data){
                  

                  var exp1 = data['item1'][0]['total'];
                  var exp2 = data['item2'][0]['total'];
                  var exp3 = data['item3'][0]['total'];


                  myExpItem.data.datasets[0].data[0] = exp1;
                  myExpItem.data.datasets[0].data[1] = exp2;
                  myExpItem.data.datasets[0].data[2] = exp3;
                  myExpItem.update();

              }
          });
    }

    function chart3(){
          jQuery.ajax({
              type : "get",
              url : "{{URL::to("refExpItem") }}",
              success:function(data){
                  

                  var exp1 = data['potot1'][0]['total'];
                  var exp2 = data['potot2'][0]['total'];
                  var exp3 = data['potot3'][0]['total'];


                  myExpItem.data.datasets[0].data[0] = exp1;
                  myExpItem.data.datasets[0].data[1] = exp2;
                  myExpItem.data.datasets[0].data[2] = exp3;
                  myExpItem.update();

              }
          });
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';


    function poClickEvent(event, array)
    {
        if(array[0])
        {
            let element = this.getElementAtEvent(event);
            if (element.length > 0) {
                //var series= element[0]._model.datasetLabel;
                //var label = element[0]._model.label;
                //var value = this.data.datasets[element[0]._datasetIndex].data[element[0]._index];

                var clickedDatasetIndex = element[0]._datasetIndex;
                var clickedElementindex = element[0]._index;
                var label = myChart1.data.labels[clickedElementindex];
                var value = myChart1.data.datasets[clickedDatasetIndex].data[clickedElementindex];     
                // alert("Clicked: " + label + " - " + value);
                // window.location = "/pastduepo"; 
                //console.log()

                //alert(label);

                if (label == 'Approved PO')
                {
                  // alert('123');
                 window.location = "/poappbrw"; 
                }

                // if (label == 'Unapproved PO')
                // {
                //   // alert('test');
                //  window.location = "/openpo"; 
                // }

                if (label == 'Unapproved > 3 Days')
                {
                  // alert('9999999');
                 window.location = "/poappbrw2"; 
                }

                if (label == 'Unapproved > 7 Days')
                {
                  // alert('9999999');
                 window.location = "/poappbrw3"; 
                }

            }
        }
    }

    var ctx = document.getElementById("myChart1").getContext('2d');
    var data = 
    {
        datasets: 
        [{
            data: 
            [
            <?php $obj = json_decode($expitem1,true); echo $obj[0]['total'] ; ?>, 
            <?php $obj = json_decode($expitem3,true); echo $obj[0]['total'] ; ?>,
            <?php $obj = json_decode($expitem4,true); echo $obj[0]['total'] ; ?>
            ],
            backgroundColor: ['#006400','#ffff00','#ff0000','#ff0000'],
        }],
        
          labels: ['Approved PO', 'Unapproved > 3 Days', 'Unapproved > 7 Days'],

    };
    var myChart1 = new Chart(ctx, {
        type: 'pie',
        data: data,
      options: {
        responsive: true,
        onClick: poClickEvent,
        maintainAspectRatio: false,
        tooltips: {
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: true,
          caretPadding: 10,
        },
      scales: {
        xAxes: [{
          scaleLabel: {
            display: false,
            labelString: [' > 3 Days']
          },
          time: {
            unit: 'month'
          },
          gridLines: {
            display: true,
            drawBorder: true
          },
          ticks: {
            maxTicksLimit: 6,
            fontSize : 0 // menyembunyikan label yang berkaitan degan horizontal tau xAxes
          },
          maxBarThickness: 50, // lebar bar vertical
        }],
        
      },
        legend: {
          display: false,
          fontStyle: 'normal',
          fontFamily: 'Arial',
          align: 'center',
          fontSize: 5,
          position: 'top',
          fullWidth: true,
          boxWidth: 40,
        },
        title: { display: false, text: 'Fruit in stock'},
        cutoutPercentage: 1, // lebar bar Pie
        hover: {
          onHover: function(e) {
              var point = this.getElementAtEvent(e);
              if (point.length) e.target.style.cursor = 'pointer';
              else e.target.style.cursor = 'default';
          }
        }
      }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';


    function poClickEvent2(event, array)
    {
        if(array[0])
        {
            let element = this.getElementAtEvent(event);
            if (element.length > 0) {

                var clickedDatasetIndex = element[0]._datasetIndex;
                var clickedElementindex = element[0]._index;
                var label = myChart2.data.labels[clickedElementindex];
                var value = myChart2.data.datasets[clickedDatasetIndex].data[clickedElementindex];     

                if (label == '1-7 Days')
                {
                  // alert('123');
                 window.location = "/pastduepo"; 
                }

                if (label == '8-30 Days')
                {
                  // alert('test');
                 window.location = "/pastduepo2"; 
                }

                if (label == '> 30 Days')
                {
                  // alert('9999999');
                 window.location = "/pastduepo3"; 
                }
                              
            }
        }
    }

    var ctx = document.getElementById("myChart2").getContext('2d');
    var data = 
    {
        datasets: 
        [{
            data: 
            [
            <?php $obj = json_decode($item1,true); echo $obj[0]['total'] ; ?>,
            <?php $obj = json_decode($item2,true); echo $obj[0]['total'] ; ?>, 
            <?php $obj = json_decode($item3,true); echo $obj[0]['total'] ; ?>
            ],
            backgroundColor: ['#006400','#ffff00','#ff0000','#ff0000'],
            // hoverBackgroundColor: "#2e59d9",
            // borderColor: "#4e73df",
        }],
        
        labels: ['1-7 Days','8-30 Days', '> 30 Days'],
        // fontColor: 'black'
    };
    var myChart2 = new Chart(ctx, {
        type: 'bar',
        data: data,
      options: {
        responsive: true,
        onClick: poClickEvent2,
        maintainAspectRatio: false,
        tooltips: {
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: true,
          caretPadding: 10,
        },
      scales: {
        xAxes: [{
          time: {
            unit: 'month'
          },
          gridLines: {
            display: true,
            drawBorder: true
          },
          ticks: {
            maxTicksLimit: 6,
            fontColor: 'black',
            fontStyle: 'bold'
          },
          maxBarThickness: 40, // lebar bar
        }],
        yAxes: [{
          ticks: {
            min: 0,
            // max:,
            fontColor:"#000000",
            maxTicksLimit: 40,
            padding: 10,
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [5],
            zeroLineBorderDash: [2]
          }
        }],
      },
        legend: {
          display: false
        },
        cutoutPercentage: 60,
        hover: {
          onHover: function(e) {
              var point = this.getElementAtEvent(e);
              if (point.length) e.target.style.cursor = 'pointer';
              else e.target.style.cursor = 'default';
          }
        }
      }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
      // *     example: number_format(1234.56, 2, ',', ' ');
      // *     return: '1 234,56'
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';


    function poClickEvent3(event, array)
    {
        if(array[0])
        {
            let element = this.getElementAtEvent(event);
            if (element.length > 0) {

                var clickedDatasetIndex = element[0]._datasetIndex;
                var clickedElementindex = element[0]._index;
                var label = myChart3.data.labels[clickedElementindex];
                var value = myChart3.data.datasets[clickedDatasetIndex].data[clickedElementindex];     


                if (label == 'This Months')
                {
                  // alert('123');
                 window.location = "/nbrofpo1"; 
                }

                if (label == 'Last Months')
                {
                  // alert('test');
                 window.location = "/nbrofpo2"; 
                }

                if (label == 'Last 2 Months')
                {
                  // alert('9999999');
                 window.location = "/nbrofpo3"; 
                }
                              
            }
        }
    }


    // myChart3

    var ctx = document.getElementById("myChart3").getContext('2d');
    var data = 
    {
        datasets: 
        [{
            data: 
            [
            <?php $obj = json_decode($potot1,true); echo $obj[0]['total'] ; ?>,
            <?php $obj = json_decode($potot2,true); echo $obj[0]['total'] ; ?>, 
            <?php $obj = json_decode($potot3,true); echo $obj[0]['total'] ; ?>
            ],
            // backgroundColor: ['#00cc66','#ff0000','#ff0000','#ff0000'],
            hoverBackgroundColor: "#",
            borderColor: "#4e73df",
        }],
        
        labels: ['This Months','Last Months', 'Last 2 Months']
    };
    var myChart3 = new Chart(ctx, {
        type: 'line',
        data: data,
      options: {
        responsive: true,
        onClick: poClickEvent3,
        maintainAspectRatio: false,
        tooltips: {
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: true,
          caretPadding: 10,
        },
      scales: {
        xAxes: [{
          time: {
            unit: 'month'
          },
          gridLines: {
            display: true,
            drawBorder: true
          },
          ticks: {
            maxTicksLimit: 6,
            fontColor: 'black',
            fontStyle: 'bold'
          },

          maxBarThickness: 40, // lebar bar
        }],
        yAxes: [{
          ticks: {
            // stepSize: 1,
            min: 0,
            // max: ,
            fontColor:"#000000",
            maxTicksLimit: 40,
            padding: 20,
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }],
      },
        legend: {
          display: false
        },
        cutoutPercentage: 60,
        hover: {
          onHover: function(e) {
              var point = this.getElementAtEvent(e);
              if (point.length) e.target.style.cursor = 'pointer';
              else e.target.style.cursor = 'default';
          }
        }
      }
    });
</script>




@endsection

@section('footer')
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
          <!--   <span>Copyright &copy; PT Intelegensia Mustaka Indonesia 2020</span> -->
          </div>
        </div>
      </footer>
@endsection

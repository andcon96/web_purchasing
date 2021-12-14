@extends('layout.layout')

@section('content')
<style type="text/css">
 @media (min-width: 992px) {
      .chart-pie {
        height: 150px !important;
        padding-bottom: 0px !important;
        padding-top: 10px !important;
      }

      .card-header{
        height: 10px !important;
      }

      .card-body{
        padding: 5px !important;
      }

      .header-text{
        font-size: 12px !important;
      }

    }

    .fa-refresh:hover{
      color: red !important;
      cursor: pointer;
    }
    
   .satu {
   font-size: 25px !IMPORTANT;
   color:black !IMPORTANT;
   background-color:	#F0E68C;
   text-align: center !IMPORTANT;
   
   }
      .empat {
   font-size: 30px;   
   color:darkblue;
   }  

   
      .dua {
   font-size: 25px !IMPORTANT;
   color:black !IMPORTANT;
   background-color:#FFE4C4;
   text-align: center !IMPORTANT;
   
   }
   
   .manu {
   font-size: 30px;
   color:white !IMPORTANT;
   background-color:brown;
   }
</style>


	<div class="row">
	
		<div class="col-xl-6 col-lg-6 col-md-12 mb-4">
        
		
</br>
            
           
         <p class="header-text m-0 font-weight-bold text-primary"><center>Item Expired in :</center></p>
		  <div id='divpie'>
        
          <div class="card-body">
            <div class="no-gutters align-items-center">
                    <div class="col mr-2 text-center ">
                  
                    </div>
              
                <div class="chart-pie pt-4 pb-2"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div></div></div>
                  <div class="chartjs-size-monitor-shrink"></div></div>
                 
          
                  <canvas background-color:"red" width="274" height="00" class="chartjs-render-monitor" id="myexpitm" style="display: block; height: 220px; width: 640px;" ></canvas>
                
                </div>
              </div>
            <center>  
              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="red" /> <!-- Sampai sini -->
              </svg> 0 days : <?php $obj = json_decode($invdx1,true);echo $obj ?>  &nbsp;&nbsp;
              <svg height="18" width="18"> 
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="green" /> <!-- Sampai sini -->
              </svg> 30 days : <?php $obj = json_decode($invdx2,true);echo $obj ?>  <br>
              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="black" /> <!-- Sampai sini -->
              </svg> 90 days : <?php $obj = json_decode($invdx3,true);echo $obj ?>  &nbsp;&nbsp;
              <svg height="18" width="18">
                <circle cx="4" cy="4" r="4" stroke="" stroke-width="" fill="orange" /> <!-- Sampai sini -->
              </svg> 180 days : <?php $obj = json_decode($invdx4,true);echo $obj ?> <br>
            </center>  
            </div>
            
          </div>
        
       </div>
		<div class="col-xl-6  col-lg-6 col-md-12 mb-4">
       <div>
			<p><center>
			  
                Almost Safety Stock : <?php $obj = json_decode($sft2,true);echo $obj ?> Items <br>
					  Below Safety Stock : <?php $obj = json_decode($sft1,true);echo $obj ?> Items
					</center> </p>
			  <div class="card-body">
				<div class="no-gutters align-items-center">
				  <div class="col mr-2 text-center ">
					 <p class="card-text">
					 
				  </div>
				  <div>
					<div class="chart-pie pt-4 pb-2"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div></div></div><div class="chartjs-size-monitor-shrink"><div></div></div></div>
					  <canvas width="274" height="200" class="chartjs-render-monitor" id="mySafetyStock" style="width: 183px; height: px; display: block;"></canvas>
					</div>
					<div class="mt-4 small text-center">
					  <span class="mr-2">
						<i class="fas fa-circle" style="color:black"></i><b> Almost at Safety Stock</b>
					  </span>
					  <span class="mr-2">
						<i class="fas fa-circle" style="color:red"></i><b> Below Safety Stock</b>
					  </span>
					</div>
				  </div>
				</div>
			  </div>
			
		</div>	
      </div>
    </div>

    <div>
      <a href="/dashpurdet" class="empat"><center>Purchased items with no activity</center></a>
  
  
  <div class="row">


    <div class="col-lg-3 offset-lg-0">
      <div class="card border shadow">
      
        <div class=" satu py-3  align-items-center justify-content-between">
       
          <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
         <center>For 30 days</center>
        </div>
      
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr1 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class=" flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamt1->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

    <div class="col-lg-3">
      <div class="card border shadow">
     <div class=" satu py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 90 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr2 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="   flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamt2->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

<div class="col-lg-3">
     <div class="card border shadow">
     <div class=" satu py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 180 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="   flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr3 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="   flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamt3->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

<div class="col-lg-3">
      <div class="card border shadow">
     <div class=" satu py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 365 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr4 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamt4->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

                   

  </div> <!-- End of Row -->
</div> <!-- End of Container -->
</br>

    <div>
       <a href="/dashmandet" class="empat"><center>Manufactured items with no activity</center></a>
  <div class="row">
    <div class="col-lg-3">
      <div class="card border shadow">
      
        <div class=" dua py-3 flex-row align-items-center justify-content-between">
       
          <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
         <center>For 30 days</center>
        </div>
      
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr1 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamtx1->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

    <div class="col-lg-3">
     <div class="card border shadow">
     <div class="dua py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 90 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr2 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamtx2->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

<div class="col-lg-3">
     <div class="card border shadow">
     <div class="dua py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 180 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="   flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr3 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamtx3->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

<div class="col-lg-3">
    <div class="card border shadow">
     <div class=" dua py-3  flex-row align-items-center justify-content-between">
          <!-- <h6 class="font-weight-bold">Past Due Purchase Order</h6> -->
           <center>For 365 days</center>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ $invbr4 }}</i></center>
                  </div>
               </div>
            </div>
        </div>
        <div class="card-body">
          <div class="">
            <div>
              <div class=""><div class=""><div class=""><div></div></div><div class=""><div></div></div></div>
                </div>
                  <div class="  flex-row align-items-center justify-content-between">
                    <center><i>{{ number_format($invamtx4->total,2 ) }}</i></center>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div> <!-- end of class="col-lg-3 -->

                   

  </div> <!-- End of Row -->
</div> <!-- End of Container -->

<!--
	<table rules="none"  width="100%"   >
                  <thead>
                    <tr>
                      <th height="40px" colspan="6" class="satu"><a href="/dashpurdet" class="satu"><center> Purchased items with no activity </center></a></th>                                                               
                    </tr>
                  </thead>    
                 <tr><td></td>
                    <tr>
                      <th></th>
                      <th class="tiga"><center>For 30 days </center></th>
                      <th class="tiga"><center>For 90 days </center></th>
                      <th class="tiga"><center>For 180 days </center></th>
                      <th class="tiga"><center>For 365 days </center></th>
                                          
                    </tr>
                     
                  <tbody>   
                     <tr><td></td>
                    <tr>
                      <td class="dua"><center><i>Qty</i></center></td> 
                      <td class="dua"><center><i>{{ $invbr1 }}</i></center></td> 
                      <td class="dua"><center><i>{{ $invbr2 }}</i></center></td> 
                      <td class="dua"><center><i>{{ $invbr3 }}</i></center></td>
                      <td class="dua"><center><i>{{ $invbr4 }}</i></center></td>
                                        
                    </tr>
					<tr>   
                      <td class="empat"><center><i>Amount</i></center></td>
                      <td class="empat"><center>{{ number_format($invamt1->total,2 ) }}</center></td> 
                      <td class="empat"><center>{{ number_format($invamt2->total,2 ) }}</center></td> 
                      <td class="empat"><center>{{ number_format($invamt3->total,2 ) }}</center></td>
                      <td class="empat"><center>{{ number_format($invamt4->total,2 ) }}</center></td>
                                       
                    </tr>
                   
                  
                   </tbody>                  
                  
                 </table>
                 </br>
                 
                 <table rules="none"  id="dataTable" width="100%"  >
                  <thead>
                    <tr>
                      <th height="40px" colspan="6" class="manu" ><a href="/dashmandet" class="manu"><center> Manufactured items with no activity</a> </center></th>                                                               
                    </tr>
                  </thead>    
                 
                    <tr>
                     <th></th>
                      <th class="tiga"><center>For 30 days </center></th>
                      <th class="tiga"><center>For 90 days </center></th>
                      <th class="tiga"><center>For 180 days </center></th>
                      <th class="tiga"><center>For 365 days </center></th>                                          
                    </tr>
                     
                  <tbody>                                    
                    <tr>
                      <td class="dua"><center><i>Qty</i></center></td> 
                      <td class="dua"><center>{{ $invbrm1 }}</center></td> 
                      <td class="dua"><center>{{ $invbrm2 }}</center></td> 
                      <td class="dua"><center>{{ $invbrm3 }}</center></td>
                      <td class="dua"><center>{{ $invbrm4 }}</center></td>                                      
                    </tr>
					<tr>
                      <td class="empat"><center><i>Amount</i></center></td>
                      <td class="empat"><center>{{ number_format($invamtx1->total,2 ) }}</center></td> 
                      <td class="empat"><center>{{ number_format($invamtx2->total,2 ) }}</center></td> 
                      <td class="empat"><center>{{ number_format($invamtx3->total,2 ) }}</center></td>
                      <td class="empat"><center>{{ number_format($invamtx4->total,2 ) }}</center></td>                                        
                    </tr>
                  
                   </tbody>                  
                  
                 </table>
-->
@endsection

@section('scripts')
<script>
    function noexpitm(event, array){
        if(array[0]){
            let element = this.getElementAtEvent(event);
            if (element.length > 0) {
                //var series= element[0]._model.datasetLabel;
                //var label = element[0]._model.label;
                //var value = this.data.datasets[element[0]._datasetIndex].data[element[0]._index];
                window.location = "/expitem";

                //console.log()
            }
        }
    }
    
    function belowStockClickEvent(event, array){
        if(array[0]){
            let element = this.getElementAtEvent(event);
            if (element.length > 0) {
                //var series= element[0]._model.datasetLabel;
                //var label = element[0]._model.label;
                //var value = this.data.datasets[element[0]._datasetIndex].data[element[0]._index];
                window.location = "/bstock";
                //console.log()
            }
        }
    }
</script>
<script>
  var ctx = document.getElementById("mySafetyStock");
    var myExpItem = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["1 Month", "2 Months"],
        datasets: [{
          data: [<?php $obj = json_decode($sft1,true);echo $obj ?>,<?php $obj = json_decode($sft2,true);echo $obj ?>],
          backgroundColor: ['black', 'red'],
          hoverBackgroundColor: ['#2e59d9', '#17a673'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
         enable3D: true,
        onClick: belowStockClickEvent,
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        cutoutPercentage: 60,
      },
    });
    
  var ctx = document.getElementById("myexpitm");
    var myMachineDown = new Chart(ctx, {
      type: 'horizontalBar',
	  	  
      data: {	  
        labels: ["0 days", "30 days", "90 days", "180 days"],       
		    datasets: [{
          label: "Total",
          backgroundColor: ["red","green","black","orange"],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
		  
          data: [
           1,2,3,4],
          }],
      },
       options: {
         enable3D: true,
        onClick: noexpitm,
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        scales: {
            xAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true   // minimum value will be 0.
                }
            }]
        },
        cutoutPercentage: 60,
      },
    });
</script>
@endsection
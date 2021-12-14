@extends('layout.layout')
@section('menu_name','Purchase Item ')
@section('content')
	<!-- Page Heading -->

	<div class="row">
		<div class="col-xl-6 col-lg-6 col-md-10">       	
        <div class="card ml-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="card-title mb-2" style="text-align:center;">Dashboard</h4>
                    </div>
                    <!--/.col-->
                </div>
                <!--/.row-->
                <div class="chart-wrapper mt-4 mr-3 ml-3">
                    <div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                        <div style="position:absolute;width:500px;height:500px;left:0;top:0"></div>
                    </div>
                    <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                        <div style="position:absolute;width:200%;height:200%;left:0; top:0">
                        </div>
                    </div>
                </div>
                    <canvas id="myexpitm" style="display: block; height: 160px; width:555px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
		</div>

      
	   
		<div class="col-xl-6 col-lg-6 col-md-10">
      
			<table  rules="none" width="100%" border ="0" class="table table-bordered table-sm">  
        <thead>
          <tr>
            <th><center><a href="{{url('/dashpurdet1')}}" class="linkhead">For 30 days </a></center></th>
            <th><center><a href="{{url('/dashpurdet2')}}" class="linkhead">For 90 days </a></center></th>
            <th><center><a href="{{url('/dashpurdet3')}}" class="linkhead">For 180 days </a></center></th>
            <th><center><a href="{{url('/dashpurdet4')}}" class="linkhead" >For 365 days </a></center></th>
          </tr>
        </thead>   
			
			  <tr>
          <td class="dua"><center>{{ $invbr1 }}</center></td> 
          <td class="dua"><center>{{ $invbr2 }}</center></td> 
          <td class="dua"><center>{{ $invbr3 }}</center></td>
          <td class="dua"><center>{{ $invbr4 }}</center></td> 
        </tr>						
			  <tr>
          <td class="empat"><center>{{ number_format($invamt1->total,2 ) }}</center></td> 
          <td class="empat"><center>{{ number_format($invamt2->total,2 ) }}</center></td> 
          <td class="empat"><center>{{ number_format($invamt3->total,2 ) }}</center></td>
          <td class="empat"><center>{{ number_format($invamt4->total,2 ) }}</center></td>
        </tr>
			</table>
		</div>
	</div>
                       
  <div class="card-body mb-3 pt-0">
      <div class="table-responsive">   
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="1">
              <thead>
              <tr>
        <th>Item Number</th>                      
        <th>Description</th>
        <th>um</th>
                  <th>Qty On Hand</th>
                  <th>Last Transaction</th>
                  <th>days Without Transaction</th>   
                  <th>Value</th>                 
              </tr>
              </thead>                                     
                
              <tbody>                  
                  @foreach($invbr as $show)
                  <tr>
                      <td>{{ $show->xtrhist_part }}</td> 
                      <td>{{ $show->xtrhist_desc }}</td> 
                      <td>{{ $show->xtrhist_um }}</td>
                      <td>{{ $show->xtrhist_qty_oh  }}</td>
                      <td>{{ $show->xtrhist_last_date }}</td>
                      <td>{{ $show->xtrhist_days }}</td>
                      <td>{{ $show->xtrhist_amt }}</td>
                  </tr>
                  @endforeach  
              </tbody>
          </table>
              {{ $invbr->render() }}
            
      </div>
  </div>

@endsection


<!--Chart-->
@section('scripts')
<script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>
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
  
    var ctx = document.getElementById("myexpitm");
    var myMachineDown = new Chart(ctx, {
      type: 'bar',
        
      data: {	  
        labels: ["30D", "90D", "180D","365D"],        
      datasets: [{
          label: "Total",
          backgroundColor: '#90C4FF',
          hoverBackgroundColor: "#20aaf0",
          pointHoverBackgroundColor: '#fff',
          borderWidth: 2,
      
          data: [
      <?php $obj = json_decode($invbr1,true);echo $obj ?>
      ,<?php $obj = json_decode($invbr2,true);echo $obj ?>
      ,<?php $obj = json_decode($invbr3,true);echo $obj ?>		   
      ,<?php $obj = json_decode($invbr4,true);echo $obj ?>],
        }],
      },
      options: {
    
        maintainAspectRatio: false,
        layout: {
        backgroundColor: "#90C4FF",
          padding: {
            left: 10,
            right: 55,
            top: 0,
            bottom: 0
          }
        },
        scales: {
          xAxes: [{
            time: {
              unit: 'month'
            },
            gridLines: {
              display: false,
              drawBorder: false
            },
            ticks: {
              maxTicksLimit: 6,
        fontSize : 20,
        fontColor :"black",
            },
            maxBarThickness: 55,
          }],
          yAxes: [{
            ticks: {
              min: 0,              
              maxTicksLimit: 7,
              padding: 10,
        fontSize : 18,
        fontColor :"black",
            },
            gridLines: {
              color: "#90C4FF",
              zeroLineColor: "#90C4FF",
              drawBorder: false,
              borderDash: [7],
              zeroLineBorderDash: [2]
            }
          }],
        },
        legend: {
          display: false
        },
    
      }
    });
</script>
	
@endsection
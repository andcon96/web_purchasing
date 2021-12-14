@extends('layout.layout')
@section('menu_name','Purchase Order Detail')
@section('content')

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

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:18px;
        font-weight: 600;
        padding: 5px 10px 5px 10px;
    }


    .bt-action{
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
  
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }


    @media only screen and (max-width: 800px) {
        
    /* Force table to not be like tables anymore */
    #dataTable table, 
    #dataTable thead, 
    #dataTable tbody, 
    #dataTable th, 
    #dataTable td, 
    #dataTable tr { 
        display: block; 
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    #dataTable thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    #dataTable tr { border: 1px solid #ccc; }

    #dataTable td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 40%; 
        white-space: normal;
        text-align:left;
    }

    #dataTable td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align:left;
        font-weight: bold;
    }

    /*
    Label the data
    */
    #dataTable td:before { 
        content: attr(data-title); 
        vertical-align: top;
        padding: 6px 0px 0px 0px;
    }
}   
</style>

	
	
@if(session()->has('updated'))
<div class="alert alert-success">
    {{ session()->get('updated') }}
</div>
@endif
			 

</br>


<div class="table-responsive col-lg-12 col-md-12 tag-container">
<h3>Header</h3>
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>NO. PO</th>
         <th>Supplier</th>
         <th>Order Date</th>
         <th>Due Date</th>  
         <th>Currency</th>
         <th>Status</th>                    
      </tr>
   </thead>
    <tbody>         
        @foreach($po as $show)
        <tr>
            <td style="width:10%">{{ $show->xpo_nbr }}</td>
            <td style="width:10%">{{ $show->xpo_vend }}</td>
            <td style="width:10%">{{ $show->xpo_ord_date }}</td>
            <td style="width:15%">{{ $show->xpo_due_date }}</td>
            <td style="width:10%">{{ $show->xpo_curr }}</td>
            <td style="width:15%">{{ $show->xpo_status }}</td>           			 
        </tr>
        @endforeach  
    </tbody>
  </table>
</div>
</br>
<div class="table-responsive col-lg-12 col-md-12 tag-container">
   <h3>Detail</h3>	  
   <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
      <thead>
         <tr>
            <th>Order</th>
            <th>Item Number</th>
            <th>Description</th>
            <th>Line</th>
            <th>Due Date</th>
            <th>Qty Order</th>
            <th>Price</th>       
         </tr>
      </thead>                                                    
      <tbody>                    
         @foreach($poddet as $p)
         <tr>
            <td>{{ $p->xpod_nbr }}</td> 
            <td>{{ $p->xpod_part }}</td>                       
            <td>{{ $p->xpod_desc}}</td>
            <td>{{ $p->xpod_line }}</td>
            <td>{{ $p->xpod_due_date}}</td>
            <td>{{ number_format($p->xpod_qty_ord,2) }}</td>
            <td>{{ number_format($p->xpod_price,2 )}}</td>
                              
         </tr>
         @endforeach  
      </tbody> 
   </table>    
</div>
<div class="row">
    <div class="col-lg-2 offset-lg-2">
      <div class="card border shadow">
             
          <form action="/podsave" method="post">
                  {{ csrf_field() }}                                                         
               <input disable type="hidden" name="nbr" value= {{ $car }} >                         
               <input disable type="hidden" name="conf" value= "Confirmed" >                                                    
			   <input type="hidden" name="supp" value= {{ Auth::user()->supp_id }} >
					    		<input type="submit" class="col-lg-12" value="Confirmed" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
         </div>
      </div>
      <div class="col-lg-2">
         <div class="card border shadow">
           <form action="/podupd" method="post">
                  {{ csrf_field() }}                                                         
               <input disable type="hidden" name="nbr" value= {{ $car }} >                         
               <input disable type="hidden" name="conf" value= "UnConfirm" >                                                    
			         <input type="hidden" name="supp" value= {{ Auth::user()->supp_id }} >
					     <input type="submit" class="col-lg-12" value="UnConfirm" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
 
         
     </div>
   </div>
   <div class="col-lg-2">
         <div class="card border shadow">
           <form action="/podmail" method="get">
                  {{ csrf_field() }}                                                         
               <input disable type="hidden" name="nbr" value= {{ $car }} >                         
               <input disable type="hidden" name="conf" value= "Confirmed" >                                                    
 
					    		<input type="submit" class="col-lg-12" value="Email" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
 
         
     </div>
   </div>
   <div class="col-lg-2">
         <div class="card border shadow">
           <form action="/poconf" method="get">
                  {{ csrf_field() }}                                                         
               <input disable type="hidden" name="nbr" value= {{ $car }} >                         
               <input disable type="hidden" name="conf" value= "Confirmed" >                                                    
 
					    		<input type="submit" class="col-lg-12" value="Back" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
 
         
     </div>
   </div>
</div>
                      
@endsection
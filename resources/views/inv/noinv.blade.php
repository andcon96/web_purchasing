@extends('layout.layout')

@section('content')

<table rules="none"  width="100%" class="table table-bordered mb-3" >
  <thead>
    <tr>
      <th height="40px" colspan="6">
        <a href="/dashpurdet" class="linkhead"><center> Purchased items with no activity </center></a>
      </th>                                                               
    </tr>
  </thead>
  <tr>    
    <th></th>
    <th class="tiga"><center>IN 30 DAYS </center></th>
    <th class="tiga"><center>IN 90 DAYS </center></th>
    <th class="tiga"><center>IN 180 DAYS </center></th>
    <th class="tiga"><center>IN 365 DAYS </center></th>                                    
  </tr>

  <tbody>   
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

<table rules="none" width="100%"  class="table table-bordered mb-3">
  <thead>
    <tr>
      <th height="40px" colspan="6">
        <a href="/dashmandet" class="linkhead"><center> Manufactured items with no activity </center></a>
      </th>                                                               
    </tr>
  </thead>    
  <tr>
    <th></th>
    <th class="tiga"><center>IN 30 DAYS </center></th>
    <th class="tiga"><center>IN 90 DAYS </center></th>
    <th class="tiga"><center>IN 180 DAYS </center></th>
    <th class="tiga"><center>IN 365 DAYS </center></th>                  
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
      <td><center><i>Amount</i></center></td>
      <td><center>{{ number_format($invamtx1->total,2 ) }}</center></td> 
      <td><center>{{ number_format($invamtx2->total,2 ) }}</center></td> 
      <td><center>{{ number_format($invamtx3->total,2 ) }}</center></td>
      <td><center>{{ number_format($invamtx4->total,2 ) }}</center></td>             
    </tr>
  </tbody>                  

</table>

@endsection

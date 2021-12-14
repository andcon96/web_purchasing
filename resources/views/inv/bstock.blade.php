@extends('layout.layout')

@section('menu_name','Safety Stock')

@section('content')
	
	<!-- Page Heading -->
	@if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
          {{ session()->get('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
  @endif

  @if(session()->has('updated'))
      <div class="alert alert-success  alert-dismissible fade show"  role="alert">
          {{ session()->get('updated') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
  @endif

    <table width="100%">
      <tr>
        <th class="dua">Below Safety Stock :</th>
      </tr>
    </table>

    <table width="100%" hight="50%" class="table table-bordered mb-5 table-sm">
        <thead>	
          <tr>
            <th>Item Number</th>
            <th>Description</th>
                <th>UM</th>
            <th>Qty On Hand</th>				
            <th>Safety Stock</th>
            <th>Qty Required</th>
          </tr>
        </thead>
        <tbody>                    
            @forelse($bstock as $p)                   
            <tr>
                <td>{{ $p->xinv_part }}</td> 
            <td>{{ $p->xitem_desc }}</td> 
            <td>{{ $p->xitem_um }}</td> 
            <td>{{ $p->xinv_qty_oh }}</td> 
                <td>{{ $p->xinv_sft_stock }}</td>
            <td>{{ $p->xinv_qty_req }}</td> 				
          </tr>
          @empty
          <td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
          @endforelse 
        </tbody>
    </table>
    {{ $bstock->render() }}

    <table width="100%">
      <tr>
      <th class="dua">Almost Safety Stock :</th>
      
      </tr>
    </table>
   
    <table width="100%" hight="50%" class="table table-bordered table-sm">
        <thead>	
          <tr>
            <th>Item Number</th>
            <th>Description</th>
                <th>UM</th>
            <th>Qty On Hand</th>				
            <th>Safety Stock</th>
            <th>Qty Required</th>
          </tr>
        </thead>
        <tbody>                    
          @forelse($bstock1 as $p)                   
          <tr>
              <td>{{ $p->xinv_part }}</td> 
          <td>{{ $p->xitem_desc }}</td> 
          <td>{{ $p->xitem_um }}</td> 
          <td>{{ $p->xinv_qty_oh }}</td> 
              <td>{{ $p->xinv_sft_stock }}</td>
          <td>{{ $p->xinv_qty_req }}</td> 				
        </tr>
        @empty
        <td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
        @endforelse  
      </tbody>
    </table>
{{ $bstock1->render() }}



@endsection
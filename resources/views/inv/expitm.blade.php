@extends('layout.layout')

@section('menu_name','Inventory')

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
		<th class="dua">Expired Inventory as of today</th>
		<th class="dua">Total Item: {{ $invdx1 }} </th>
		
	  </tr>
	</table>

    <table width="100%" hight="50%" class="table table-bordered table-sm mb-3">
        <thead>	
			<tr>
				<th>Item Number</th>
				<th>Description</th>
				<th>Qty On Hand</th>
				<th>Location</th>
				<th>Lot</th>
				<th>Reff</th>
				<th>Expired Date</th>
				<th>Days Expired</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tbody>                    
            @forelse($invd1 as $p)                   
            <tr>
                <td>{{ $p->xinvd_part }}</td> 
				<td>{{ $p->xitem_desc }}</td> 
				<td>{{ $p->xinvd_qty_oh }}</td> 
				<td>{{ $p->xinvd_loc }}</td> 
				<td>{{ $p->xinvd_lot }}</td> 
				<td>{{ $p->xinvd_ref }}</td>
				<td>{{ $p->xinvd_expire }}</td>
				<td>{{ $p->xinvd_days }}</td>
				<td>{{ $p->xinvd_amt }}</td>
			</tr>
			@empty
            	<td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
			@endforelse  
		</tbody>
    </table>
	{{ $invd1->render() }}

	<table width="100%">
	  <tr>
		<th class="dua">Expired in 30 Days</th>
		<th class="dua">Total Item: {{ $invdx2 }} </th>		
	  </tr>
	</table>
 
	<table width="100%" hight="50%" class="table table-bordered table-sm mb-3">  
        <thead>	
			<tr>
				<th>Item Number</th>
				<th>Description</th>
				<th>Qty On Hand</th>
				<th>Location</th>
				<th>Lot</th>
				<th>Reff</th>
				<th>Expired Date</th>
				<th>Days Expired</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tbody>                    
            @forelse($invd2 as $p)                   
            <tr>
                <td>{{ $p->xinvd_part }}</td> 
				<td>{{ $p->xitem_desc }}</td> 
				<td>{{ $p->xinvd_qty_oh }}</td> 
				<td>{{ $p->xinvd_loc }}</td> 
				<td>{{ $p->xinvd_lot }}</td> 
				<td>{{ $p->xinvd_ref }}</td>
				<td>{{ $p->xinvd_expire }}</td>
				<td>{{ $p->xinvd_days }}</td>
				<td>{{ $p->xinvd_amt }}</td>
			</tr>
			@empty
            	<td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
			@endforelse  
		</tbody>
    </table>
	{{ $invd2->render() }}
	

	<table width="100%">
	  <tr>
		<th class="dua">Expired in 90 Days</th>
		<th class="dua">Total Item: {{ $invdx3 }} </th>		
	  </tr>
	</table>

	<table width="100%" hight="50%" class="table table-bordered table-sm mb-3">  
        <thead>	
			<tr>
				<th>Item Number</th>
				<th>Description</th>
				<th>Qty On Hand</th>
				<th>Location</th>
				<th>Lot</th>
				<th>Reff</th>
				<th>Expired Date</th>
				<th>Days Expired</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tbody>                    
            @forelse($invd3 as $p)                   
            <tr>
                <td>{{ $p->xinvd_part }}</td> 
				<td>{{ $p->xitem_desc }}</td> 
				<td>{{ $p->xinvd_qty_oh }}</td> 
				<td>{{ $p->xinvd_loc }}</td> 
				<td>{{ $p->xinvd_lot }}</td> 
				<td>{{ $p->xinvd_ref }}</td>
				<td>{{ $p->xinvd_expire }}</td>
				<td>{{ $p->xinvd_days }}</td>
				<td>{{ $p->xinvd_amt }}</td>
			</tr>
			@empty
            	<td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
			@endforelse  
		</tbody>
    </table>
	{{ $invd3->render() }}
	

	<table width="100%">
	  <tr>
		<th class="dua">Expired in 180 Days</th>
		<th class="dua">Total Item: {{ $invdx4 }} </th>		
	  </tr>
	</table>

	<table width="100%" hight="50%" class="table table-bordered table-sm mb-3">  
        <thead>	
			<tr>
				<th>Item Number</th>
				<th>Description</th>
				<th>Qty On Hand</th>
				<th>Location</th>
				<th>Lot</th>
				<th>Reff</th>
				<th>Expired Date</th>
				<th>Days Expired</th>
				<th>Cost</th>
			</tr>
		</thead>
		<tbody>                    
            @forelse($invd4 as $p)                   
            <tr>
                <td>{{ $p->xinvd_part }}</td> 
				<td>{{ $p->xitem_desc }}</td> 
				<td>{{ $p->xinvd_qty_oh }}</td> 
				<td>{{ $p->xinvd_loc }}</td> 
				<td>{{ $p->xinvd_lot }}</td> 
				<td>{{ $p->xinvd_ref }}</td>
				<td>{{ $p->xinvd_expire }}</td>
				<td>{{ $p->xinvd_days }}</td>
				<td>{{ $p->xinvd_amt }}</td>
			</tr>
			@empty
            	<td colspan='12' class='text-danger'><b> <center>No Data Available</center> </b></td>
			@endforelse  
		</tbody>
    </table>
	{{ $invd4->render() }}

@endsection
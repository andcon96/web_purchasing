<!DOCTYPE html>
<html>
<head>
	<title>Return DNP</title>
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
</head>
<body>
	<style type="text/css">
		/* table tr td,
		table tr th{
			font-size: 10pt;
		} */
		@page { margin: 120px 50px 50px 50px; }
	    #header { 
	    	position: fixed; 
	    	left: 0px; 
	    	top: -125px; 
	    	right: 0px;  
	    	text-align: center; 
	    }
	    .pindah{
	    	/*page-break-after: always;*/ 
			display: block; 
			page-break-before: always;
	    }

		table.minimalistBlack {
            border: 3px solid #000000;
            width: 100%;
            border-collapse: collapse;
        }
        table.minimalistBlack td, table.minimalistBlack th {
            border: 1px solid #000000;
            padding: 5px 4px;
			vertical-align: top;
        }
        table.minimalistBlack tbody td {
            font-size: 14px;
        }
        table.minimalistBlack thead {
            background: #CFCFCF;
            background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
            border-bottom: 3px solid #000000;
        }
        table.minimalistBlack thead th {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            text-align: center;
        }
        table.minimalistBlack tfoot td {
            font-size: 14px;
        }
	</style>

    <!--Header-->
    <div id="header" style="text-align:center;margin-bottom:0px;">
    </div>

	<div id="detail" style="margin-top:0px;padding-top:0px;">
		<p style="text-align:right;margin-top:0px;margin-right:20px;">Hal : 1</p>

		<table style="width:100%;" class="">
			<tr>
				<td width="55%" style="vertical-align:top;">
						Kepada Yth.
				</td>
				<td width="45%">	
						<table width="100%">
						<tr>
							<td width="40%" style="height:30px;vertical-align:top;">No. Retur</td>
							<td style="height:30px;vertical-align:top;"> 123</td>
						</tr>
						<tr>
							<td width="40%" style="height:30px;vertical-align:top;">Tgl. Retur</td>
							<td style="height:30px;vertical-align:top;"> 123</td>
						</tr>
						<tr>
							<td width="40%" style="height:30px;vertical-align:top;">Nama Ekspedisi</td>
							<td style="height:30px;vertical-align:top;"> Hello</td>
						</tr>
						</table>
				</td>
			</tr>
		</table>
		<!--Isi Table-->
		<table style="width:100%;" class="">
			<tr>
				<th width="5%"> </th>
				<th width="18%"></th>
				<th width="22%"></th>
				<th width="55%"></th>
			</tr>
			@php($flg = 0)
			@php($hal = 1)
			@foreach($data as $data)
				@php($flg += 1)
				<tr>
					<td style="text-align:right">{{$loop->iteration}}</td>
					<td style="text-align:center">10 Pcs</td>
					<td>{{$data->first_name}}</td>
					<td>{{$data->email}}</td>
				</tr>

				@if($flg == 30)
					@php($flg = 0)
					@php($hal += 1)
					<tr class="pindah"></tr>
					<tr>
						<td style="height:100px;" colspan='4'>
							<p style="text-align:right;margin-top:0px;margin-right:20px;">Hal : {{$hal}}</p>
							<table style="width:100%;">
								<tr>
									<td width="55%" style="vertical-align:top;">
											Kepada Yth.
									</td>
									<td width="45%">	
											<table width="100%">
											<tr>
												<td width="40%" style="height:30px;vertical-align:top;">No. Retur</td>
												<td style="height:30px;vertical-align:top;"> asdfa</td>
											</tr>
											<tr>
												<td width="40%" style="height:30px;vertical-align:top;">Tgl. Retur</td>
												<td style="height:30px;vertical-align:top;"> asdfa</td>
											</tr>
											<tr>
												<td width="40%" style="height:30px;vertical-align:top;">Nama Ekspedisi</td>
												<td style="height:30px;vertical-align:top;"> asdfa</td>
											</tr>
											</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				@endif
			@endforeach

		</table>
	</div>
	
	
	
</body>
</html>
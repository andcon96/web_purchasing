<!DOCTYPE html>
<html>
<head>
<img src="img/logo.png" align="right" width="100px">
	<title>Membuat Laporan PDF Dengan DOMPDF Laravel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>PURCHASE ORDER PRINT</h4>
		</br>
		</br>
		</br>
	</center>
   
    @foreach($popdf as $show)
	</br>
		</br>
		</br>
    <table border="0">
      <tr>
      <td>Order Number : </td>
      <td>{{ $show->xpo_nbr }}</td>
      </tr>
	  <tr>
      <td>Order Date : </td>
      <td>{{ $show->xpo_ord_date }}</td>
      </tr>
	  <tr>
	   <td>Currency : </td>
         <td>{{ $show->xpo_curr }}</td>
	  </tr>
      <tr>
      <td>Supplier : </td>
      <td>{{ $show->xpo_vend }}</td>       
      </tr>
	  <tr>
      <td> </td>
      <td></td>       
      </tr>
	  <tr>
      <td> </td>
      <td></td>       
      </tr>
    </table>


    @endforeach 
	<table>
	</tr>
	  <tr>
      <td> </td>
      <td></td>       
      </tr>
	  <tr>
      <td> </td>
      <td></td>       
      </tr>
	    <tr>
      <td> </td>
      <td></td>       
      </tr>
	  <tr>
      <td> </td>
      <td></td>       
      </tr>
    </table>
	

	<table class='table table-bordered'>
		<thead>
			<tr>
            <th>Line</th>
			<th>Item Number</th>
            <th>Description</th>
			<th>UM</th>
			<th>Due Date</th>
            <th>Qty Order</th>           
            <th>Price</th>

			</tr>
		</thead>
       <tbody>                  
                   @foreach($podpdf as $show)
                    <tr>                      
                      <td>{{ $show->xpod_line }}</td>
                      <td>{{ $show->xpod_part }}</td>
                      <td>{{ $show->xpod_desc }}</td>
					  <td>{{ $show->xpod_um }}</td>
					  <td>{{ $show->xpod_due_date }}</td>
                      <td>{{ $show->xpod_qty_ord }}</td>                     
                      <td>{{ $show->xpod_price }} </td>                      
                    </tr>
                   @endforeach  
                   </tbody>
         

	</table>
   
 
</body>
</html>
<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th></th>
         <th>No.</th>
         <th>RFP/RFQ No.</th>
         <th>Supplier Code</th>
         <th>Supplier Name</th>  
         <th>Due Date</th>
         <th>Propose Date</th>
      </tr>
   </thead>
    <tbody>   
        @foreach ($data as $show)
        <tr>
        	<td>
        		<input type="checkbox" name="data[]" 
        					value="{{$show->rf_number}}">  
        	</td>
        	<td>{{ $loop->iteration }}</td>
            <td>{{ $show->rf_number }}</td> 
            <td>{{ $show->supp_code }}</td> 
            <td>{{ $show->xalert_nama }}</td> 
            <td>{{ $show->due_date }}</td>
            
            @if($show->propose_date == "")
            <td>-</td>
            @else
            <td>{{ $show->propose_date }}</td>
            @endif
        </tr>
        @endforeach     
    </tbody>
  </table>
</div>
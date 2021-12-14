<div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
         <th>PO No.</th>
         <th>Amount</th>
         <th>Create Date</th>
         <th>Due Date</th>
         <th>Next Approver</th>  
         <th>Status</th>
         <th>Action</th>
         <th>History</th>
      </tr>
   </thead>
    <tbody>         
        @forelse ($users as $show)
        <tr>
            <td style="width:15%">{{ $show->xpo_nbr }}</td>
            <td style="width:15%">{{ number_format($show->xpo_total,2 )}}</td>
            <td style="width:20%">{{ $show->xpo_crt_date }}</td>
            <td style="width:20%">{{ $show->xpo_due_date }}</td>
            <td style="width:20%">{{ $show->name }}</td>
            <td style="width:15%">
                @if($show->xpo_app_status == 0)
                    On Process
                @elseif($show->xpo_app_status == 1)
                    Approved
                @else
                    Reject
                @endif
            </td>
            <td style="width:8%">
              <!--
            	<a href="" class='updatedata' data-toggle='modal' data-target='#editModal' 
            	data-nbr='{{$show->xpo_nbr}}'  data-supplier='{{$show->xpo_vend}}' 
              data-created='{{$show->xpo_crt_date}}' data-due='{{$show->xpo_due_date}}'
              data-approver='{{$show->xpo_app_approver}}' data-altapprover='{{$show->xpo_app_alt_approver}}'
              data-apporder='{{$show->xpo_app_order}}'
               >
              -->
              <a href="{{url('/detailpoapp/'.$show->xpo_nbr.'')}}">
              @if($show->xpo_app_approver == Session::get('userid')) 
                <i class="fas fa-edit"></i>
              @else
                <i class="fas fa-eye"></i>
              @endif
              </a>
            </td>
            <td>
                <a href="" class='adddata' data-toggle='modal' data-target='#detailModal' 
                data-nbr='{{$show->xpo_nbr}}' 
                ><i class="fas fa-info-circle"></i></a>
            </td>
        </tr>
        @empty
            <td colspan='8' class='text-danger'><b> <center>No Data Available</center> </b></td>
        @endforelse 
      <tr>
          <td colspan="8">{!! $users->render() !!}</td>
      </tr>  
    </tbody>
  </table>
  
</div>
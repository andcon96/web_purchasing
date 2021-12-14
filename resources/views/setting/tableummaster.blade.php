<div class="table-responsive tag-container"  style="overflow-x: auto; display: block;white-space: nowrap;">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
         <th>UM Code</th>
         <th>UM Description</th>
      </tr>
       </thead>
        <tbody>         
            @forelse ($datas as $show)
              <tr>
                <td>{{ $show->um }}</td>
                <td>{{ $show->um_desc }}</td>
              </tr>
            @empty
              <tr>
                <td class="text-danger" colspan='4'>
                    <center><b>No Data Available</b></center>  
                </td>
              </tr>
            @endforelse                      
        </tbody>
      </table>
      {!! $datas->links() !!}
</div>
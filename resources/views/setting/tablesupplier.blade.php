<div class="table-responsive tag-container mt-3"  style="overflow-x: auto; display: block;white-space: nowrap;">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
         <th>Supplier</th>
         <th>Supplier Desc</th>
         <th>Active</th>
         <th>Purchasing</th>  
         <th width="10%">Edit</th>
      </tr>
       </thead>
        <tbody>         
            @foreach ($alert as $show)
              <tr>
                <td>{{ $show->xalert_supp }}</td>
                <td>{{ $show->xalert_nama }}</td>
                <td>{{ $show->xalert_active }}</td>
                <td>
                    {{ str_limit($show->xalert_not_pur,60) }}
                </td>
                <td>
                  <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->xalert_id}}" data-role="{{$show->xalert_supp}}" data-supp="{{$show->xalert_nama}}"><i class="fas fa-edit"></i></a>
                </td>
              </tr>
            @endforeach                      
        </tbody>
      </table>
      {!! $alert->render() !!}
</div>
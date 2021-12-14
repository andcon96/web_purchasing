<div class="table-responsive col-lg-12 tag-container">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
     <th width="15%">Supplier Code</th>
     <th width="45%">Supplier Name</th>  
     <th width="15%">Need Aproval</th>  
     <th width="15%">Type</th>
     <th width="10%">Edit</th>
  </tr>
   </thead>
    <tbody>         
        <tr>
            <td>------</td>
            <td>General</td>
            <td>Yes</td>
            <td>General</td>
            <td>
              @if($intv_general != '0')
                <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-idsupp="General" data-suppname="General" data-suppcode="General" data-int="{!! $intv_general->intv_rem !!}" data-req="{!! $intv_general->reapprove !!}"><i class='fas fa-edit'></i></a>
              @endif
            </td>
        </tr>
        @foreach ($users as $show)
          <tr>
            <td>{{ $show->xalert_supp }}</td>
            <td>{{ $show->xalert_nama }}</td>
            <td>{{ $show->xalert_po_app }}</td>
            <td>
              @if(is_null($show->id))
                General
              @else
                Specific
              @endif
            </td>
            <td>
              <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-idsupp="{{$show->xalert_id}}" data-suppname="{{$show->xalert_nama}}" data-int="{{$show->intv_rem}}" data-req="{{ $show->reapprove }}" data-suppcode="{{$show->xalert_supp}}"><i class='fas fa-edit'></i></a>
            </td>
          </tr>
        @endforeach                      
    </tbody>
  </table>
  {!! $users->render() !!}
</div>
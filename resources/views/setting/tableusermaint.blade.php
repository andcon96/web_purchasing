@forelse ($users as $show)
  <tr>
    <td data-title="Name">{{ $show->name }}</td>
    <td data-title="Username">{{ $show->username }}</td>
    <td data-title="Role">
      @if($show->role == 'Supplier')
          External
      @elseif($show->role == 'Purchasing')
          Internal
      @elseif($show->role == 'Admin')
          Admin
      @endif
    </td>
    <td>{{$show->role_type}}</td>
    <td>
      @if($show->active == 'yes')
        Active
      @elseif($show->active == 'no')
        Not Active
      @endif
    </td>
    <td data-title="Edit" class="action">
      <a href="" class="editUser" data-toggle="modal" data-target="#editModal" data-id="{{$show->id}}" data-uname="{{$show->username}}" data-name="{{$show->name}}"                         data-role="{{$show->role}}" data-domain="{{$show->domain}}" data-email="{{$show->email}}"
      data-suppid="{{$show->xalert_supp}}" data-suppname="{{$show->xalert_nama}}" data-roletype="{{$show->role_type}}" data-department="{{$show->department}}"
      ><i class="fas fa-edit"></i></a>
    </td>
    <td data-title="Pass" class="action">
      <a href="" class="changepass" data-id="{{$show->id}}" data-uname="{{$show->username}}" data-toggle='modal' data-target="#changepassModal"><i class="fas fa-key"></i></a>
    </td>
    <td data-title="Delete" class="action">
      @if( $show->role_type != 'AdminIMI' )
        @if( $show->active == 'no' )
      <a href="" class="deleteUser" data-id="{{$show->id}}" data-role="{{$show->username}}" data-status="activate"  data-active="1" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-check"></i></a>
        @elseif($show->active == 'yes')
      <a href="" class="deleteUser" data-id="{{$show->id}}" data-role="{{$show->username}}" data-status="deactivate"  data-active="2" data-toggle='modal' data-target="#deleteModal"><i class="fas fa-times"></i></a>
        @endif
      @else
        <a href="#" class="deleteUser" data-id="{{$show->id}}" data-role="{{$show->username}}" data-status="Active"><i class="fas fa-check" style="opacity:0.5;cursor: not-allowed;"></i></a>
      @endif
    </td>
  </tr>
@empty
<tr>
  <td class="text-danger" colspan='12'>
    <center><b>No Data Available</b></center>
  </td>
</tr>
@endforelse
<tr style="border:0 !important">
  <td colspan="12">
    {{ $users->links() }}
  </td>
</tr>             

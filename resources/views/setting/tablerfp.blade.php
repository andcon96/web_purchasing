<div class="table-responsive col-lg-12 tag-container">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="20%">Department</th>
                <th width="30%">Department Description</th>
                <th width="20%">Edit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $show)
            <tr>
                <td>{{$show->xdept}}</td>
                <td>{{$show->xdept_desc}}</td>
                <td>
                    <a href="" class="editApprover" data-toggle="modal" data-target="#editModal" data-deptid="{{$show->deptid}}" data-deptname="{{$show->xdept}}" data-desc="{{$show->xdept_desc}}"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
                <td colspan='12' class='text-danger'><b>No Data Available</b></td>
            @endforelse
        </tbody>
    </table>
</div>
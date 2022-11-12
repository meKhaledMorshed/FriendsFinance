<div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="m-0 px-1" id="subMenuHeadings">Users </h5>
    <ul class="nav nav-pills flex-column flex-md-row g-2">

        <li class="nav-item">
            <a class="btn btn-sm" id="btnUser" href=" {{ route('admin.user') }}"> <i class="bx bxs-user me-1"></i> Users</a>
        </li>




        @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'editor')
        <li class="nav-item">
            <a class="btn btn-sm" id="btnCreate" href=" {{ route('admin.user.userForm') }}">Create</a>
        </li>
        @endif


        <!-- /dropdown -->
    </ul>
</div>
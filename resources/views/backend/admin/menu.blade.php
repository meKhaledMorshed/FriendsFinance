<div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="m-0 px-1" id="menuHeadings">Admin Panel</h5>
    <ul class="nav nav-pills flex-column flex-md-row g-2">

        <li class="nav-item">
            <a class="btn btn-sm" id="users" href=" {{ route('admin.user') }}"> <i class="bx bxs-user me-1"></i> Users</a>
        </li>

        <li class="nav-item">
            <a class="btn btn-sm" id="admins" href=" {{ route('adminPanel') }}"> <i class="bx bxs-user me-1"></i> Admins</a>
        </li>

        <li class="nav-item">
            <button class="btn btn-sm" id="make-admin" onclick="adminform()">Make Admin</button>
        </li>

        <li class="nav-item">
            <button class="btn btn-sm" onclick="designationtbl()">Designation</button>
        </li>


        <!-- /dropdown -->
    </ul>
</div>
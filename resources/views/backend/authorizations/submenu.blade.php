<div class="d-flex align-items-center justify-content-between g-2 mb-1 px-2">
    <h5 class="m-0" id="subMenuHeadings">Authorizations </h5>
    <ul class="nav nav-pills flex-column flex-md-row p-1 g-2">


        @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'authorizer')
        <li class="nav-item">
            <a class="btn btn-sm" id="btnApproveUser" href="{{ route('admin.approve-user') }}">
                <i class="bx bxs-user me-1"></i> User
            </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" id="btnVerifyAddress" href="{{ route('admin.verifyUserAddress') }}"> Address </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" id="btnVerifyUserDocs" href="{{ route('admin.verifyUserDocuments') }}"> Documents</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="{{ route('admin.approve-nominee') }}"> Nominee</a>
        </li>
        @endif

        @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'accountant')
        <li class="nav-item">
            <a class="btn btn-sm" href="{{ route('admin.approve-account') }}"> Account</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="{{ route('admin.approve-transection') }}"> Transection</a>
        </li>
        @endif

        @if($admin->role == 'master' || $admin->role == 'super')
        <li class="nav-item">
            <a class="btn btn-sm" href="{{ route('admin.approve-admin') }}">
                <i class="bx bxs-user me-1"></i> Admin
            </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="{{ route('admin.reset_pass4u') }}"> Reset Password</a>
        </li>
        @endif
    </ul>
</div>
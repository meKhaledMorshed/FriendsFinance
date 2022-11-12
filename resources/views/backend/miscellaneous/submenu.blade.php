<div class="d-flex align-items-center justify-content-between g-2 mb-1 px-2">
    <h5 class="m-0">Miscellaneous </h5>
    <ul class="nav nav-pills flex-column flex-md-row p-1 g-2">


        @if($admin->role == 'master' || $admin->role == 'super')
        <li class="nav-item">
            <a class="btn btn-sm" id="btnSltOpt" href="{{ route('admin.select-option') }}"> Select Options </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" id="btnVerifyAddress" href="#"> Address </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="#"> Documents</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="#"> Nominee</a>
        </li>
        @endif

        @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'accountant')
        <li class="nav-item">
            <a class="btn btn-sm" href="#"> Account</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-sm" href="#"> Transection</a>
        </li>
        @endif
    </ul>
</div>
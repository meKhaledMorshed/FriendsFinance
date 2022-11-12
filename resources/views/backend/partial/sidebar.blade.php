<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{url('admin/dashboard')}}" class="app-brand-link">
            <!-- brand logo  -->
            <span class="app-brand-logo ">
                <img src="{{asset('assets')}}/img/logo/{{ $entity->logo }}" alt class="w-px-40 h-auto rounded-circle" />
            </span>
            <!-- brand name  -->
            <span class="fw-bolder ms-3">{{ $entity->name }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- User -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings"> Users Settings </div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.user') }}" class="menu-link">
                        <div data-i18n="Account">All Users</div>
                    </a>
                </li>

                @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'editor')
                <li class="menu-item">
                    <a href="{{ route('admin.user.create') }}" class="menu-link">
                        <div data-i18n="Account">Create New User</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        <!-- Admin Links -->
        <li class="menu-item">
            <a href="{{ route('adminPanel') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-circle"></i>
                <div data-i18n="Analytics">Admin Panel</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Account Settings"> Admin Settings </div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.user') }}" class="menu-link">
                        <div data-i18n="Account">All Admins</div>
                    </a>
                </li>

                @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'editor')
                <li class="menu-item">
                    <a href="{{ route('admin.user.create') }}" class="menu-link">
                        <div data-i18n="Account">Create New Admin</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        <!-- Authentications -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div data-i18n="Authentications">
                    Authentications
                </div>
            </a>
            <ul class="menu-sub">

                @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'authorizer')
                <li class="menu-item">
                    <a href="{{ route('admin.approve-user') }}" class="menu-link">
                        <div data-i18n="Basic">Approve User</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.verifyUserAddress') }}" class="menu-link">
                        <div data-i18n="Basic">Verify Address</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.verifyUserDocuments') }}" class="menu-link">
                        <div data-i18n="Basic">Verify Documents</div>
                    </a>
                </li>
                @endif

                @if($admin->role == 'master' || $admin->role == 'super')
                <li class="menu-item">
                    <a href="{{ route('admin.approve-admin') }}" class="menu-link">
                        <div data-i18n="Basic">Approve Admin</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.reset_pass4u') }}" class="menu-link">
                        <div data-i18n="Basic">
                            Reset Password
                        </div>
                    </a>
                </li>
                @endif

                @if($admin->role == 'master' || $admin->role == 'super' || $admin->role == 'accountant')
                <li class="menu-item">
                    <a href="{{ route('admin.approve-transection') }}" class="menu-link">
                        <div data-i18n="Basic">Approve Transection</div>
                    </a>
                </li>
                @endif




            </ul>
        </li>
        <!-- Misc -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Misc</span>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                <div data-i18n="Misc">Misc</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.select-option') }}" class="menu-link">
                        <div data-i18n="Error">Select Options</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="pages-misc-error.html" class="menu-link">
                        <div data-i18n="Error">Error</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="pages-misc-under-maintenance.html" class="menu-link">
                        <div data-i18n="Under Maintenance">
                            Under Maintenance
                        </div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">Support</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Documentation">
                    Documentation
                </div>
            </a>
        </li>
    </ul>
</aside>
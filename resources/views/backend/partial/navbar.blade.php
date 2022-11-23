<nav class="position-sticky sticky-top layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- notice box for notice generate by laravel  -->
            <div class="nav-item lh-1 me-3" id="navbar-info">
                @if(session()->has('notice'))
                <span class="alert alert-info" title="{{session()->get('notice')}}"><i class='bx bxs-bell-ring'></i> {{session()->get('notice')}}</span>
                @endif
                @if(session()->has('success'))
                <span class="alert alert-success " title="{{session()->get('success')}}"> <i class='bx bxs-user-plus'></i> {{session()->get('success')}}</span>
                @endif
                @if(session()->has('error'))
                <span class="alert alert-danger" title="{{session()->get('error')}}"> <i class='bx bxs-error'></i> {{session()->get('error')}}</span>
                @endif
            </div>
            <!-- notice box for notice generate by js  -->
            <div class="alert py-2 m-0 d-none" id="notice-bx">
                <i class='bx bxs-bell-ring'></i><span id="notice" title="Notice"></span>
            </div>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{asset('assets')}}/photos/{{ $admin->photo }}" alt class="rounded-circle" height="40" width="40" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{asset('assets')}}/photos/{{ $admin->photo }}" alt class="rounded-circle" height="40" width="40" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ $admin->name }}</span>
                                    <small class="text-muted">{{ $admin->title}}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <span class="d-flex align-items-center align-middle">
                                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                <span class="flex-grow-1 align-middle">Billing</span>
                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('logout') }}">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>

</nav>
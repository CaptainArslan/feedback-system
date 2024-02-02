    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur"
        navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                @include('components.breadcrumb')
            </nav>

            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">

                </div>
                <ul class="navbar-nav  justify-content-end">
                    <li class="nav-item d-flex align-items-center">
                        @if (session('super_admin') && !empty(session('super_admin')))
                            <a class="btn btn-outline-primary btn-sm mb-0 me-3"
                                href="{{ route('backtoadmin') }}?admin=1">Back to
                                Super Admin </a>
                        @endif

                        @if (session('company_admin') && !empty(session('company_admin')))
                            <a class="btn btn-outline-primary btn-sm mb-0 me-3"
                                href="{{ route('backtoadmin') }}?company=1">Back to
                                Agency</a>
                        @endif
                    </li>
                    <li class="nav-item d-flex align-items-center pt-3">
                        <div class="dropdown">
                            <a href="javascript:;" class="btn bg-gradient-dark dropdown-toggle "
                                data-bs-toggle="dropdown" id="navbarDropdownMenuLink2">
                                <img
                                    style ="max-height: 25px;
    max-width: 30px;"src="{{ asset(auth()->user()->image ?? '') }}" />
                                {{ auth()->user()->first_name ?? '' }}{{ auth()->user()->last_name ?? '' }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        MyProfile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">SignOut
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

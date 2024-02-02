<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>
      HexatechSolutions Portal | @yield('title')
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../uiassets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../uiassets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../uiassets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../uiassets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <!-- Nepcha Analytics (nepcha.com) -->
    <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
    <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
    {{-- <script src="../assets/plugins/global/plugins.bundle.js"></script> --}}
    <script src="../assets/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/scripts.bundle.js"></script>

    <style>
        #videoElement {
            width: 400px;
            height: 300px;
            border: 1px solid black;
        }
    </style>
    @yield('css')
</head>
@php
    $topnav=false;
@endphp
<body class="g-sidenav-show  bg-gray-100">
    @if($topnav)
        @include('components.topnav')
    @else
    @include('components.sidenav')
    @endif
   
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('components.header')

        <div class="container-fluid py-4" style="height: calc(100vh - 115px);">
            @yield('content')
        </div>
        <footer class="footer pt-3  ">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script>,
                            made with <i class="fa fa-heart"></i> by
                            <a href="{{ route('dashboard') }}" class="font-weight-bold" target="_blank">HexaTech
                                Solution</a>
                            for a better web.
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="fa fa-cog py-2"> </i>
        </a>
        <div class="card shadow-lg ">
            <div class="card-header pb-0 pt-3 ">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">HexaTech Solution </h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary active" data-color="primary"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-dark" data-color="dark"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger"
                            onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between 2 different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-transparent"
                        onclick="sidebarType(this)">Transparent</button>
                    <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white"
                        onclick="sidebarType(this)">White</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="mt-3">
                    <h6 class="mb-0">Navbar Fixed</h6>
                </div>
                <div class="form-check form-switch ps-0">
                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                        onclick="navbarFixed(this)">
                </div>
                <hr class="horizontal dark my-sm-4">
            </div>
        </div>
    </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    <!--   Core JS Files   -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../uiassets/js/core/popper.min.js"></script>
    <script src="../uiassets/js/core/bootstrap.min.js"></script>
    <script src="../uiassets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../uiassets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../uiassets/js/plugins/chartjs.min.js"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script>
        let navlink = document.querySelectorAll('.nav-link');
        const fromStorage = (action, key, value) => localStorage[action + 'Item'](key, value);
        navlink.forEach((link) => {
            const val = link.getAttribute('data-menu');
            const active = fromStorage('get', 'activeNav') || "dashboard";
            link.classList.toggle('active', active === val);
            link.addEventListener('click', () => {
                fromStorage('set', 'activeNav', val);
            })
        })
    </script>
    <script>
     const fileInput = document.querySelector('.inputimage');
        const imagePreview = document.querySelector(".previewimage");

        fileInput.addEventListener("change", function() {
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                // Handle case when no file is selected or user cancels the file dialog
                imagePreview.src = "";
            }
        });
        </script>
    @yield('js')
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../uiassets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>

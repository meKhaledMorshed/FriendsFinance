<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{asset('assets')}}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="" />
    <title> @yield('pageTitle', config('app.name')) </title>

    <!-- Favicon -->
    <!-- <link rel="shortcut icon" href="{{asset('assets')}}/img/favicon/favicon.ico" /> -->
    <!-- <link rel="shortcut icon" type="image/x-icon" href="{{ url('assets/img/favicon/favicon.ico') }}" /> -->


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{asset('assets')}}/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('assets')}}/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('assets')}}/css/demo.css" />
    <link rel="stylesheet" href="{{asset('assets')}}/css/aditional.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('assets')}}/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{asset('assets')}}/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page css will extends here  -->
    @yield('css')
    <!-- /Page css will extends here  -->

    <!-- Helpers -->
    <script src="{{asset('assets')}}/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('assets')}}/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar Menu -->
            @include('backend.partial.sidebar')
            <!-- / Sidebar Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('backend.partial.navbar')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('backend.partial.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{asset('assets')}}/vendor/libs/jquery/jquery.js"></script>
    <script src="{{asset('assets')}}/vendor/libs/popper/popper.js"></script>
    <script src="{{asset('assets')}}/vendor/js/bootstrap.js"></script>
    <script src="{{asset('assets')}}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="{{asset('assets')}}/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{asset('assets')}}/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="{{asset('assets')}}/js/main.js"></script>

    <!-- Page JS -->
    <script src="{{asset('assets')}}/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- fontawesome icone link  -->
    <script src="https://kit.fontawesome.com/b15f2f7f2e.js" crossorigin="anonymous"></script>

    <!-- aditional JS -->
    <script src="{{asset('assets')}}/js/aditional/aditional.js"></script>

    <!-- script will extends here  -->
    @yield('script')
    <!-- script will extends here  -->

    <!-- sweet alert js  -->
    <script src="{{asset('assets')}}/vendor/js/sweetalert.min.js"></script>


</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <!-- page title  -->
    <title> <?php echo session()->get('pageTitle') ?: 'Register Business'; ?> </title>

    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <h4 class="mb-2">Register Your Business</h4>
                        </div>

                        @if(session()->has('notice'))
                        <p class="small">{{ session()->get('notice') }}</p>
                        @endif
                        <form id="formAuthentication" class="mb-3" action="{{ route('createentity') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 row">
                                <label for="name" class="col-3">Name</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Entity Name" value="{{old('name')}}" autofocus required />
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="html5-email-input" class="col-md-3 col-form-label">Email</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="email" name="email" placeholder="admin@example.com" value="{{old('email')}}" id="html5-email-input" required />
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="html5-date-input" class="col-md-5 col-form-label" title="Entity established Date.">Established Date</label>
                                <div class="col-md-7">
                                    <input class="form-control" type="date" name="dob" id="html5-date-input" value="{{old('dob')}}" required />
                                    @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="logo" class="col-md-3 col-form-label">Logo</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="file" name="logo" id="logo" required />
                                    @error('logo') <span class=" small text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="address" class="col-md-3 col-form-label">Address</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="address" placeholder="Type office address here"></textarea>
                                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary d-grid w-100">Sign up</button>
                        </form>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
    <!-- / Content -->
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <!-- Page JS -->
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
@extends('backend.master')

@section('pageTitle', 'Create user | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-md-12">
            <!-- frist menu  -->
            @include('backend.user.submenu')
            <!-- /frist menu  -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="createForm" method="post" enctype="multipart/form-data">
                @csrf

                <div class="card">
                    <div class="card-header m-0 pt-2 pb-0">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <h5 class="m-0">New user creation form</h5>
                            <span class="d-none small" id="taskIndicator"> Loadning.... <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>
                            <!-- submit form  -->
                            <div class="d-flex">
                                <button type="reset" class="btn btn-warning btn-sm mx-1"> <i class='bx bx-refresh'></i> Reset </button>
                                <button type="submit" class="btn btn-success btn-sm mx-1"> <i class='bx bxs-save'></i> Save </button>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2" />
                    <!-- Update users -->
                    <div class="card-body p-2">

                        <datalist id="datalist"> </datalist>

                        <div class="row mx-2 tabView" id="personalTab">
                            <div class="col-md-8">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">Name</span>
                                            <input type="text" class="form-control" name="name" placeholder="Full Name" onblur="checkInput(this)" />
                                        </div>

                                        <div class="row g-1">
                                            <div class="col-md-7">
                                                <div class="input-group input-group-sm mb-3">
                                                    <span class="input-group-text igtSize">Birthday</span>
                                                    <input type="date" class="form-control" name="dob" onblur="checkInput(this)" required />
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group input-group-sm mb-3">
                                                    <select class="form-select" name="gender" onblur="checkInput(this)" required>
                                                        <option disabled selected value="">Gender</option>
                                                        @foreach($genders as $gender)
                                                        <option value="{{$gender->optionValue}}">{{$gender->optionName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">Email</span>
                                            <input type="email" class="form-control" name="email" placeholder="Email Address" required onblur="checkInput(this)" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text"><i class='bx bxs-phone'></i></span>
                                            <input type="tel" class="form-control lts" name="ccc" placeholder="code" list="datalist" onfocus="populateDataList('datalist','ccc')" onblur="checkInput(this)" required pattern="[+]?[0-9]{2,8}" />
                                            <input type="tel" class="form-control w-50 lts" name="mobile" placeholder="Mobile Number" required onblur="checkInput(this)" required pattern="[0-9]{8,14}" />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text  ">Profession</span>
                                            <input type="text" class="form-control" name="profession" placeholder="User's Profession" onblur="checkInput(this)" list="datalist" onfocus="populateDataList('datalist','profession')" />
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">NID</span>
                                            <input type="text" class="form-control lts" name="nidNumber" placeholder="ID Card Number" pattern="[A-Za-z0-9]{8,25}" required onblur="checkInput(this)" />
                                            <label for="nid" class="input-group-text">Upload</label>
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">Mother</span>
                                            <input type="text" class="form-control" name="mother" placeholder="Mother Name" onblur="checkInput(this)" required />
                                        </div>
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">Father</span>
                                            <input type="text" class="form-control" name="father" placeholder="Father Name" onblur="checkInput(this)" required />
                                        </div>
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize">Spouse</span>
                                            <input type="text" class="form-control" name="spouse" placeholder="Spouse Name" />
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <!-- present address  -->
                                    <div class="col-sm-6">
                                        <h6 class="mb-2 text-center">Present Address</h6>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Country</span>
                                            <input type="text" class="form-control" name="pCountry" placeholder="Present Country" onfocus="populateDataList('datalist','country')" onblur="checkInput(this)+populateDataList('datalist',this.value,'district')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">District</span>
                                            <input type="text" class="form-control" name="pDistrict" placeholder="Present District" onblur="checkInput(this)+populateDataList('datalist',this.value,'police-station')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Police Station</span>
                                            <input type="text" class="form-control" name="pPoliceStation" placeholder="Present Police Station" onblur="checkInput(this)+populateDataList('datalist',this.value,'post-office')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Post Office</span>
                                            <input type="text" class="form-control" name="pPostOffice" placeholder="Present Post Office" onblur="checkInput(this)" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text" style="width: 4rem">House</span>
                                            <input type="text" class="form-control" name="pHouse" placeholder="123 / Example mansion" onblur="checkInput(this)" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text" style="width: 4rem">Area</span>
                                            <input type="text" class="form-control" name="pArea" placeholder="Area / Village / Road / Block / Ward etc." />
                                        </div>

                                        <div class="form-check mb-0 d-flex gap-2 align-items-center">
                                            <input type="checkbox" class="form-check-input" value="1" name="sameAddress" id="sameAddr" onchange="toggleInputDisabled('fAddress')" />
                                            <label class="form-check-label small" for="sameAddr">Permant & Present address are same. </label>
                                        </div>

                                    </div>
                                    <!-- permanent address  -->
                                    <div class="col-sm-6">
                                        <h6 class="mb-2 text-center">Permanent Address</h6>
                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Country</span>
                                            <input type="text" class="form-control fAddress" name="fCountry" placeholder="Present Country" onfocus="populateDataList('datalist','country')" onblur="checkInput(this)+populateDataList('datalist',this.value,'district')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">District</span>
                                            <input type="text" class="form-control fAddress" name="fDistrict" placeholder="Present District" onblur="checkInput(this)+populateDataList('datalist',this.value, 'police-station')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Police Station</span>
                                            <input type="text" class="form-control fAddress" name="fPoliceStation" placeholder="Present Police Station" onblur="checkInput(this)+populateDataList('datalist',this.value,'post-office')" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text igtSize-2">Post Office</span>
                                            <input type="text" class="form-control fAddress" name="fPostOffice" placeholder="Present Post Office" onblur="checkInput(this)" list="datalist" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text" style="width: 4rem">House</span>
                                            <input type="text" class="form-control fAddress" name="fHouse" placeholder="123 / Example mansion" onblur="checkInput(this)" required />
                                        </div>

                                        <div class="input-group input-group-sm mb-3">
                                            <span class="input-group-text" style="width: 4rem">Area</span>
                                            <input type="text" class="form-control fAddress" name="fArea" placeholder="Area / Village / Road / Block / Ward etc." />
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Remarks  -->
                                <div class="form-floating mb-3 ">
                                    <textarea class="form-control" name="remarks" placeholder="Write remarks if any." style="height: 10rem"></textarea>
                                    <label for="remarks"><i class="bx bxs-comment"></i> Remarks</label>
                                </div>
                                <!-- /Remarks  -->

                                <h6 class="mb-2 text-end">Upload </h6>

                                <div class="input-group input-group-sm mb-3">
                                    <label for="nid" class="input-group-text igtSize-2">NID Card</label>
                                    <input type="file" class="form-control" id="nid" name="nid" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" onchange="showimage(this.files[0],'viewNid',`{{asset('assets/img/icons/blank-card.webp')}}`)" />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <label for="photo" class="input-group-text igtSize-2">User Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" onchange="showimage(this.files[0],'viewPhoto',`{{asset('assets/img/icons/blank-profile-picture.webp')}}`)" />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <label for="sign" class="input-group-text igtSize-2">Signature</label>
                                    <input type="file" class="form-control" id="sign" name="signature" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" onchange="showimage(this.files[0],'viewSign',`{{asset('assets/img/icons/signature-sample.webp')}}`)" />
                                </div>

                                <!-- Preview user's photo , NID and signature  -->

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <img src="{{asset('assets/img/icons/blank-profile-picture.webp')}}" alt="User photo" class="rounded" height="100" width="100" id="viewPhoto" />
                                        </div>
                                        <div>
                                            <img src="{{asset('assets/img/icons/signature-sample.webp')}}" alt="Signature" height="40" width="100" id="viewSign" />
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="text-end">
                                            <img src="{{asset('assets/img/icons/blank-card.webp')}}" alt="NID" height="140" id="viewNid" />
                                        </div>
                                    </div>
                                </div>
                                <!-- /Preview user's photo , NID and signature  -->

                                <!-- submit form  -->
                                <div class="text-end mt-3">
                                    <button type="reset" class="btn btn-warning btn-sm "> <i class='bx bx-refresh'></i> Reset </button>
                                    <button type="submit" class="btn btn-success btn-sm "> <i class='bx bxs-save'></i> Save</button>
                                </div>
                                <!-- /submit form  -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
    .igtSize {
        width: 4rem;
    }

    .igtSize-2 {
        width: 6rem;
    }

    .lts {
        letter-spacing: 0.2rem;
    }
</style>

@endsection


@section('script')

<script>
    //
    document.getElementById('btnCreate').classList.add('btn-info');
    document.getElementById('subMenuHeadings').innerHTML = 'Create user';
    let taskIndicator = document.getElementById('taskIndicator');

    // post form data to create new user 
    let createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.user.create') }}/new";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                createForm.reset();
                document.getElementById('viewPhoto').src = "{{asset('assets/img/icons/blank-profile-picture.webp')}}";
                document.getElementById('viewSign').src = "{{asset('assets/img/icons/signature-sample.web')}}";
                document.getElementById('viewNid').src = "{{asset('assets/img/icons/blank-card.webp')}}";
            }
        })
    });

    //end 
</script>



@endsection
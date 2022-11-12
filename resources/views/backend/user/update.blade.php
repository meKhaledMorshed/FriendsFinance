@extends('backend.master')

@section('pageTitle', 'Update User | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-md-12">
            <!-- frist menu  -->
            @include('backend.user.submenu')
            <!-- /frist menu  -->
            <div class="card">
                <div class="card-header m-0 pt-2 pb-0">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <a href="{{ route('admin.userView'). '/' . $user->id }}" class="btn btn-sm">Profile</a>
                            <button class="btn btn-sm btnInactive" onclick="changeTab(this,'personalTab')">Personal Information</button>
                            <button class="btn btn-sm btnInactive" onclick="changeTab(this,'addressForm')">Address</button>
                            <button class="btn btn-sm btnInactive" onclick="changeTab(this,'documentTab')">Documents</button>
                            <button class="btn btn-sm btnInactive" onclick="changeTab(this,'ContactTab')">Contacts</button>
                            <button class="btn btn-sm btnInactive" onclick="changeTab(this,'securityTab')">Security</button>
                        </div>
                        <span class="d-none small" id="taskIndicator"> Loadning.... <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>
                    </div>
                </div>
                <hr class="mb-2" />
                <!-- Update users -->
                <div class="card-body p-2">
                    <div class="row mx-2 tabView" id="personalTab">
                        <div class="col-md-8">
                            <h5>Personal Informations</h5>
                            <form id="userinfoForm" method="POST" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="data" value="userinfo" />
                                <input type="hidden" name="id" value="{{$user->userinfo->id}}" />
                                <input type="hidden" name="uid" value="{{$user->id}}" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text igtSize">Name</span>
                                            <input type="text" class="form-control" id="name" name="name" value="{{$user->userinfo->name}}" placeholder="Full Name" required onblur="checkInput(this)" />
                                        </div>

                                        <div class="row g-1">
                                            <div class="col-md-7">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text igtSize">Birthday</span>
                                                    <input type="date" class="form-control" id="dob" name="dob" value="{{$user->userinfo->birthday}}" onblur="checkInput(this)" required />
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3">
                                                    <select class="form-select" id="gender" name="gender" onblur="checkInput(this)" required>
                                                        <option disabled value="">Gender</option>
                                                        @foreach($genders as $gender)
                                                        <option value="{{$gender->optionValue}}" {{ $gender->optionValue == $user->userinfo->gender? 'selected':'' }}>{{$gender->optionName}}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-2">
                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="Write remarks if any." style="height: 6rem">{{$user->userinfo->remarks}}</textarea>
                                            <label for="remarks"><i class="bx bxs-comment"></i> Remarks</label>
                                        </div>

                                    </div>
                                    <div class="col-md-6">

                                        <div class="input-group mb-3">
                                            <span class="input-group-text  ">Profession</span>
                                            <input type="text" class="form-control" id="profession" name="profession" value="{{$user->userinfo->profession}}" placeholder="User's Profession" onblur="checkInput(this)" list="professions" />
                                        </div>
                                        <datalist id="professions">
                                            @foreach($professions as $profession)
                                            <option value="{{$profession->optionValue}}"></option>
                                            @endforeach
                                        </datalist>

                                        <div class="input-group mb-3">
                                            <span class="input-group-text igtSize">Mother</span>
                                            <input type="text" class="form-control" id="mother" name="mother" value="{{$user->userinfo->mother}}" placeholder="Mother Name" onblur="checkInput(this)" required />
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text igtSize">Father</span>
                                            <input type="text" class="form-control" id="father" name="father" value="{{$user->userinfo->father}}" placeholder="Father Name" onblur="checkInput(this)" required />
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text igtSize">Spouse</span>
                                            <input type="text" class="form-control" id="spouse" name="spouse" value="{{$user->userinfo->spouse}}" placeholder="Spouse Name" onblur="checkInput(this)" />
                                        </div>

                                    </div>
                                </div>
                                <!-- submit form  -->
                                <div class="d-flex">
                                    <span id="basicInfoProcessing"></span>
                                    <button type="reset" class="btn btn-warning btn-sm m-1"> <i class='bx bx-refresh'></i> Reset </button>
                                    <button type="submit" class="btn btn-success btn-sm m-1"> <i class='bx bxs-save'></i> Save Changes</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <h5>Photo and Signature</h5>

                            <form id="userPhotoForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group input-group-sm mb-3">
                                    <input type="hidden" name="id" value="{{$user->userinfo->id}}" />
                                    <input type="hidden" name="uid" value="{{$user->userinfo->uid}}" />

                                    <span class="input-group-text igtSize">User Photo</span>
                                    <input type="file" class="form-control" name="photo" id="inputUserPhoto" onchange="showimage(this.files[0],'viewUserPic','/assets/photos/{{$user->userinfo->photo}}')" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" />
                                    <button class=" btn btn-success" type="submit">Update</button>
                                </div>
                            </form>
                            <form id="userSignForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group input-group-sm mb-3">
                                    <input type="hidden" name="data" value="userSignature" />
                                    <input type="hidden" name="id" value="{{$user->userinfo->id}}" />
                                    <input type="hidden" name="uid" value="{{$user->userinfo->uid}}" />

                                    <span class="input-group-text igtSize">Signature</span>
                                    <input type="file" class="form-control" name="signature" onchange="showimage(this.files[0],'viewSignature','/assets/documents/signatures/{{$user->userinfo->signature}}')" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" />
                                    <button class=" btn btn-success" type="submit">Update</button>
                                </div>
                            </form>
                            <!-- preview user's photo and signature  -->
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <img src="/assets/photos/{{$user->userinfo->photo}}" alt="user-avatar" class="rounded" height="150" id="viewUserPic" />
                                </div>
                                <div>
                                    <img src="/assets/documents/signatures/{{$user->userinfo->signature}}" alt="signature" height="50" id="viewSignature" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- User Address form -->
                    <form class="d-none tabView" id="addressForm">
                        @csrf

                        <input type="hidden" name="uid" value="{{$user->id}}" />

                        <div class="row mx-3">
                            <div class="col-sm-4">

                                <h6 class="mb-2 text-center text-info">Present Address</h6>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Country</span>
                                    <input type="text" class="form-control" name="pCountry" value="{{ $user->presentAddress==null?'': $user->presentAddress->country}}" placeholder="Present Country" onblur="checkInput(this)+populateDataList('district',this.value,'district')" list="country" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">District</span>
                                    <input type="text" class="form-control" name="pDistrict" value="{{ $user->presentAddress==null?'': $user->presentAddress->district}}" placeholder="Present District" onblur="checkInput(this)+populateDataList('policeStation',this.value,'police-station')" list="district" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Police Station</span>
                                    <input type="text" class="form-control" name="pPoliceStation" value="{{ $user->presentAddress==null?'': $user->presentAddress->policeStation}}" placeholder="Present Police Station" onblur="checkInput(this)+populateDataList('postOffice',this.value,'post-office')" list="policeStation" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Post Office</span>
                                    <input type="text" class="form-control" name="pPostOffice" value="{{ $user->presentAddress==null?'': $user->presentAddress->postOffice}}" placeholder="Present Post Office" onblur="checkInput(this)" list="postOffice" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem">House</span>
                                    <input type="text" class="form-control" name="pHouse" value="{{ $user->presentAddress==null?'': $user->presentAddress->house}}" placeholder="123 / Example mansion" onblur="checkInput(this)" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem">Area</span>
                                    <input type="text" class="form-control" name="pArea" value="{{ $user->presentAddress==null?'': $user->presentAddress->area}}" placeholder="Area / Village / Road / Block / Ward etc." />
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-sm" id="pRemarks" name="pRemarks" placeholder="Remarks if any" style="height: 60px"> {{$user->presentAddress==null?'': $user->presentAddress->remarks}}</textarea>
                                    <label for="pRemarks">Remarks for Present Address</label>
                                </div>

                                <div class="form-check mb-0 d-flex gap-2 align-items-center">
                                    <input type="checkbox" class="form-check-input" value="1" name="sameAddress" id="sameAddress" onchange="toggleInputDisabled('fAddress')" />
                                    <label class="form-check-label small" for="sameAddress">Permant & Present address are same. </label>
                                </div>

                            </div>
                            <!-- permanent address  -->
                            <div class="col-sm-4">
                                <h6 class="mb-2 text-center text-primary">Permanent Address</h6>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Country</span>
                                    <input type="text" class="form-control fAddress" name="fCountry" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->country}}" placeholder="Present Country" onblur="checkInput(this)+populateDataList('district',this.value,'district')" list="country" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">District</span>
                                    <input type="text" class="form-control fAddress" name="fDistrict" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->district}}" placeholder="Present District" onblur="checkInput(this)+populateDataList('policeStation',this.value, 'police-station')" list="district" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Police Station</span>
                                    <input type="text" class="form-control fAddress" name="fPoliceStation" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->policeStation}}" placeholder="Present Police Station" onblur="checkInput(this)+populateDataList('postOffice',this.value,'post-office')" list="policeStation" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text igtSize-2">Post Office</span>
                                    <input type="text" class="form-control fAddress" name="fPostOffice" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->postOffice}}" placeholder="Present Post Office" onblur="checkInput(this)" list="postOffice" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem">House</span>
                                    <input type="text" class="form-control fAddress" name="fHouse" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->house}}" placeholder="123 / Example mansion" onblur="checkInput(this)" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem">Area</span>
                                    <input type="text" class="form-control fAddress" name="fArea" value="{{ $user->permanentAddress==null?'': $user->permanentAddress->area}}" placeholder="Area / Village / Road / Block / Ward etc." />
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-sm fAddress" id="fRemarks" name="pRemarks" placeholder="Remarks if any" style="height: 60px"> {{$user->permanentAddress==null?'': $user->permanentAddress->remarks}}</textarea>
                                    <label for="pRemarks">Remarks for Present Address</label>
                                </div>

                                <div class="text-end my-2">
                                    <button type="reset" class="btn btn-warning btn-sm "> <i class='bx bx-refresh'></i> Reset </button>
                                    <button type="submit" class="btn btn-success btn-sm "> <i class='bx bxs-save'></i> Update</button>
                                </div>
                            </div>
                        </div>
                        <!-- datalist for address  -->
                        <datalist id="country">
                            @foreach($countries as $country)
                            <option value="{{$country->optionValue}}"></option>
                            @endforeach
                        </datalist>

                        <datalist id="district"> </datalist>

                        <datalist id="policeStation"> </datalist>

                        <datalist id="postOffice"> </datalist>

                        <!-- /datalist for address  -->
                    </form>

                    <div class="row mx-1 d-none tabView" id="documentTab">
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-bordered ">
                                <thead>
                                    <div class="d-flex justify-content-between align-items-center mb-2 ">
                                        <h6 class="m-0">All Documents</h6>
                                        <button class="btn btn-sm btn-info" onclick="documentFormSection('edit')">Add New</button>
                                    </div>
                                    <tr class="text-center">
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Number</th>
                                        <th>type</th>
                                        <th>remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->documents as $document)
                                    <tr class="small">
                                        <td>{{ $document->id }}</td>
                                        <td>{{ $document->docName }}</td>
                                        <td>{{ $document->docNumber }}</td>
                                        <td>{{ $document->type }}</td>
                                        <td>{{ $document->remarks }}</td>
                                        <td class="d-flex gap-1 text-center">
                                            <button class="btn btn-sm btn-info px-1" onclick="zoomDocument('{{ $document->document }}')"><i class='bx bxs-zoom-in'></i></button>
                                            <button class="btn btn-sm btn-warning px-1" onclick="documentFormSection('update',{id:'{{ $document->id }}',name:'{{ $document->docName }}',number:'{{ $document->docNumber }}',filename:'{{ $document->document }}',type:'{{ $document->type }}',remarks:'{{ $document->remarks }}',status:'{{ $document->isActive }}'})"><i class='bx bxs-edit'></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <p>No Document found.</p>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="row d-none secInDoc" id="docZoomSection">
                                <h6 class="text-uppercase">Preview document</h6>
                                <div class="col-12">
                                    <img src="/assets/img/icons/blank-document.webp" id="docZoomPreview" alt="" class="d-block rounded border" />
                                </div>
                            </div>
                            <div class="row secInDoc" id="docFormSection">
                                <h6 class="text-uppercase mb-2 " id="docFormHeadings">Add new document</h6>
                                <div class="col-8 py-2">
                                    <form id="documentForm">
                                        @csrf

                                        <input type="hidden" class="form-control" name="id" id="docID" disabled />
                                        <input type="hidden" class="form-control" name="uid" id="docUID" value="{{$user->id}}" />


                                        <div class=" input-group input-group-sm mb-2">
                                            <span class="input-group-text">Name</span>
                                            <input type="text" class="form-control text-capitalize" name="name" id="docName" placeholder="Document Name" onblur="checkInput(this)" onfocus="populateDataList('datalist','document')" list="datalist" required>
                                            <datalist id="datalist"></datalist>
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Number</span>
                                            <input type="text" class="form-control text-uppercase" name="number" id="docNum" placeholder="Document Number" onblur="checkInput(this)">
                                        </div>

                                        <div class="row g-1">
                                            <div class="col-6">
                                                <div class="input-group input-group-sm mb-2">
                                                    <span class="input-group-text">Type</span>
                                                    <select class="form-select px-2" name="type" id="docType" required>
                                                        <option selected disabled>Select</option>
                                                        <option value="NID" title="National ID Card">NID</option>
                                                        <option value="PASSPORT">Passport</option>
                                                        <option value="BRC" title="Birth Registration Certificate">BRC</option>
                                                        <option value="DL" title="Driving License">DL</option>
                                                        <option value="OTHERS">Others</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="input-group input-group-sm mb-2">
                                                    <span class="input-group-text">Status</span>
                                                    <select class="form-select px-2" name="status" id="docStatus">
                                                        <option selected disabled>Select</option>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-2">
                                            <textarea class="form-control" id="docRemarks" name="remarks" placeholder="Write remarks if any." style="height: 60px"></textarea>
                                            <label for="docRemarks"><i class="bx bxs-comment"></i> Remarks</label>
                                        </div>
                                        <div class="row g-1">
                                            <div class="col-7">
                                                <div class="input-group">
                                                    <input type="file" class="form-control form-control-sm" id="doc" name="doc" accept="image/png, image/jpeg, image/jpg, image/PNG, image/JPEG" onchange="showimage(this.files[0],'previewDoc','/assets/img/icons/blank-document.webp')" />
                                                </div>
                                            </div>
                                            <div class="col-5 text-end">
                                                <button class="btn btn-sm btn-warning px-2" type="reset">Reset</button>
                                                <button class="btn btn-sm btn-success p-1" type="submit">Save Change</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-4 px-1 py-2">
                                    <img src="/assets/img/icons/blank-document.webp" id="previewDoc" alt="" class="d-block rounded border" />
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row mx-2 d-none tabView" id="ContactTab">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Alternate Contacts</h6>
                                    <label for="contact" class="btn btn-sm btn-info" onclick="updateContact()">Add New</label>
                                </div>
                                <div>
                                    <form id="altContactForm">
                                        @csrf

                                        <input type="hidden" class="form-control" name="id" id="contactID" disabled />
                                        <input type="hidden" class="form-control" name="uid" id="contactUID" value="{{$user->id}}" />

                                        <div class=" input-group input-group-sm mb-2">
                                            <input type="text" class="form-control w-50" name="contact" id="contact" onblur="checkInput(this)" placeholder="Email, Mobile or Others " required minlength="8" maxlength="40">

                                            <select class=" form-select" name="type" id="contactType" required>
                                                <option selected disabled value="">Type</option>
                                                <option value="email">Email</option>
                                                <option value="mobile" title="Mobile number with country code">Mobile</option>
                                                <option value="others">Others</option>
                                            </select>
                                            <select class="form-select" name="status" id="contactStatus" required>
                                                <option selected disabled value="">Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>

                                            <button class="btn btn-sm btn-success" type="submit">Save</button>
                                        </div>
                                    </form>
                                </div>

                                <table class="table table-sm table-bordered ">

                                    <thead>
                                        <div class="d-flex justify-content-between align-items-center mb-2 ">
                                        </div>
                                        <tr class="text-center">
                                            <th>SL</th>
                                            <th>Contact</th>
                                            <th>type</th>
                                            <th>Status</th>
                                            <th class="text-success"><i class='bx bxs-edit'></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($user->contacts as $contact)
                                        <tr class="small text-center">
                                            <td>{{ $contact->id }}</td>
                                            <td class="text-start">{{ $contact->contact }}</td>
                                            <td>{{ $contact->type }}</td>
                                            <td>{{ $contact->isActive==1 ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning px-1" onclick="updateContact({form:'update', id:'{{ $contact->id }}', contact:'{{ $contact->contact }}', type:'{{ $contact->type }}', status:'{{ $contact->isActive }}'})"><i class='bx bxs-edit'></i></button>
                                            </td>
                                        </tr>
                                        @empty
                                        <p>No Document found.</p>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-2 d-none tabView" id="securityTab">
                        <div class="col-md-6">
                            <h5>User Credintials</h5>
                            <form id="userCredintialForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="uid" value="{{$user->id}}" />

                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="email" name="email" value="{{$user->email}}" placeholder="Email Address" onblur="checkInput(this)" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-2 col-form-label">Mobile</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">

                                            <input type="tel" class="form-control w-25" name="ccc" value="{{ $user->ccc}}" onfocus="populateDataList('datalist','ccc')" list="datalist" onblur="checkInput(this)" placeholder="Code" title="Country Calling Code" pattern="[+]?[0-9]{2,8}" />

                                            <input type="tel" class="form-control w-75" id="mobile" name="mobile" value="{{$user->mobile}}" placeholder="Mobile number" onblur="checkInput(this)" pattern="[0-9]{8,14}" />

                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-2 col-form-label">Two FA</label>
                                    <div class="col-sm-10">

                                        <input type="radio" class="btn-check" name="twoFA" id="twoFAOn" value="1" {{ $user->twoFA == 1 ? 'checked':'' }} />
                                        <label class="btn btn-outline-success" for="twoFAOn">Active</label>

                                        <input type="radio" class="btn-check" name="twoFA" id="twoFAOff" value="0" {{ $user->twoFA == 0 ? 'checked':'' }} />
                                        <label class="btn btn-outline-danger" for="twoFAOff">Inactive</label>

                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-10">

                                        <input type="radio" class="btn-check" name="isActive" id="userActive" value="1" {{ $user->isActive == 1 ? 'checked':'' }} />
                                        <label class="btn btn-outline-success" for="userActive">Active</label>

                                        <input type="radio" class="btn-check" name="isActive" id="userInactive" value="0" {{ $user->isActive == 0 ? 'checked':'' }} />
                                        <label class="btn btn-outline-danger" for="userInactive">Inactive</label>

                                    </div>
                                </div>

                                <!-- submit form  -->
                                <div class="d-flex justify-content-end gap-3 mb-3">
                                    <button type="reset" class="btn btn-warning"> <i class='bx bx-refresh'></i> Reset </button>
                                    <button type="submit" class="btn btn-success"> <i class='bx bxs-save'></i> Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ========================================================================================================================================= -->
                </div>
                <!-- /Update users -->
            </div>
        </div>
    </div>
</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
    .igtSize {
        width: 5rem;
    }

    .igtSize-2 {
        width: 6rem;
    }

    #mobile {
        letter-spacing: 8px;
    }

    #previewDoc {
        max-width: 100%;
        max-height: 12.8rem;
    }

    #docZoomPreview {
        max-width: 100%;
        max-height: 25rem;
    }
</style>

@endsection


@section('script')

<script>
    //
    document.getElementById('subMenuHeadings').innerHTML = 'Update user informations';
    let taskIndicator = document.getElementById('taskIndicator');

    // change Tab visivility  
    function changeTab(btn, tab) {

        let btns = document.getElementsByClassName('btnInactive');
        for (let btn of btns) {
            btn.classList.remove('btn-success');
        }
        btn.classList.add('btn-success');

        let tabViews = document.getElementsByClassName('tabView');
        for (let tabView of tabViews) {
            tabView.classList.add('d-none');
        }
        document.getElementById(tab).classList.remove('d-none');
    }

    function activeDocSection(target) {
        let sections = document.getElementsByClassName('secInDoc');
        for (let section of sections) {
            section.classList.add('d-none');
        }
        document.getElementById(target).classList.remove('d-none');
    }

    function zoomDocument(fileName) {
        activeDocSection('docZoomSection');
        let img = document.getElementById('docZoomPreview');
        img.src = `/assets/documents/${fileName}`;
    }

    function documentFormSection(form = '', data) {
        activeDocSection('docFormSection');
        document.getElementById('docID').disabled = true;

        if (form == 'edit') {
            document.getElementById('docFormHeadings').innerHTML = 'add new document';
            document.getElementById('documentForm').reset();
            document.getElementById('previewDoc').src = '/assets/img/icons/blank-document.webp';
        }
        if (form == 'update') {
            document.getElementById('docFormHeadings').innerHTML = 'update document';
            document.getElementById('docID').disabled = false;
            document.getElementById('docID').value = data.id;
            document.getElementById('docName').value = data.name;
            document.getElementById('docNum').value = data.number;
            document.getElementById('docType').value = data.type;
            document.getElementById('docStatus').value = data.status;
            document.getElementById('docRemarks').value = data.remarks;
            document.getElementById('previewDoc').src = '/assets/documents/' + data.filename;
        }


    }

    function updateContact(data) {

        document.getElementById('altContactForm').reset();

        document.getElementById('contactID').disabled = true;

        if (data.form == 'update') {
            document.getElementById('contactID').disabled = false;
            document.getElementById('contactID').value = data.id;
            document.getElementById('contact').value = data.contact;
            document.getElementById('contactType').value = data.type;
            document.getElementById('contactStatus').value = data.status;
        }
    }

    let userinfoForm = document.getElementById('userinfoForm');
    userinfoForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/personalinfo";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
        });
    });

    let userPhotoForm = document.getElementById('userPhotoForm');
    userPhotoForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/photo";
        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
            res ? userPhotoForm.reset() : '';
        });

    });

    let userSignForm = document.getElementById('userSignForm');
    userSignForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/signature";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
            res ? userSignForm.reset() : '';
        });

    });

    let userAddrForm = document.getElementById('addressForm');
    userAddrForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/address";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
        });

    });

    // add or update documents 
    let documentForm = document.getElementById('documentForm');
    documentForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/document";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
        });
    });

    // Update user Credintial
    let userCredintialForm = document.getElementById('userCredintialForm');
    userCredintialForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/credintials";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
        });
    });

    // Update user alt Contact  
    let altContactForm = document.getElementById('altContactForm');
    altContactForm.addEventListener('submit', e => {
        e.preventDefault();

        taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('admin.postUpdateUser') }}/alternate-contact";

        postdata(url, data).then(res => {
            taskIndicator.classList.add('d-none')
        });
    });


    //end 
</script>



@endsection
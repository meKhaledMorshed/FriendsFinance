@extends('backend.master')

@section('pageTitle', $user->userinfo->name .' | ' . config('app.name'))


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
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="m-0 p-1 text-uppercase">User detail informations</h5>
                </div>
                <hr class="m-0" />
                <!-- Update users -->
                <div class="card-body p-2">
                    <div class="row mx-2 tabView" id="aboutTab">
                        <div class="col-md-8">

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Name </p><span>:&nbsp;</span>
                                        <p class="fw-bold">{{ $user->userinfo->name}}</p>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Birthday </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ date('M d, Y',  strtotime($user->userinfo->birthday))  }}</p>

                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Email </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->email}}</p>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Mobile </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->ccc }} &nbsp; {{ $user->mobile}}</p>
                                    </div>

                                    @if($user->usernid != null)
                                    <div class="d-flex">
                                        <p class="infoTitle">NID </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->usernid->docNumber }}</p>
                                    </div>
                                    @endif

                                </div>

                                <div class="col-md-6">

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Gender </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->userinfo->gender  }}</p>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Profession </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->userinfo->profession}}</p>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Mother </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->userinfo->mother}}</p>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <p class="infoTitle">Father </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->userinfo->father}}</p>
                                    </div>

                                    <div class="d-flex">
                                        <p class="infoTitle">Spouse </p><span>:&nbsp;</span>
                                        <p class="fw-semibold">{{ $user->userinfo->spouse}}</p>
                                    </div>

                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-sm-6">
                                    <!-- present address  -->
                                    <div class="border p-2 font-monospace">
                                        <b>Present Address&nbsp; : &nbsp;</b>
                                        @if( $user->presentAddress != null)
                                        <p class="fw-normal d-inline">
                                            {{ $user->presentAddress->house}},&nbsp;
                                            {{ $user->presentAddress->area}},&nbsp;
                                            {{ $user->presentAddress->postOffice}},&nbsp;
                                            {{ $user->presentAddress->policeStation}},&nbsp;
                                            {{ $user->presentAddress->district}},&nbsp;
                                            {{ $user->presentAddress->country}},&nbsp;
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- permanent address  -->
                                    <div class="border p-2 font-monospace">
                                        <b>Permanent Address&nbsp; : &nbsp;</b>
                                        @if( $user->permanentAddress != null)
                                        <p class="fw-normal d-inline">
                                            {{ $user->permanentAddress->house}},&nbsp;
                                            {{ $user->permanentAddress->area}},&nbsp;
                                            {{ $user->permanentAddress->postOffice}},&nbsp;
                                            {{ $user->permanentAddress->policeStation}},&nbsp;
                                            {{ $user->permanentAddress->district}},&nbsp;
                                            {{ $user->permanentAddress->country}},&nbsp;
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Preview user's photo , NID and signature  -->
                            <div class="row border py-2 mb-3 ">
                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <img src="{{ asset('assets/photos/' . $user->userinfo->photo) }}" alt="User photo" class="rounded" height="100" width="100" id="viewPhoto" />
                                    </div>
                                    <div>
                                        <img src="{{asset('assets/documents/signatures/'.$user->userinfo->signature)}}" alt="Signature" height="40" width="100" id="viewSign" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @if($user->usernid !=null )
                                    <div>
                                        <img src="{{asset('assets/documents/'.$user->usernid->document)}}" alt="NID" height="140" id="viewNid" />
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <!-- /Preview user's photo , NID and signature  -->

                            <!-- Remarks  -->
                            <div class="row border p-1 mb-2 ">
                                <div class="font-monospace">
                                    <b>Remarks&nbsp; : &nbsp;</b>
                                    <span>{{ $user->userinfo->remarks}}</span>
                                </div>
                            </div>
                            <!-- /Remarks  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
    p {
        margin: 0;
    }

    .infoTitle {
        width: 5rem;
    }
</style>

@endsection


@section('script')

<script>
    // 
    document.getElementById('subMenuHeadings').innerHTML = `{{$user->userinfo->name}} <a href="{{ route('admin.updateUser') .'/'. $user->id }}"><i class='bx bx-edit-alt'></i></a>`;
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
                document.getElementById('viewPhoto').src = "{{asset('assets/img/avatars/blank-profile-picture.webp')}}";
                document.getElementById('viewSign').src = "{{asset('assets/documents/signatures/signature-sample.png')}}";
                document.getElementById('viewNid').src = "{{asset('assets/img/elements/blank-card.webp')}}";
            }
        })
    });

    //end 
</script>



@endsection
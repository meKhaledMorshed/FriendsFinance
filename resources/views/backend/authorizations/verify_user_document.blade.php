@extends('backend.master')

@section('pageTitle', 'Approve Users | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!--  menu  -->
    <div class="row">
        <div class="col-md-12">
            @include('backend.authorizations.submenu')
        </div>
    </div>
    <!-- / menu  -->

    <div class="d-flex justify-content-between bg-light p-2 mb-2 rounded">

        <div>
            <button class="btn btn-sm btn-inactive" id="btn-pending" onclick="pullDocuments('pending',this.id)">Pending</button>
            <button class="btn btn-sm btn-inactive" id="btn-active" onclick="pullDocuments('active',this.id)">Active</button>
            <button class="btn btn-sm btn-inactive" id="btn-inactive" onclick="pullDocuments('inactive',this.id)">Inactive</button>
            <button class="btn btn-sm btn-inactive" id="btn-auth" onclick="pullDocuments('authorize',this.id)">Authorized</button>
            <button class="btn btn-sm btn-inactive" id="btn-unauth" onclick="pullDocuments('unauthorize',this.id)">Unauthorized</button>
            <button class="btn btn-sm btn-inactive" id="btn-reject" onclick="pullDocuments('reject',this.id)">Reject</button>
        </div>

        <span class="d-none" id="taskIndicator"><img src="../assets/img/icons/processing-indicator.gif" height="30" /></span>

        <div>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Search....." oninput="pullDocuments(this.value)" autofocus>
            </div>
        </div>

    </div>


    <div class="row g-3" id="docBox">
        <div class="mt-3 text-center">
            <img src="../assets/img/icons/load-indicator-4.gif" height="400" />
        </div>
    </div>

</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
</style>

@endsection

@section('script')


<script>
    //
    document.getElementById('btnVerifyUserDocs').classList.add('btn-info');
    document.getElementById('subMenuHeadings').innerHTML = "User's Documents Verification";

    let taskIndicator = document.getElementById('taskIndicator');

    document.onload = pullDocuments();

    function pullDocuments(filter = 'pending', btn = '') {

        taskIndicator.classList.remove('d-none');
        // change btn color 
        if (btn != '') {
            let inactiveBtns = document.getElementsByClassName('btn-inactive');
            for (let inactiveBtn of inactiveBtns) {
                inactiveBtn.classList.remove('btn-success');
            }
            document.getElementById(btn).classList.add('btn-success')
        }

        fetch("{{ route('admin.pullUserDocuments') }}/" + filter)
            .then(res => {
                if (res.status == 200) {
                    taskIndicator.classList.add('d-none');
                    res.json().then(data => showUserDocuments(data))
                } else {
                    swal("No data found.");
                }

            });
    }

    // works will continue from here 
    function showUserDocuments(results) {
        let docBox = document.getElementById('docBox');
        let doc = '';

        for (let result of results) {

            let status;
            result.isAuth == null ? status = "Pending" : '';
            result.isAuth == -1 ? status = "Reject" : '';
            result.isAuth == 0 ? status = "Unauthorized" : '';
            result.isAuth == 1 ? status = "Authorized" : '';
            result.isActive == 0 && result.isAuth == 1 ? status = "Inactive" : '';
            result.isActive == 1 && result.isAuth == 1 ? status = "Active" : '';

            const dateOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            let date = new Date(result.modifiedDate).toLocaleDateString('en-us', dateOptions);

            let activeBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('active','${ result.id }')" title="Active"><i class='bx bx-power-off'></i></button>`;

            let inActiveBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('inactive','${ result.id }')" title="Inctive"><i class='bx bx-power-off'></i></button>`;

            let authBtn = `<button class='btn btn-success btn-sm p-1' onclick="changeStatus('authorize','${ result.id }')" title="Authorize"><i class='bx bxs-check-circle'></i></button>`;

            let unAuthBtn = `<button class='btn btn-warning btn-sm p-1' onclick="changeStatus('unauthorize','${ result.id }')" title="Unauthorize"><i class='bx bxs-x-circle'></i></button>`;

            let rejectBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('reject','${ result.id }')" title="Reject"><i class='bx bx-trash'></i></button>`;

            let pendingBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('pending','${ result.id }')" title="Pending"><i class='bx bx-refresh'></i></button>`;

            doc += `
                <div class="col-4">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img class="card-img card-img-left" height="100" src="${ '../assets/documents/' + result.document}" alt="Card image" />
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-1">
                                    <h6 class="mb-0">${ result.docName + (result.docNumber!=null? ' - ' + result.docNumber:'') }</h6>
                                    <span class="small mb-1"> ${ result.name } ( ID: ${result.uid} )</span>

                                    <p class="text-muted small">${result.remarks!=null? result.remarks:''}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mx-2">
                                    <span class="small mb-1"> ${ date } </span>
                                    <div>
                                        ${ result.isActive == 0 ? activeBtn : inActiveBtn}
                                        ${ result.isAuth == null || result.isAuth == 0 ? authBtn : unAuthBtn}
                                        ${ result.isAuth == -1 ? pendingBtn : rejectBtn}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        docBox.innerHTML = doc;

    }

    function changeStatus(change, id) {

        swal({
                title: `Are you sure to ${change} ?`,
                buttons: true,
                dangerMode: true,
            })
            .then((next) => {
                if (next) {
                    taskIndicator.classList.remove('d-none');

                    fetch("{{ route('admin.changeUserDocumentStatus') }}/" + change + '/' + id)
                        .then(res => {
                            taskIndicator.classList.add('d-none');
                            if (res.status == 201) {
                                swal("Status successfully changed", {
                                    icon: "success",
                                });
                                pullDocuments(change);
                            } else if (res.status == 403) {
                                swal("Self id modify is not possible.", "", "error");

                            } else {
                                swal("Unable to change status.", "", "error")
                            };

                        });
                }
            });

    }
</script>





@endsection
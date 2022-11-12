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
    <div class="card toggleBox" id="authBox">
        <div class="card-header py-2 px-4">
            <div class="d-flex justify-content-between">

                <div>
                    <button class="btn btn-sm btn-inactive" id="btn-pending" onclick="pullAddress('pending',this.id)">Pending</button>
                    <button class="btn btn-sm btn-inactive" id="btn-active" onclick="pullAddress('active',this.id)">Active</button>
                    <button class="btn btn-sm btn-inactive" id="btn-inactive" onclick="pullAddress('inactive',this.id)">Inactive</button>
                    <button class="btn btn-sm btn-inactive" id="btn-auth" onclick="pullAddress('authorize',this.id)">Authorized</button>
                    <button class="btn btn-sm btn-inactive" id="btn-unauth" onclick="pullAddress('unauthorize',this.id)">Unauthorized</button>
                    <button class="btn btn-sm btn-inactive" id="btn-reject" onclick="pullAddress('reject',this.id)">Reject</button>
                </div>

                <span class="d-none" id="taskIndicator"><img src="../assets/img/icons/processing-indicator.gif" height="30" /></span>

                <div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search....." oninput="pullAddress(this.value)" autofocus>
                    </div>
                </div>

            </div>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-hover table-sm ">
                <thead>
                    <tr class="table-success text-center">
                        <th scope="col">ID</th>
                        <th scope="col">User's Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBody">
                    <tr>
                        <td class="text-center" colspan="11"> Please wait ......... </td>
                    </tr>
                    <!-- result will display here  -->
                </tbody>
            </table>
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
    document.getElementById('btnVerifyAddress').classList.add('btn-info');
    document.getElementById('subMenuHeadings').innerHTML = "User's Address Verification";
    //
    document.onload = pullAddress();

    function pullAddress(filter = 'pending', btn = '') {

        // change btn color 
        if (btn != '') {
            let inactiveBtns = document.getElementsByClassName('btn-inactive');
            for (let inactiveBtn of inactiveBtns) {
                inactiveBtn.classList.remove('btn-success');
            }
            document.getElementById(btn).classList.add('btn-success')
        }

        fetch("{{ route('admin.pullUserAddress') }}/" + filter)
            .then(res => {
                if (res.status == 200) {
                    res.json().then(data => showUserAddress(data))
                } else {
                    swal("No data found.");
                }

            });
    }

    // works will continue from here 
    function showUserAddress(results) {
        let tblBody = document.getElementById('tblBody');
        let row = '';

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
            let birth = new Date(result.birthday).toLocaleDateString('en-us', dateOptions);

            let activeBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('active','${ result.id }')" title="Active"><i class='bx bx-power-off'></i></button>`;

            let inActiveBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('inactive','${ result.id }')" title="Inctive"><i class='bx bx-power-off'></i></button>`;

            let authBtn = `<button class='btn btn-success btn-sm p-1' onclick="changeStatus('authorize','${ result.id }')" title="Authorize"><i class='bx bxs-check-circle'></i></button>`;

            let unAuthBtn = `<button class='btn btn-warning btn-sm p-1' onclick="changeStatus('unauthorize','${ result.id }')" title="Unauthorize"><i class='bx bxs-x-circle'></i></button>`;

            let rejectBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('reject','${ result.id }')" title="Reject"><i class='bx bx-trash'></i></button>`;

            let pendingBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('pending','${ result.id }')" title="Pending"><i class='bx bx-refresh'></i></button>`;

            row += `
                <tr class="small text-center">
                    <td class="text-center">${ result.uid }</td> 

                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between align-items-center">
                            <b>${ result.name } </b> <img src="../assets/photos/${ result.photo }"  alt="Photo"  height="30" /> 
                        </div>
                    </td> 

                    <td  class="text-start">                        
                        House : ${ result.house } ${ result.area != null ? ', Area : '+ result.area : '' }, 
                        Post office : ${ result.postOffice + ', ' + result.policeStation + ', ' + result.district + ', ' + result.country}
                        <snap class="badge ${ result.type == 'Present' ? 'bg-info' : 'bg-warning' }">${ result.type }</snap>
                    </td>

                    <td class="small text-start">${ result.remarks == null ? '-' : result.remarks }</td> 
                    <td>${ status }</td> 
                    <td class="text-nowrap">${ date }</td>  
                    <td class="d-flex justify-content-between gap-1"> 
                        ${ result.isActive == 0 ? activeBtn : inActiveBtn} 
                        ${ result.isAuth == null || result.isAuth == 0 ? authBtn : unAuthBtn}  
                        ${ result.isAuth == -1 ? pendingBtn : rejectBtn}  
                    </td> 
                </tr>
            `;
        }
        tblBody.innerHTML = row;


    }

    function changeStatus(change, id) {

        swal({
                title: `Are you sure to ${change} ?`,
                buttons: true,
                dangerMode: true,
            })
            .then((next) => {
                if (next) {

                    let taskIndicator = document.getElementById('taskIndicator');
                    taskIndicator.classList.remove('d-none');

                    fetch("{{ route('admin.changeUserAddressStatus') }}/" + change + '/' + id)
                        .then(res => {
                            taskIndicator.classList.add('d-none');
                            if (res.status == 201) {
                                swal("Status successfully changed", {
                                    icon: "success",
                                });
                                pullAddress(change);
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
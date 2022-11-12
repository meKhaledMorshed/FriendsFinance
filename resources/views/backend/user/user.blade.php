@extends('backend.master')

@section('pageTitle', 'Users | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-md-12">
            <!-- frist menu  -->
            @include('backend.user.submenu')
            <!-- /frist menu  -->
            <div class="card">
                <div class="card-header m-0 p-2">
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <div class="input-group input-group-sm input-group-merge">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" class="form-control" placeholder="Search...." oninput="pullUsers(this.value)" />
                            </div>
                        </div>
                        <span class="d-none" id="taskIndicator"><img src="../assets/img/icons/load-indicator.gif" height="20" /> Loadning...</span>
                        <div class="ms-auto">
                            <button class="btn btn-sm" onclick="pullUsers('active')">Active</button>
                            <button class="btn btn-sm" onclick="pullUsers('inactive')">Inactive</button>

                        </div>
                    </div>
                </div>
                <!-- Users -->
                <div class="card-body px-2">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Birthday</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                <!-- rows will populate here by js  -->
                                <tr class="text-center">
                                    <td colspan="11">
                                        <div id="defaultRow">
                                            <img src="../assets/img/icons/load-indicator-4.gif" height="300" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /Users -->
            </div>
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
    document.getElementById('btnUser').classList.add('btn-info');
    document.getElementById('subMenuHeadings').innerHTML = 'Users';

    document.onload = pullUsers();

    function pullUsers(filter = '') {

        document.getElementById('taskIndicator').classList.remove('d-none');

        fetch("{{ route('admin.pullUser') }}/" + filter)
            .then(res => {

                document.getElementById('taskIndicator').classList.add('d-none');

                if (res.status == 200) {
                    res.json()
                        .then(users => showUsersOnTable(users));
                } else if (res.status == 404) {
                    res.text().then(txt => swal(txt));
                    document.getElementById('defaultRow').innerHTML = 'Now User found.';
                } else {
                    throw 'No response.'
                }
            })
            .catch(err => swal(err, '', 'error'));
    }

    function showUsersOnTable(users) {
        let tblBody = document.getElementById('tbody');
        let row = '';

        for (let user of users) {

            let isAdmin = user.isAdmin == 1 ? " <i class='bx bxs-check-circle text-success small' title='Admin'></i>" : "";

            let status;
            user.userinfo.isAuth == null ? status = "Pending" : '';
            user.userinfo.isAuth == -1 ? status = "Reject" : '';
            user.userinfo.isAuth == 0 ? status = "Unauthorized" : '';
            user.userinfo.isAuth == 1 ? status = "Authorized" : '';
            user.isActive == 0 && user.userinfo.isAuth == 1 ? status = "Inactive" : '';
            user.isActive == 1 && user.userinfo.isAuth == 1 ? status = "Active" : '';

            const dateOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            let date = new Date(user.userinfo.modifiedDate).toLocaleDateString('en-us', dateOptions);
            let birth = new Date(user.userinfo.birthday).toLocaleDateString('en-us', dateOptions);

            let viewBtn = `<a class='btn btn-info btn-sm p-1' href="{{ route('admin.userView') }}/${user.id}" title='View details' ><i class='bx bxs-user-pin'></i></a>`;
            let editBtn = `<a class='btn btn-primary btn-sm p-1' href="{{ route('admin.updateUser') }}/${user.id}" title='Update User' ><i class='bx bx-edit-alt'></i></a>`;


            row += `
                <tr class="small">
                    <td class="text-center">${ user.id }</td> 
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between align-items-center"> 
                            <p class="mb-0 fw-bold">${ user.userinfo.name + isAdmin } </p>
                            <img src="../assets/photos/${ user.userinfo.photo }" class="rounded" alt="Photo"  height="30" /> 
                        </div>
                    </td> 

                    <td>${ birth }</td>  
                    <td>${ user.email }</td> 
                    <td>${ user.ccc + user.mobile }</td>  
                    
                    <td class="small">${ user.present_address == null ? '' : user.present_address.policeStation +', '+user.present_address.district +', '+user.present_address.country }</td> 

                    <td class="small">${ user.userinfo.remarks == null ? '' : user.userinfo.remarks }</td> 

                    <td class="text-center">${ status }</td>  
                    <td class="d-flex justify-content-between gap-1">${ viewBtn + editBtn  }</td> 
                </tr>
            `;
        }
        tblBody.innerHTML = row;


    }
    //end 
</script>



@endsection
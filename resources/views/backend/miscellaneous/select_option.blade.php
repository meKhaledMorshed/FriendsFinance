@extends('backend.master')

@section('pageTitle', 'Select Options | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <div class="row">
        <div class="col-md-12">
            <!--  menu  -->
            @include('backend.miscellaneous.submenu')
            <!-- / menu  -->
        </div>
    </div>

    <!-- view all select option  -->
    <div class="card toggleBox" id="viewBox">
        <div class="card-header py-2 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Select options</h5>
                <div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search....." oninput="pulldata(this.value)" autofocus>
                    </div>
                </div>
                <div>
                    <button class="btn btn-sm btn-success" onclick="toggleBox('createBox')">Add New</button>
                    <button class="btn btn-sm btn-info" onclick="toggleBox('authBox')">Authorization</button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-hover table-sm ">
                <thead>
                    <tr class="table-success text-center">
                        <th scope="col">ID</th>
                        <th scope="col">Option</th>
                        <th scope="col">Value</th>
                        <th scope="col">Parent ID - Value</th>
                        <th scope="col">Group</th>
                        <th scope="col">Type</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="tblbody">
                    <!-- result will display here  -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- insert or update new select option  -->
    <div class="row toggleBox d-none" id="createBox">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between py-3">
                    <h5 class="m-0" id="soFormHeadings">Create a Select option</h5>
                    <div>
                        <button class="btn btn-sm btn-success" onclick="toggleBox('createBox')">Add New</button>
                        <button class="btn btn-sm btn-warning" onclick="toggleBox('authBox')">Authorization</button>
                        <button class="btn btn-sm btn-info" onclick="toggleBox('viewBox')">View All</button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" id="addOption">
                        @csrf


                        <div class="row">
                            <!-- left column  -->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3 d-none" id="optIdBox">
                                    <span class="input-group-text" style="width: 4rem;">ID</span>
                                    <input type="text" class="form-control" id="optID" name="id" value="" placeholder="ID if update" readonly />

                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem;">Activity</span>
                                    <select class="form-select" id="isActive" name="isActive">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text" style="width: 4rem;">Type</span>
                                    <select class="form-select" id="optType" name="optType" required>
                                        <option value="Option">Option</option>
                                        <option value="Group">Group</option>
                                    </select>
                                </div>



                            </div>

                            <!-- right column  -->

                            <div class="col-md-8">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text w-25">Option Name</span>
                                    <input type="text" class="form-control text-capitalize" id="optName" name="optName" placeholder="Option Name" oninput="copyValue(this.value)" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text w-25">Option Value</span>
                                    <input type="text" class="form-control" id="optValue" name="optValue" placeholder="Option Value" required />
                                </div>

                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text w-25">Group Name</span>
                                    <select class="form-select" id="optGroup" name="optGroup">
                                        <option disabled selected>Select</option>
                                    </select>
                                </div>

                            </div>


                        </div>

                        <div class="d-flex gap-3 mb-2">
                            <div class="input-group input-group-sm ">
                                <span class="input-group-text" style="width: 4rem;">Parent</span>
                                <input type="text" class="form-control" id="parentID" name="parentID" placeholder="Parent ID" oninput="pullParentValue(this.value)" />
                            </div>

                            <div class="input-group input-group-sm ">
                                <input type="text" class="form-control" id="parentValue" placeholder="Parent's Value" disabled title="Parent's Value" />
                            </div>

                            <div class="input-group input-group-sm ">
                                <input type="text" class="form-control" id="parentGroup" placeholder="Parent's Group" disabled title="Parent's Group" />
                            </div>

                        </div>


                        <div class="form-floating">
                            <textarea class="form-control" placeholder="Remarks about the option" id="optRemarks" name="optRemarks"></textarea>
                            <label for="optRemarks">Remarks</label>
                        </div>

                        <div class="d-flex mt-3">
                            <div>
                                <button type="reset" class="btn btn-warning btn-sm" onclick="clearIdValue()">Clear</button>
                                <button type="submit" class="btn btn-primary btn-sm mx-2">Send</button>
                            </div>
                            <span class="alert alert-success d-none py-0 mx-2 my-0" id="showResponse"> </span>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Authorization table for select option  -->
    <div class="card toggleBox d-none" id="authBox">
        <div class="card-header py-2 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">Authorization table</h5>
                <div class="d-flex align-items-center gap-3 ">
                    <div class="input-group input-group-sm">
                        <select class="form-select" id="searchForAuth" name="searchForAuth" onchange="pullUnauthSO(this.value)">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending" selected>Pending</option>
                            <option value="unauth">Unauthorized</option>
                            <option value="auth">Authorized</option>
                            <option value="reject">Reject</option>
                        </select>
                    </div>

                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search....." oninput="pullUnauthSO(this.value)" autofocus>
                    </div>
                </div>
                <div>
                    <button class="btn btn-sm btn-warning" onclick="toggleBox('moreAuthBox')">More Option</button>
                    <button class="btn btn-sm btn-success" onclick="toggleBox('createBox')">Add New</button>
                    <button class="btn btn-sm btn-info" onclick="toggleBox('viewBox')">View All</button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-hover table-sm ">
                <thead>
                    <tr class="table-success text-center">
                        <th scope="col">ID</th>
                        <th scope="col">Option</th>
                        <th scope="col">Value</th>
                        <th scope="col">Parent ID - Value</th>
                        <th scope="col">Group</th>
                        <th scope="col">Type</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="authtblbody">
                    <!-- result will display here  -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- More Authorization options for select option  -->
    <div class="card toggleBox d-none" id="moreAuthBox">
        <div class="card-header py-2 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0">More Actions</h5>
                <div>
                    <button class="btn btn-sm btn-success" onclick="toggleBox('createBox')">Add New</button>
                    <button class="btn btn-sm btn-info" onclick="toggleBox('viewBox')">View All</button>
                    <button class="btn btn-sm btn-info" onclick="toggleBox('authBox')">Authorization</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div>
                <div>
                    <button class="btn btn-sm btn-success" onclick="changeStatus('active','all')+pullUnauthSO('active')">Active All</button>
                    <button class="btn btn-sm btn-info" onclick="changeStatus('inactive','all')+pullUnauthSO('inactive')">Deactive All</button>
                    <button class="btn btn-sm btn-success" onclick="changeStatus('auth','all')+pullUnauthSO('auth')">Authorize All</button>
                    <button class="btn btn-sm btn-warning" onclick="changeStatus('unauth','all')+pullUnauthSO('unauth')">Unauthorize All</button>
                    <button class="btn btn-sm btn-danger" onclick="changeStatus('reject','all')+pullUnauthSO('reject')">Reject All</button>

                </div>
                <hr />
                <div>
                    <button class="btn btn-sm btn-success" onclick="requestOnSOTable('makeBackup')">Make Backup</button>
                    <button class="btn btn-sm btn-info" onclick="requestOnSOTable('useBackup')">Use Backup</button>
                    <button class="btn btn-sm btn-success" onclick="requestOnSOTable('useDefault')">Use Default</button>
                    <button class="btn btn-sm btn-danger" onclick="requestOnSOTable('deleteAll')">Delete All</button>

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
</style>

@endsection

@section('script')

<script>
    // page tab
    document.getElementById('btnSltOpt').classList.add('btn-info');

    document.onload = pulldata();
    // get authorized data 
    function pulldata(filter = '') {
        fetch('pull-select-options/' + filter)
            .then(res => res.json())
            .then(results => showToViewAll(results))
            .catch();
    }

    // row in View All page 
    function showToViewAll(results) {
        let tblbody = document.getElementById('tblbody');
        let row = '';

        for (let result of results) {

            let status;
            result.isAuth == null ? status = "Pending" : '';
            result.isAuth == -1 ? status = "Reject" : '';
            result.isAuth == 0 ? status = "Unauthorized" : '';
            result.isAuth == 1 ? status = "Authorized" : '';
            result.isActive == 0 && result.isAuth == 1 ? status = "Inactive" : '';
            result.isActive == 1 && result.isAuth == 1 ? status = "Active" : '';

            let date = new Date(result.updated_at).toDateString();

            let activeBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('active','${ result.id }')+pulldata()" title="Active"><i class='bx bx-power-off'></i></button>`;
            let inActiveBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('inactive','${ result.id }')+pulldata()" title="Inctive"><i class='bx bx-power-off'></i></button>`;
            let unAuthBtn = `<button class='btn btn-warning btn-sm p-1' onclick="confirm('Are you sure to make Unauthorize ?')?changeStatus('unauth','${ result.id }')+pulldata():''" title="Unauthorize"><i class='bx bxs-x-circle'></i></button>`;

            row += `
                <tr class="small">
                    <td class="text-center">${ result.id }</td>
                    <td>${ result.optionName }</td> 
                    <td>${ result.optionValue == null ? '-' : result.optionValue  }</td> 

                    <td >${ result.parentID == null ? '' : result.parentID +' = '+ result.parentValue }</td> 
                    
                    <td>${ result.group == null ? '-' : result.group }</td> 
                    <td>${ result.type }</td>
                    <td>${ result.remarks == null ? '-' : result.remarks }</td> 
                    <td class="text-center">${ status }</td> 
                    <td class="text-center">${ date }</td>  
                    <td class="d-flex justify-content-between gap-1">
                        <button class='btn btn-info btn-sm p-1' onclick="editselectoption('${ result.id }')"title="Update"><i class='bx bxs-edit-alt'></i></button>
                        ${ result.isActive == 0 ? activeBtn : inActiveBtn} 
                        ${unAuthBtn}                    
                    </td> 
                </tr>
            `;
        }
        tblbody.innerHTML = row;
    }

    function toggleBox(bx) {

        if (bx == 'createBox') {
            document.getElementById('soFormHeadings').innerHTML = 'Create New Select Option';
            document.getElementById('optIdBox').classList.add('d-none');
            document.getElementById('addOption').reset();
            pullGroups();
        }
        bx == 'authBox' ? pullUnauthSO('pending') : '';
        bx == 'viewBox' ? pulldata() : '';
        let targetBox = document.getElementById(bx);
        let activeBoxs = document.getElementsByClassName('toggleBox');

        for (let activeBox of activeBoxs) {
            activeBox.classList.add('d-none');
        }
        targetBox.classList.remove('d-none');
    }

    function copyValue(value) {
        document.getElementById('optValue').value = value;
    }
    // pull groups when create bos is shown 
    function pullGroups() {
        fetch('pull-options-groups')
            .then(res => res.json())
            .then(groups => {
                let selectGroup = document.getElementById('optGroup');
                let option = '<option selected value="">Select</option>';
                for (let group of groups) {
                    option += `  <option value="${group}">${group}</option>  `;
                }
                selectGroup.innerHTML = option;
            })
            .catch();
    }

    // pull parent value for insert or update parent 
    function pullParentValue(id) {
        fetch("pull-options-parent-value/" + id)
            .then(res => {
                if (res.status == 200) {
                    return res.json();
                }
                throw 'No match found';
            })
            .then(val => {
                document.getElementById('parentValue').value = val.optionValue;
                document.getElementById('parentGroup').value = val.group;
            })
            .catch(err => {
                document.getElementById('parentValue').value = err;
                document.getElementById('parentGroup').value = err;
            });

    }

    function editselectoption(id) {
        toggleBox('createBox');
        document.getElementById('optIdBox').classList.remove('d-none');
        document.getElementById('soFormHeadings').innerHTML = 'Update Select Option';

        fetch('pull-single-select-option/' + id)
            .then(res => res.json())
            .then(results => useResults(results))
            .catch();
    }

    function useResults(data) {
        document.getElementById('optID').value = data.id;
        document.getElementById('isActive').value = data.isActive;
        document.getElementById('optName').value = data.optionName;
        document.getElementById('optValue').value = data.optionValue;
        document.getElementById('parentID').value = data.parentID;
        document.getElementById('parentValue').value = data.parentValue;
        document.getElementById('optGroup').value = data.group;
        document.getElementById('optType').value = data.type;
        document.getElementById('optRemarks').value = data.remarks;
    }

    function clearIdValue() {

        document.getElementById('optID').value = null;
    }

    document.querySelector('#addOption').addEventListener('submit', (e) => {
        e.preventDefault();

        let showResponse = document.getElementById('showResponse');

        let data = new FormData(e.target);

        fetch("{{ route('admin.add-select-option') }}", {
                method: 'POST',
                body: data
            })
            .then(response => {
                showResponse.classList.remove('alert-success', 'd-none');
                showResponse.classList.add('alert-danger');

                if (response.status == 201) {
                    document.getElementById('addOption').reset();
                    showResponse.classList.remove('alert-danger');
                    showResponse.classList.add('alert-success');
                    return response.text();
                } else if (response.status == 406) {
                    return response.text();
                }
                throw new Error;
                return false;
            })
            .then(text => {
                showResponse.innerHTML = "<i class='bx bxs-check-circle'></i> " + text;
                pulldata();
                pullGroups();

            })
            .catch(error => {
                showResponse.innerHTML = "<i class='bx bxs-error'></i> Check all fields.";
            });

        setTimeout(() => showResponse.classList.add('d-none'), 5000)


    })

    // get authorized data 
    function pullUnauthSO(filter = null) {
        fetch('pull-select-options/' + filter)
            .then(res => res.json())
            .then(results => showToAuthorization(results))
            .catch();
    }

    // row in  Authorization page 
    function showToAuthorization(results) {
        let tblbody = document.getElementById('authtblbody');
        let row = '';

        for (let result of results) {

            let status;
            result.isAuth == null ? status = "Pending" : '';
            result.isAuth == -1 ? status = "Reject" : '';
            result.isAuth == 0 ? status = "Unauthorized" : '';
            result.isAuth == 1 ? status = "Authorized" : '';
            result.isActive == 0 && result.isAuth == 1 ? status = "Inactive" : '';
            result.isActive == 1 && result.isAuth == 1 ? status = "Active" : '';

            let date = new Date(result.updated_at).toDateString();

            let activeBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeStatus('active','${ result.id }')+pullUnauthSO('active')" title="Active"><i class='bx bx-power-off'></i></button>`;
            let inActiveBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('inactive','${ result.id }')+pullUnauthSO('inactive')" title="Inctive"><i class='bx bx-power-off'></i></button>`;

            let authBtn = `<button class='btn btn-success btn-sm p-1' onclick="changeStatus('auth','${ result.id }')+pullUnauthSO('auth')" title="Authorize" ><i class='bx bx-check-circle'></i></button>`;
            let unAuthBtn = `<button class='btn btn-warning btn-sm p-1' onclick="changeStatus('unauth','${ result.id }')+pullUnauthSO('unauth')" title="Unauthorize"><i class='bx bx-x-circle'></i></button>`;
            let rejectBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeStatus('reject','${ result.id }')+pullUnauthSO('reject')" title="Reject"><i class='bx bxs-trash'></i></button>`;

            row += `
                <tr class="small">
                    <td class="text-center">${ result.id }</td>
                    <td>${ result.optionName }</td> 
                    <td>${ result.optionValue == null ? '-' : result.optionValue  }</td> 

                    <td >${ result.parentID == null ? '' : result.parentID +' = '+ result.parentValue }</td> 
                    
                    <td>${ result.group == null ? '-' : result.group }</td> 
                    <td>${ result.type }</td> 
                    <td>${ result.remarks == null ? '-' : result.remarks }</td> 
                    <td class="text-center">${ status }</td> 
                    <td class="text-center">${ date }</td>  
                    <td class="d-flex justify-content-between gap-1"> 

                        ${ result.isActive == 0 ? activeBtn : inActiveBtn} 

                        ${ result.isAuth == null ? authBtn + unAuthBtn + rejectBtn: ''} 
                        ${ result.isAuth == 0 ? authBtn  + rejectBtn: ''} 
                        ${ result.isAuth == 1 ?  unAuthBtn + rejectBtn: ''}  
                        ${ result.isAuth == -1 ? authBtn + unAuthBtn : ''}  

                    </td> 
                </tr>
            `;
        }
        tblbody.innerHTML = row;
    }

    // change status of select options
    function changeStatus(request = '', id = '') {
        fetch("{{ route('admin.changeStatusSO')}}/" + request + '/' + id)
            .then(res => {
                if (res.status == 202) {
                    return res.text();
                }
                throw new Error(res.text());
            })
            .then(data => {
                handleNotice(data, 'alert-success')
            })
            .catch();

    }

    // request On Select Option Table
    function requestOnSOTable(request = '') {
        fetch("{{ route('admin.requestOnOSTable')}}/" + request)
            .then(res => {
                if (res.status == 201) {
                    return res.text();
                }
                throw new Error('Request not successfull.');
            })
            .then(data => {
                handleNotice(data, 'alert-success');
            })
            .catch(err => {
                handleNotice(err, 'alert-danger');
            });
    }

    //
</script>





@endsection
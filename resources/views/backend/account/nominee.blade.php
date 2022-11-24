@extends('backend.master')

@section('pageTitle', 'Account Nominee | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="card mb-2 views " id="view_1">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0" id="formTitle">Account Nominee Form</h5>
                        <div>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('view_2')">View Nominees</label>
                        </div>
                    </div>

                    <form id="form" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" class="form-control" name="id" id="id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="accountNumber" class="input-group-text igt-1">Account</label>
                            <input type="number" class="form-control" id="accountNumber" name="accountNumber" onblur="pullAccountName(this.value)" placeholder="Account Number" required style="letter-spacing: 0.3rem;" />
                            <input type="text" class="form-control w-25" id="accountName" placeholder="Account Name" readonly />
                        </div>

                        <div class="row g-2 mb-3 ">
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <label for="name" class="input-group-text igt-1">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <label for="dob" class="input-group-text igt-1">Birthday</label>
                                    <input type="date" class="form-control" id="dob" name="dob" required />
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">

                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <label for="gender" class="input-group-text igt-1">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option selected disabled value="">Select</option>
                                        <option value="Female">Female</option>
                                        <option value="Male">Male</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <label for="relation" class="input-group-text igt-1">Relation</label>
                                    <select class="form-select" id="relation" name="relation" required>
                                        <option selected disabled value="">Select</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Brother">Brother</option>
                                        <option value="Sister">Sister</option>
                                        <option value="Spouse">Spouse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <label for="share" class="input-group-text">Share</label>
                                    <input type="number" class="form-control" id="share" name="share" placeholder="100" min="1" max="100" />
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3 ">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <label for="email" class="input-group-text igt-1">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <label for="mobile" class="input-group-text igt-1">Mobile</label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Mobile number with Country code" />
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3 ">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <label for="nid" class="input-group-text igt-1">NID</label>
                                    <input type="text" class="form-control" id="nid" name="nid" placeholder="NID Number" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <label for="passport" class="input-group-text igt-1">Passport</label>
                                    <input type="text" class="form-control" id="passport" name="passport" placeholder="Passport Number" />
                                </div>
                            </div>
                        </div>

                        <!-- Address  -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Nominee address in details" name="address" id="address" style="height: 70px"></textarea>
                            <label for="address">Address</label>
                        </div>

                        <!-- Photo and Remarks  -->
                        <div class="row g-2 mb-3 ">
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <!-- <label for="photo" class="input-group-text igt-1">Photo</label> -->
                                    <input type="file" class="form-control" id="photo" name="photo" title="Nominee Photo" />
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <label for="remarks" class="input-group-text igt-1">Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Leave a remarks here if any" />
                                </div>
                            </div>
                        </div>

                        <!-- Status and Authorization -->
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <div class=" input-group input-group-sm">
                                    <span class="input-group-text">Status</span>

                                    <input type="radio" class="btn-check" name="status" id="active" value="1">
                                    <label class="btn btn-outline-success" for="active">Active</label>

                                    <input type="radio" class="btn-check" name="status" id="inactive" value="0">
                                    <label class="btn btn-outline-danger" for="inactive">Inactive</label>
                                </div>
                            </div>
                            <div>
                                <div class="input-group input-group-sm">

                                    <span class="input-group-text">Authorization</span>

                                    <input type="radio" class="btn-check" name="authorization" id="authorize" value="1">
                                    <label class="btn btn-outline-success" for="authorize">Authorize</label>

                                    <input type="radio" class="btn-check" name="authorization" id="unauthorize" value="0">
                                    <label class="btn btn-outline-warning" for="unauthorize">Unauthorize</label>

                                    <input type="radio" class="btn-check" name="authorization" id="hold" value="">
                                    <label class="btn btn-outline-secondary" for="hold">Hold</label>

                                    <input type="radio" class="btn-check" name="authorization" id="reject" value="-1">
                                    <label class="btn btn-outline-danger" for="reject">Reject</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mb-3">

                            <span class="d-none small" id="taskIndicator"> Loadning.... <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>

                            <button type="reset" class="btn btn-sm btn-warning">Clear</button>
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- ----------------------------------------------------------- view-2 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="view_2">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Accounts</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search...." onblur="loadAccounts(this.value)" />
                        <span class="input-group-text" role="button"><i class='bx bx-search'></i></span>
                    </div>
                    <span class="d-none small" id="searchtaskIndicator"> <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>
                </div>
                <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Create Account</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Account Owner</th>
                        <th>Category</th>
                        <th>Branch</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tbl_body">
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




</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
    .igt-1 {
        width: 4rem;
    }

    .igt-2 {
        width: 12rem;
    }
</style>

@endsection

@section('script')

<script>
    //   

    document.onload = loanNominees();

    async function loanNominees(filter = '') {

        document.getElementById('searchtaskIndicator').classList.remove('d-none');

        const res = await fetch("{{ route('account.pullNominees') }}/" + filter);

        if (res.status == 200) {
            const Nominees = await res.json();
            showNominees(Nominees);
        } else {
            document.getElementById('searchtaskIndicator').classList.add('d-none');
            let tBody = document.getElementById('tbl_body');
            let tr = ` <tr class="text-center"> <td colspan="11">No data found for ${filter}.</td> </tr> `;
            tBody.innerHTML = tr;
        }
    }

    async function showNominees(nominees) {
        let tBody = document.getElementById('tbl_body');
        let tr = '';

        for (let nominee of nominees) {

            let status;
            nominee.isAuth == null ? status = "Pending" : '';
            nominee.isAuth == -1 ? status = "Reject" : '';
            nominee.isAuth == 0 ? status = "Unauthorized" : '';
            nominee.isAuth == 1 ? status = "Authorized" : '';
            nominee.isActive == 0 && nominee.isAuth == 1 ? status = "Inactive" : '';
            nominee.isActive == 1 && nominee.isAuth == 1 ? status = "Active" : '';

            const nomineeNumber = () => {
                let num = nominee.nomineeNumber.toString();
                while (num.length < 10) num = "0" + num;
                return num;
            }

            // const pullUserName = await fetch("{{ route('admin.getUserName') }}/" + nominee.uid);
            // const UserName = pullUserName.status == 200 ? await pullUserName.text() : 'Data Missing';


            let remarks = nominee.remarks != null ? nominee.remarks : '-';

            tr += ` 
                    <tr class="text-center">
                        <td> ${nominee.id}  </td>
                        <td> ${nominee.nomineeName}  </td>
                        <td> ${ nomineeNumber() }  </td>
                        <td> ${UserName}  </td>
                        <td> ${Category}  </td>
                        <td> ${Branch}  </td>
                        <td> ${remarks}  </td>
                        <td> ${status}  </td>
                        <td> 
                            <button class="btn btn-sm btn-warning px-1" onclick="toggleView('update',{
                                    form:'update', 
                                    id:'${nominee.id}', 
                                    uid:'${nominee.uid}', 
                                    user:'${UserName}', 
                                    nomineeName:'${nominee.nomineeName}',  
                                    nomineeNumber:'${ nomineeNumber() }', 
                                    branchID:'${nominee.branchID}', 
                                    catID:'${nominee.catID}', 
                                    isActive:'${nominee.isActive}', 
                                    isAuth:'${nominee.isAuth}',   
                                    remarks:'${remarks}' })">
                                    
                                    <i class='bx bxs-edit'></i>
                            </button>
                         </td>
                    </tr>

                    `;
        }

        tBody.innerHTML = tr;
        document.getElementById('searchtaskIndicator').classList.add('d-none');
    }

    function toggleView(view, data) {

        let views = document.getElementsByClassName('views');
        for (let view of views) {
            view.classList.add('d-none');
        }

        if (view == 'view_2') {
            document.getElementById('view_2').classList.remove('d-none');
            loadAccounts();
        }
        if (view == 'addNew') {
            form.reset();
            document.getElementById('view_1').classList.remove('d-none');
            document.getElementById('id').disabled = true;
            document.getElementById('category').disabled = false;
            document.getElementById('branch').disabled = false;
            document.getElementById('formTitle').innerHTML = 'Add New Account';
        }
        if (view == 'update') {
            document.getElementById('view_1').classList.remove('d-none');

            document.getElementById('id').disabled = false;
            document.getElementById('category').disabled = true;
            document.getElementById('branch').disabled = true;

            document.getElementById('id').value = data.id;
            document.getElementById('uid').value = data.uid;
            document.getElementById('username').value = data.user;
            document.getElementById('accountName').value = data.accountName;
            document.getElementById('accountNumber').value = data.accountNumber;
            document.getElementById('category').value = data.catID;
            document.getElementById('branch').value = data.branchID;

            if (data.isActive == 1) {
                document.getElementById('active').checked = true;
            } else {
                document.getElementById('inactive').checked = true;
            }

            document.getElementById('authorization').value = data.isAuth;
            document.getElementById('remarks').value = data.remarks;

            document.getElementById('formTitle').innerHTML = 'Update Account';
        }

    }

    const form = document.getElementById('form');
    form.addEventListener('submit', e => {

        e.preventDefault();

        document.getElementById('taskIndicator').classList.remove('d-none');

        const formdata = new FormData(e.target);
        const url = "{{ route('account.nominee.postForm') }}";

        postdata(url, formdata).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                form.reset();
            }
        })

    })


    // sned api request for account name
    async function pullAccountName(accNum) {
        const res = await fetch("{{ route('admin.getAccountName') }}/" + accNum);
        if (res.status == 200) {

            const name = await res.text();
            document.getElementById('accountName').value = name;

        } else {
            document.getElementById('accountName').value = '';
            swal('Please input correct Account Number.');
        }
    }

    //
</script>





@endsection
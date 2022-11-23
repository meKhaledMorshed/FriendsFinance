@extends('backend.master')

@section('pageTitle', 'Account | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="card mb-2 views " id="view_1">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0" id="formTitle">Account Form</h5>
                        <div>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('view_2')">View Accounts</label>
                        </div>
                    </div>

                    <form id="form" method="POST">
                        @csrf

                        <input type="hidden" class="form-control" name="id" id="id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="uid" class="input-group-text igt-1">User ID</label>
                            <input type="number" class="form-control w-25" id="uid" name="uid" required onblur="pullUserName(this.value)" />
                            <input type="text" class="form-control w-50" id="username" name="username" placeholder="User Name of Account Holder" readonly />
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="accountName" class="input-group-text igt-1">Account Name</label>
                            <input type="text" class="form-control" id="accountName" name="accountName" required />
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="accountNumber" class="input-group-text igt-1">Account Number</label>
                            <input type="text" class="form-control" id="accountNumber" name="accountNumber" readonly />
                        </div>


                        <div class="row mb-3">
                            <div class="col-6">
                                <div class=" input-group input-group-sm">
                                    <label for="category" class="input-group-text">Category</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option selected disabled value="">Select Category</option>
                                        @forelse($categories as $category)
                                        <option value="{{$category->id}}">{{$category->category}}</option>
                                        @empty
                                        <option selected disabled>Add Category first</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class=" input-group input-group-sm">

                                    <label for="branch" class="input-group-text">Branch</label>
                                    <select class="form-select" id="branch" name="branch" required>
                                        <option selected disabled value="">Select Branch</option>
                                        @forelse($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->branchName}}</option>
                                        @empty
                                        <option selected disabled>No Branch Found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <div class=" input-group input-group-sm">
                                    <span class="input-group-text igt-1">Activity Status</span>

                                    <input type="radio" class="btn-check" name="status" id="active" value="1">
                                    <label class="btn btn-outline-success w-25" for="active">Active</label>

                                    <input type="radio" class="btn-check" name="status" id="inactive" value="0">
                                    <label class="btn btn-outline-danger w-25" for="inactive">Inactive</label>

                                </div>
                            </div>
                            <div class="col-6">
                                <div class=" input-group input-group-sm">

                                    <label for="authorization" class="input-group-text">Authorization</label>
                                    <select class="form-select" id="authorization" name="authorization">
                                        <option selected value="" disabled>Select</option>
                                        <option value="">Keep Pending</option>
                                        <option value="1">Authorize</option>
                                        <option value="0">Unauthorize</option>
                                        <option value="-1">Reject</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a remarks here" name="remarks" id="remarks" style="height: 70px"></textarea>
                            <label for="remarks">Remarks</label>
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
        width: 8rem;
    }

    .igt-2 {
        width: 12rem;
    }
</style>

@endsection

@section('script')

<script>
    //   

    document.onload = loadAccounts();

    async function loadAccounts(filter = '') {

        document.getElementById('searchtaskIndicator').classList.remove('d-none');

        const res = await fetch("{{ route('account.pullAccounts') }}/" + filter);

        if (res.status == 200) {
            const Accounts = await res.json();
            showAccounts(Accounts);
        } else {
            document.getElementById('searchtaskIndicator').classList.add('d-none');
            let tBody = document.getElementById('tbl_body');
            let tr = ` <tr class="text-center"> <td colspan="11">No data found for ${filter}.</td> </tr> `;
            tBody.innerHTML = tr;
        }
    }

    async function showAccounts(accounts) {
        let tBody = document.getElementById('tbl_body');
        let tr = '';

        for (let account of accounts) {

            let status;
            account.isAuth == null ? status = "Pending" : '';
            account.isAuth == -1 ? status = "Reject" : '';
            account.isAuth == 0 ? status = "Unauthorized" : '';
            account.isAuth == 1 ? status = "Authorized" : '';
            account.isActive == 0 && account.isAuth == 1 ? status = "Inactive" : '';
            account.isActive == 1 && account.isAuth == 1 ? status = "Active" : '';

            const AccountNumber = () => {
                let num = account.accountNumber.toString();
                while (num.length < 10) num = "0" + num;
                return num;
            }

            const pullUserName = await fetch("{{ route('admin.getUserName') }}/" + account.uid);
            const UserName = pullUserName.status == 200 ? await pullUserName.text() : 'Data Missing';

            const pullCategoryName = await fetch("{{ route('account.pullCategoryName') }}/" + account.catID);
            const Category = pullCategoryName.status == 200 ? await pullCategoryName.text() : 'Data Missing';

            const pullBranchName = await fetch("{{ route('pullBranchName') }}/" + account.branchID);
            const Branch = pullBranchName.status == 200 ? await pullBranchName.text() : 'Data Missing';

            let remarks = account.remarks != null ? account.remarks : '-';

            tr += ` 
                    <tr class="text-center">
                        <td> ${account.id}  </td>
                        <td> ${account.accountName}  </td>
                        <td> ${ AccountNumber() }  </td>
                        <td> ${UserName}  </td>
                        <td> ${Category}  </td>
                        <td> ${Branch}  </td>
                        <td> ${remarks}  </td>
                        <td> ${status}  </td>
                        <td> 
                            <button class="btn btn-sm btn-warning px-1" onclick="toggleView('update',{
                                    form:'update', 
                                    id:'${account.id}', 
                                    uid:'${account.uid}', 
                                    user:'${UserName}', 
                                    accountName:'${account.accountName}',  
                                    accountNumber:'${ AccountNumber() }', 
                                    branchID:'${account.branchID}', 
                                    catID:'${account.catID}', 
                                    isActive:'${account.isActive}', 
                                    isAuth:'${account.isAuth}',   
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
        const url = "{{ route('account.postForm') }}";

        postdata(url, formdata).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                form.reset();
            }
        })

    })


    // sned api request for username
    async function pullUserName(uid) {
        const res = await fetch("{{ route('admin.getUserName') }}/" + uid);
        if (res.status == 200) {

            const name = await res.text();
            document.getElementById('username').value = name;
            document.getElementById('accountName').value = name;

        } else {
            document.getElementById('username').value = '';
            document.getElementById('accountName').value = '';
            swal('Please input correct user id.');
        }
    }

    //
</script>





@endsection
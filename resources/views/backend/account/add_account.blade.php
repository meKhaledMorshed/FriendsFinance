@extends('backend.master')

@section('pageTitle', 'Add Account | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="card mb-2 views  " id="branchTableView">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Add new Account</h5>
                <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">View Accounts</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Address</th>
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

    <!-- ----------------------------------------------------------- view-2 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="branchFormView">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Add/Update Branch</h5>
                        <div>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('viewBranches')">Branch</label>
                        </div>
                    </div>

                    <form id="branchForm" method="POST">
                        @csrf

                        <input type="hidden" class="form-control" name="id" id="id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="branchName" class="input-group-text igt-1">Branch Name</label>
                            <input type="text" class="form-control" id="branchName" name="branchName" required />
                        </div>

                        <div class=" input-group input-group-sm mb-3">

                            <label for="type" class="input-group-text">Type</label>
                            <select class="form-select w-25" id="type" name="type" required>
                                <option selected disabled value="">Select Type</option>
                                <option value="Core">Core</option>
                                <option value="Division">Division</option>
                                <option value="Branch">Branch</option>
                                <option value="SubBranch">Sub Branch</option>
                                <option value="Other">Other</option>
                            </select>


                            <span class="input-group-text igt-1">Status</span>
                            <select class="form-select w-25 " id="status" name="status">
                                <option selected disabled value="">Activity</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>

                            </select>

                            <select class="form-select w-25" id="authorization" name="authorization">
                                <option selected value="" disabled>Authorization</option>
                                <option value="">Keep Pending</option>
                                <option value="1">Authorize</option>
                                <option value="0">Unauthorize</option>
                                <option value="-1">Reject</option>
                            </select>

                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Branch Address" name="address" id="address" required style="height: 100px"></textarea>
                            <label for="address">Branch Address</label>
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

    document.onload = loadbranches();

    async function loadbranches() {
        const res = await fetch("{{ route('pullBranch') }}");

        if (res.status == 200) {
            await res.json().then(data => showBranchesData(data));
        } else {
            let tBody = document.getElementById('tbl_body');
            let tr = ` 
                    <tr class="text-center">
                        <td colspan="11">
                            <div id="defaultRow">
                                <img src="../assets/img/icons/load-indicator-4.gif" height="300" />
                            </div>
                        </td>
                    </tr>

                    `;
            tBody.innerHTML = tr;
        }
    }

    function showBranchesData(branches) {
        let tBody = document.getElementById('tbl_body');
        let tr = '';

        for (let branch of branches) {

            let status;
            branch.isAuth == null ? status = "Pending" : '';
            branch.isAuth == -1 ? status = "Reject" : '';
            branch.isAuth == 0 ? status = "Unauthorized" : '';
            branch.isAuth == 1 ? status = "Authorized" : '';
            branch.isActive == 0 && branch.isAuth == 1 ? status = "Inactive" : '';
            branch.isActive == 1 && branch.isAuth == 1 ? status = "Active" : '';

            let remarks = branch.remarks != null ? branch.remarks : '-';

            tr += ` 
                    <tr class="text-center">
                        <td> ${branch.id}  </td>
                        <td> ${branch.branchName}  </td>
                        <td> ${branch.type}  </td>
                        <td> ${branch.address}  </td>
                        <td> ${remarks}  </td>
                        <td> ${status}  </td>
                        <td> 
                            <button class="btn btn-sm btn-warning px-1" onclick="toggleView('update',{
                                    form:'update', 
                                    id:'${branch.id}', 
                                    name:'${branch.branchName}',  
                                    type:'${branch.type}', 
                                    isActive:'${branch.isActive}', 
                                    isAuth:'${branch.isAuth}',   
                                    address:'${branch.address}', 
                                    remarks:'${remarks}' })">
                                    
                                    <i class='bx bxs-edit'></i>
                            </button>
                         </td>
                    </tr>

                    `;
        }

        tBody.innerHTML = tr;
    }

    function toggleView(view, data) {

        let views = document.getElementsByClassName('views');
        for (let view of views) {
            view.classList.add('d-none');
        }

        if (view == 'viewBranches') {
            document.getElementById('branchTableView').classList.remove('d-none');
            loadbranches();
        }
        if (view == 'addNew') {
            document.getElementById('branchFormView').classList.remove('d-none');
            document.getElementById('id').disabled = true;
            branchForm.reset();
        }
        if (view == 'update') {
            document.getElementById('branchFormView').classList.remove('d-none');
            document.getElementById('id').disabled = false;
            document.getElementById('id').value = data.id;
            document.getElementById('branchName').value = data.name;
            document.getElementById('type').value = data.type;
            document.getElementById('status').value = data.isActive;
            document.getElementById('authorization').value = data.isAuth;
            document.getElementById('address').value = data.address;
            document.getElementById('remarks').value = data.remarks;
        }

    }


    const branchForm = document.getElementById('branchForm');
    branchForm.addEventListener('submit', e => {

        e.preventDefault();

        document.getElementById('taskIndicator').classList.remove('d-none');

        const formdata = new FormData(e.target);
        const url = "{{ route('addOrUpdateBranch') }}";

        postdata(url, formdata).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                branchForm.reset();
            }
        })

    })

    //
</script>





@endsection
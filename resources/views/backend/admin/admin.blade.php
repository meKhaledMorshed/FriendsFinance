@extends('backend.master')

@section('pageTitle', 'Admin Panel | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!--  menu  -->
    <div class="row">
        <div class="col-md-12">
            @include('backend.admin.menu')
        </div>
    </div>
    <!-- /menu  -->

    <!-- ----------------------------------------------------------- start views ------------------------------------------------------------------- -->
    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views" id="admintbl">
        <div class="card-header">
            <h5 class="mb-0">Presenting all admins</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Assign Date</th>
                        <th>Retire Date</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr class="small">
                        <td>{{$admin->id}}</td>
                        <td>{{$admin->userinfo->name . ' - '. $admin->uid}}</td>
                        <td class="text-center">{{$admin->title->definition }}</td>
                        <td class="text-center text-uppercase">{{$admin->role}}</td>
                        <td>{{$admin->branch->branchName}}</td>
                        <td>{{ date('M d, Y',strtotime($admin->assignDate)) }}</td>
                        <td>{{$admin->retireDate}}</td>
                        <td>{{$admin->remarks}}</td>
                        <td class="text-center">
                            @if($admin->isAuth == 1) Authorize @elseif($admin->isAuth == 0) Unauthorize @elseif($admin->isAuth == -1) Rejected @else Pending @endif
                            @if($admin->isActive==1) <i class='bx bxs-circle text-success'></i> @else <i class='bx bxs-circle text-danger'></i> @endif
                        </td>
                        <td class="text-center">
                            @if($admin->isActive==1) <button class="btn btn-sm btn-danger px-1"><i class='bx bx-power-off'></i></button>
                            @else <button class="btn btn-sm btn-success px-1"><i class='bx bx-power-off'></i></button> @endif
                            <button class="btn btn-sm btn-warning px-1" onclick="adminform({
                                    form:'update', 
                                    adminID:'{{$admin->id}}', 
                                    uid:'{{$admin->uid}}', 
                                    name:'{{$admin->userinfo->name}}', 
                                    titleID:'{{$admin->titleID}}', 
                                    branchID:'{{$admin->branchID}}',
                                    role:'{{$admin->role}}', 
                                    duty:'{{$admin->duty}}', 
                                    assignDate:'{{$admin->assignDate}}', 
                                    retireDate:'{{$admin->retireDate}}', 
                                    remarks:'{{$admin->remarks}}', 
                                    isActive:'{{$admin->isActive}}', 
                                    isAuth:'{{$admin->isAuth}}', 
                                    readPermit:'{{$admin->permission->readPermit}}',
                                    writePermit:'{{$admin->permission->writePermit}}',
                                    editPermit:'{{$admin->permission->editPermit}}',
                                    deletePermit:'{{$admin->permission->deletePermit}}' })"><i class='bx bxs-edit'></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- ----------------------------------------------------------- view-2 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="adminFormBox">
        <div class="card-header">
            <h5 class="mb-0">Admin form</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('adminPanelForm','admin_form') }}" id="adminForm" method="POST">
                        @csrf

                        <input type="hidden" id="adminid" name="adminid" disabled>

                        <div class="input-group mb-3">
                            <label for="user" class="input-group-text igt-1">User</label>
                            <input type="text" class="form-control w-25" id="uid" name="uid" placeholder="User ID" readonly onblur="pullUserName(this.value)" required />
                            <input type="text" class="form-control w-50" id="username" placeholder="User Name" disabled />
                        </div>

                        <div class="d-flex gap-2 mb-3">
                            <div class="input-group">
                                <select class="form-select" id="designation" name="designation" required>
                                    <option selected disabled>Select Designation</option>
                                    @forelse($titles as $title)
                                    <option value="{{$title->id}}">{{$title->definition}}</option>
                                    @empty
                                    <option selected value="">No Data Found</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="role" class="input-group-text">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option selected disabled value="">Select Role</option>
                                    @forelse($roles as $role)
                                    <option value="{{$role}}">{{ ucwords($role) }}</option>
                                    @empty
                                    <option selected value="">No Data Found</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="branch" class="input-group-text">Branch</label>
                                <select class="form-select" id="branch" name="branch" required>
                                    <option selected disabled value="">Select Branch</option>
                                    @forelse($branches as $branch)
                                    <option value="{{$branch->id}}">{{$branch->branchName}}</option>
                                    @empty
                                    <option selected value="">No Data Found</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <label for="joining" class="input-group-text igt-1">Joining Date</label>
                            <input type="date" class="form-control" id="joining" name="joining" required />

                            <label for="retire" class="input-group-text igt-1">Retire Date</label>
                            <input type="date" class="form-control" id="retire" name="retire" disabled />
                        </div>

                        <div class="input-group mb-3">

                            <span class="input-group-text igt-1">Permissions</span>

                            <div class="input-group-text">

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="read" name="read" value="1">
                                    <label class="form-check-label fcl-1" for="read">Read</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="write" name="write" value="1">
                                    <label class="form-check-label fcl-1" for="write">Write</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="edit" name="edit" value="1">
                                    <label class="form-check-label fcl-1" for="edit">Modify</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="delete" name="delete" value="1">
                                    <label class="form-check-label fcl-1" for="delete">Delete</label>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex gap-2 mb-3">

                            <div class="form-floating w-50">
                                <textarea class="form-control" id="duty" name="duty" placeholder="Responsibilities of the admin"></textarea>
                                <label for="duty">Responsibilities</label>
                            </div>
                            <div class="form-floating w-50">
                                <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks if any..."></textarea>
                                <label for="remarks">Remarks</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between gap-3">

                            <div class="input-group w-50">
                                <span class="input-group-text">Status</span>
                                <select class="form-select w-25" id="status" name="status">
                                    <option selected disabled value="">Activity</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>

                                </select>

                                <select class="form-select w-50" id="authorization" name="authorization">
                                    <option selected value="">Select Authorization</option>
                                    <option value="1">Authorize</option>
                                    <option value="0">Unauthorize</option>
                                    <option value="-1">Reject</option>
                                </select>

                            </div>

                            <div class="d-flex gap-3">
                                <button type="reset" class="btn btn-primary">Cancel</button>
                                <button type="submit" class="btn btn-warning">Save</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!-- ----------------------------------------------------------- view-3 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="designationtbl">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Admin Designations</h5>
                <label for="contact" class="btn btn-sm btn-info" onclick="designationFormBox()">Add New</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Designation</th>
                        <th>Type</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="designation_tbl_body">
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
    <!-- ----------------------------------------------------------- view-4 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="designationFormBox">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Add Designations</h5>
                        <label for="contact" class="btn btn-sm btn-info" onclick="designationFormBox()">Add New</label>
                    </div>

                    <form id="designationForm">
                        @csrf

                        <input type="hidden" class="form-control" name="d_id" id="d_id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="d_title" class="input-group-text igt-1">Designations Short</label>
                            <input type="text" class="form-control" id="d_title" name="d_title" required />

                            <label for="d_type" class="input-group-text">Type</label>
                            <select class="form-select" id="d_type" name="d_type" required>
                                <option selected disabled value="">Select</option>
                                <option value="Elected">Elected</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Temporary">Temporary</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="d_definition" class="input-group-text igt-1">Designations Full</label>
                            <input type="text" class="form-control" id="d_definition" name="d_definition" required onclick="populateDataList('datalist','Designation')" list="datalist" />
                            <datalist id="datalist"></datalist>
                        </div>

                        <div class=" input-group input-group-sm mb-3">
                            <span class="input-group-text igt-1">Status</span>
                            <select class="form-select  " id="d_status" name="d_status">
                                <option selected disabled value="">Activity</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>

                            </select>

                            <select class="form-select  " id="d_authorization" name="d_authorization">
                                <option selected value="">Select Authorization</option>
                                <option value="1">Authorize</option>
                                <option value="0">Unauthorize</option>
                                <option value="-1">Reject</option>
                            </select>

                        </div>

                        <div class=" input-group input-group-sm mb-2">
                            <label for="d_remarks" class="input-group-text">Remarks</label>
                            <input type="text" class="form-control" name="d_remarks" id="d_remarks" />
                        </div>

                        <div class="text-end mb-3">

                            <span class="d-none small" id="d_taskIndicator"> Loadning.... <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>

                            <button type="reset" class="btn btn-sm btn-warning">Clear</button>
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ----------------------------------------------------------- view-5 ------------------------------------------------------------------------ -->
    <!-- <div class="card mb-2 views d-none">view-5</div> -->
    <!-- ----------------------------------------------------------- /end views --------------------------------------------------------------------- -->




</div>
<!-- / Content -->


@endsection

@section('css')
<style>
    /* extra css will go from here  */
    .igt-1 {
        width: 10rem;
    }

    .fcl-1 {
        width: 6rem;
    }
</style>

@endsection

@section('script')

<script>
    //  

    function adminform(data) {
        let views = document.getElementsByClassName('views');
        for (let view of views) {
            view.classList.add('d-none');
        }

        document.getElementById('adminFormBox').classList.remove('d-none');

        document.getElementById('adminForm').reset();

        if (data == undefined) {
            document.getElementById('uid').readOnly = false;
            document.getElementById('retire').disabled = true;

            document.getElementById('duty').innerHTML = '';
            document.getElementById('remarks').innerHTML = '';
        }
        if (data != undefined && data.form == 'update') {
            document.getElementById('adminid').disabled = false;
            document.getElementById('adminid').value = data.adminID;
            document.getElementById('uid').value = data.uid;
            document.getElementById('username').value = data.name;
            document.getElementById('designation').value = data.titleID;
            document.getElementById('role').value = data.role;
            document.getElementById('branch').value = data.branchID;
            document.getElementById('joining').value = data.assignDate;
            document.getElementById('retire').value = data.retireDate;
            document.getElementById('retire').disabled = false;

            data.readPermit == 1 ? document.getElementById('read').checked = true : '';
            data.writePermit == 1 ? document.getElementById('write').checked = true : '';
            data.editPermit == 1 ? document.getElementById('edit').checked = true : '';
            data.deletePermit == 1 ? document.getElementById('delete').checked = true : '';

            document.getElementById('duty').innerHTML = data.duty;
            document.getElementById('remarks').innerHTML = data.remarks;
            document.getElementById('status').value = data.isActive;
            document.getElementById('authorization').value = data.isAuth;

        }

    }

    function designationtbl(data) {

        let views = document.getElementsByClassName('views');
        for (let view of views) {
            view.classList.add('d-none');
        }

        document.getElementById('designationtbl').classList.remove('d-none');
        pullDesignations('');
    }

    async function pullDesignations(filter = '') {
        const res = await fetch("{{ route('getDesignations') }}/" + filter);

        if (res.status == 200) {
            await res.json().then(data => useDesignations(data));
        } else {
            let tBody = document.getElementById('designation_tbl_body');
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

    async function useDesignations(datas) {
        let tBody = document.getElementById('designation_tbl_body');
        let tr = '';

        for (let data of datas) {

            let status;
            data.isAuth == null ? status = "Pending" : '';
            data.isAuth == -1 ? status = "Reject" : '';
            data.isAuth == 0 ? status = "Unauthorized" : '';
            data.isAuth == 1 ? status = "Authorized" : '';
            data.isActive == 0 && data.isAuth == 1 ? status = "Inactive" : '';
            data.isActive == 1 && data.isAuth == 1 ? status = "Active" : '';

            let remarks = data.remarks != null ? data.remarks : '';



            tr += `  <tr class="text-center">
                        <td>${data.id}</td>
                        <td>${data.title}</td>
                        <td>${data.definition}</td>
                        <td>${data.type}</td>
                        <td>${remarks}</td>
                        <td>${status}</td>
                        <td>  
                            <button class="btn btn-sm btn-warning px-1" onclick="designationFormBox({
                                    form:'update', 
                                    id:'${data.id}', 
                                    title:'${data.title}', 
                                    definition:'${data.definition}', 
                                    type:'${data.type}', 
                                    status:'${data.isActive}', 
                                    auth:'${data.isAuth}', 
                                    remarks:'${remarks}' })"><i class='bx bxs-edit'></i>
                            </button>
                        
                        </td>
                   </tr>                    
                `;

            tBody.innerHTML = tr;
        }

    }

    function designationFormBox(data) {

        let views = document.getElementsByClassName('views');
        for (let view of views) {
            view.classList.add('d-none');
        }
        document.getElementById('designationFormBox').classList.remove('d-none');

        designationForm.reset();

        if (data != undefined && data.form == 'update') {

            document.getElementById('d_id').disabled = false;

            document.getElementById('d_id').value = data.id;
            document.getElementById('d_title').value = data.title;
            document.getElementById('d_type').value = data.type;
            document.getElementById('d_definition').value = data.definition;

            document.getElementById('d_status').value = data.status;
            document.getElementById('d_authorization').value = data.auth;
            document.getElementById('d_remarks').value = data.remarks;

        }
    }



    // sned api request 
    async function pullUserName(uid) {
        const res = await fetch("{{ route('admin.getUserName') }}/" + uid);
        if (res.status == 200) {
            await res.text().then(name => document.getElementById('username').value = name);
        } else {
            document.getElementById('username').value = '';
            swal('Please input correct user id.');
        }
    }


    let designationForm = document.getElementById('designationForm');
    designationForm.addEventListener('submit', e => {
        e.preventDefault();

        let d_taskIndicator = document.getElementById('d_taskIndicator');
        d_taskIndicator.classList.remove('d-none');

        let data = new FormData(e.target);
        let url = "{{ route('postDesignations') }}";

        postdata(url, data).then(res => {
            d_taskIndicator.classList.add('d-none')
            if (res) {
                designationForm.reset();
            }
        })
    });



    //
</script>





@endsection
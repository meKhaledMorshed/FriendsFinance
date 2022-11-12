@extends('backend.master')

@section('pageTitle', 'Branch | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="card mb-2 views  " id="branchTableView">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Branch</h5>
                <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
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

    <!-- ----------------------------------------------------------- view-2 ------------------------------------------------------------------------ -->
    <div class="card mb-2 views d-none" id="branchFormView">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Add Designations</h5>
                        <label for="contact" class="btn btn-sm btn-info" onclick="toggleView()">Add New</label>
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
        const res = fetch();
    }


    //
</script>





@endsection
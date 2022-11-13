@extends('backend.master')

@section('pageTitle', 'Add Account | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="card mb-2 views " id="view_1">
        <div class="card-body">
            <div class="row">
                <div class="col-6">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Account Category Form</h5>
                        <div>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('viewTable')">All Category</label>
                        </div>
                    </div>

                    <form id="form" method="POST">
                        @csrf

                        <input type="hidden" class="form-control" name="id" id="id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="name" class="input-group-text igt-1">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="parent" class="input-group-text igt-1">Parent Category</label>
                            <select class="form-select" id="parent" name="parent">
                                <option selected disabled value="">Select Parent Category if Any</option>
                                @forelse($categories as $category)
                                <option value="{{$category->id}}">{{$category->category}}</option>
                                @empty
                                <option selected disabled value="">No Category Found</option>
                                @endforelse
                                <option value="">No Parent</option>
                            </select>
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="tag" class="input-group-text igt-1">Category Tag</label>
                            <input type="text" class="form-control" id="tag" name="tag" placeholder="Add multiple tags with comma." />
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
                            <textarea class="form-control" placeholder="Leave a description here" name="description" id="description" style="height: 70px"></textarea>
                            <label for="description">Description</label>
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
                <h5 class="mb-0">Account Categories</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search...." onblur="loadDatas(this.value)" />
                        <span class="input-group-text" role="button"><i class='bx bx-search'></i></span>
                    </div>
                    <span class="d-none small" id="searchtaskIndicator"> <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>
                </div>
                <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add Category</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Parent with ID</th>
                        <th>Description</th>
                        <th>Tags</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th><i class='bx bxs-edit'></i></th>
                    </tr>
                </thead>
                <tbody id="tbl_body">
                    <!-- rows will populate here by js  -->
                    <tr class="text-center">
                        <td colspan="11">
                            <div id="defaultRow">
                                <img src="{{ asset('assets').'/img/icons/load-indicator-4.gif'}}" height="300" />
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


    async function loadDatas(filter = '') {

        document.getElementById('searchtaskIndicator').classList.remove('d-none');

        const res = await fetch("{{ route('account.pullCategories') }}/" + filter);

        if (res.status == 200) {
            document.getElementById('searchtaskIndicator').classList.add('d-none');
            await res.json().then(data => showDataOnTable(data));
        } else {
            document.getElementById('searchtaskIndicator').classList.add('d-none');

            let tBody = document.getElementById('tbl_body');
            let tr = `  <tr class="text-center"> <td colspan="11">No data fount</td> </tr>  `;
            tBody.innerHTML = tr;
        }
    }

    async function showDataOnTable(datas) {
        let tBody = document.getElementById('tbl_body');
        let tr = '';

        for (let data of datas) {

            let status;
            data.isAuth == null ? status = "Pending" : '';
            data.isAuth == -1 ? status = "Reject" : '';
            data.isAuth == 0 ? status = "Unauthorized" : '';
            data.isAuth == 1 ? status = "Authorized" : '';
            data.isActive == 0 && data.isAuth == 1 ? status = "Inactive" : '';
            data.isActive == 1 && data.isAuth == 1 ? status = "Active" : '';

            let description = data.description != null ? data.description : '';
            let tags = data.tags != null ? data.tags : '';
            let remarks = data.remarks != null ? data.remarks : '';

            const res = await fetch("{{ route('account.pullParentCategoryName') }}/" + data.parentCatID);

            const parent = data.parentCatID != null ? await res.text() : '';

            const parentID = data.parentCatID != null ? data.parentCatID : '';

            tr += ` 
                    <tr>
                        <td class="text-center"> ${data.id}  </td>
                        <td> ${data.category}  </td>
                        <td> ${parent + ' - ' + parentID}  </td>
                        <td> ${description}  </td>
                        <td> ${tags}  </td>
                        <td> ${remarks}  </td>
                        <td class="text-center"> ${status}  </td>
                        <td class="text-center"> 
                            <button class="btn btn-sm btn-warning px-1" onclick="toggleView('update',{
                                    form:'update', 
                                    id:'${data.id}', 
                                    name:'${data.category}',  
                                    parentID:'${parentID}',  
                                    tags:'${tags}', 
                                    description:'${description}',
                                    remarks:'${remarks}',
                                    isActive:'${data.isActive}', 
                                    isAuth:'${data.isAuth}'  })">
                                    
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

        if (view == 'viewTable') {
            document.getElementById('view_2').classList.remove('d-none');
            loadDatas();
        }
        if (view == 'addNew') {
            document.getElementById('view_1').classList.remove('d-none');
            document.getElementById('id').disabled = true;
            form.reset();
        }
        if (view == 'update') {
            document.getElementById('view_1').classList.remove('d-none');
            document.getElementById('id').disabled = false;
            document.getElementById('id').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('parent').value = data.parentID;
            document.getElementById('tag').value = data.tags;

            if (data.isActive == 1) {
                document.getElementById('active').checked = true;
            } else {
                document.getElementById('inactive').checked = true;
            }

            document.getElementById('authorization').value = data.isAuth;
            document.getElementById('description').value = data.description;
            document.getElementById('remarks').value = data.remarks;
        }

    }


    const form = document.getElementById('form');
    form.addEventListener('submit', e => {

        e.preventDefault();

        document.getElementById('taskIndicator').classList.remove('d-none');

        const formdata = new FormData(e.target);
        const url = "{{ route('account.category.postForm') }}";

        postdata(url, formdata).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                form.reset();
            }
        })

    })

    //
</script>





@endsection
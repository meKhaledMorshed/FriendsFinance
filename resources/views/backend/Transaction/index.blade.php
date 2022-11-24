@extends('backend.master')

@section('pageTitle', 'Account Nominee | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <!-- ----------------------------------------------------------- view-1 ------------------------------------------------------------------------ -->

    <div class="row views d-none" id="view_1">
        <div class="col-6">
            <div class="card ">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0" id="formTitle">Record new Transaction</h5>
                        <div>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
                            <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('view_2')">View Transactions</label>
                        </div>
                    </div>

                    <form id="form" method="POST">
                        @csrf

                        <input type="hidden" class="form-control" name="id" id="id" disabled />

                        <div class="input-group input-group-sm mb-3">
                            <label for="debit" class="input-group-text igt-1">Debit</label>
                            <input type="number" class="form-control" id="debit" name="debit" onblur="pullAccountName(this.value,'drAccName')" placeholder="Account Number" required style="letter-spacing: 0.3rem;" />
                            <input type="text" class="form-control w-25" id="drAccName" placeholder="Debit Account Name" readonly />
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <label for="credit" class="input-group-text igt-1">Credit</label>
                            <input type="number" class="form-control" id="credit" name="credit" onblur="pullAccountName(this.value,'crAccName')" placeholder="Account Number" required style="letter-spacing: 0.3rem;" />
                            <input type="text" class="form-control w-25" id="crAccName" placeholder="Credit Account Name" readonly />
                        </div>

                        <div class="row g-2 mb-3 ">
                            <div class="col-5">
                                <div class="input-group input-group-sm">
                                    <label for="amount" class="input-group-text igt-1">Amount</label>
                                    <input type="number" step="any" class="form-control" id="amount" name="amount" required />
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                            <div class="col-7">
                                <!-- Narration  -->
                                <div class="input-group input-group-sm">
                                    <label for="narration" class="input-group-text">Narration</label>
                                    <input type="text" class="form-control" id="narration" name="narration" placeholder="Narration about transaction" required />
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
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
    <div class="card mb-2 views " id="view_2">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center ">
                <h5 class="mb-0">Transactions </h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search...." onblur="loadRecords(this.value)" />
                        <span class="input-group-text" role="button"><i class='bx bx-search'></i></span>
                    </div>
                    <span class="d-none small" id="searchtaskIndicator"> <img src="/assets/img/icons/load-indicator.gif" height="15" /></span>
                </div>
                <label for="contact" class="btn btn-sm btn-info" onclick="toggleView('addNew')">Add New</label>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Narration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Br</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody id="tbl_body">
                    <!-- rows will populate here by js  -->
                    <tr class="text-center">
                        <td colspan="9">
                            <div id="defaultRow">
                                <img src="{{ asset('assets') }}/img/icons/load-indicator-4.gif" height="300" />
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

    document.onload = loadRecords();

    async function loadRecords(filter = '') {

        document.getElementById('searchtaskIndicator').classList.remove('d-none');

        const res = await fetch("{{ route('transaction.pull.transactions') }}/" + filter);

        if (res.status == 200) {
            const Records = await res.json();
            showRecords(Records);
        } else {
            document.getElementById('searchtaskIndicator').classList.add('d-none');
            let tBody = document.getElementById('tbl_body');
            let tr = ` <tr class="text-center"> <td colspan="9">No data found for ${filter}.</td> </tr> `;
            tBody.innerHTML = tr;
        }
    }

    async function showRecords(records) {
        let tBody = document.getElementById('tbl_body');
        let tr = '';

        for (let record of records) {

            let status;
            record.isAuth == null ? status = "Pending" : '';
            record.isAuth == -1 ? status = "Reject" : '';
            record.isAuth == 0 ? status = "Unauthorized" : '';
            record.isAuth == 1 ? status = "Authorized" : '';

            const DebitAccountNumber = () => {
                let num = record.debitAccount.toString();
                while (num.length < 10) num = "0" + num;
                return num;
            }
            const CreditAccountNumber = () => {
                let num = record.creditAccount.toString();
                while (num.length < 10) num = "0" + num;
                return num;
            }
            const BranchID = () => {
                let num = record.branchID.toString();
                while (num.length < 3) num = "0" + num;
                return num;
            }
            const dateOptions = {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            const date = new Date(record.modifiedDate).toLocaleDateString('en-us', dateOptions);

            tr += ` 
                    <tr class="text-center small">
                        <td> ${record.id}  </td>
                        <td> ${DebitAccountNumber()}  </td>
                        <td> ${CreditAccountNumber()}  </td>
                        <td> ${record.narration}  </td>
                        <td class="text-end"> ${record.amount} ₺</td>
                        <td> ${status}  </td>
                        <td> ${date}  </td>
                        <td> ${BranchID()} </td>    
                        <td> 
                            <button class="btn btn-sm btn-warning px-1" onclick="toggleView('update',{
                                    form:'update', 
                                    id:'${record.id}', 
                                    debit:'${DebitAccountNumber()}', 
                                    credit:'${CreditAccountNumber()}', 
                                    amount:'${record.amount}',  
                                    narration:'${  record.narration }'  })"> 

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
            loadRecords();
        }
        if (view == 'addNew') {
            form.reset();
            document.getElementById('view_1').classList.remove('d-none');
            document.getElementById('id').disabled = true;
            document.getElementById('formTitle').innerHTML = 'Add New Transaction';
        }
        if (view == 'update') {
            document.getElementById('view_1').classList.remove('d-none');

            document.getElementById('id').disabled = false;

            document.getElementById('id').value = data.id;
            document.getElementById('debit').value = data.debit;
            document.getElementById('credit').value = data.credit;
            document.getElementById('amount').value = data.amount;
            document.getElementById('narration').value = data.narration;

            document.getElementById('formTitle').innerHTML = 'Update Transaction';
        }

    }

    const form = document.getElementById('form');
    form.addEventListener('submit', e => {

        e.preventDefault();

        document.getElementById('taskIndicator').classList.remove('d-none');

        const formdata = new FormData(e.target);
        const url = "{{ route('transaction.create.post') }}";

        postdata(url, formdata).then(res => {
            taskIndicator.classList.add('d-none')
            if (res) {
                form.reset();
                loadRecords();
            }
        })

    });


    // sned api request for account name
    async function pullAccountName(accNum, elementID) {
        const res = await fetch("{{ route('admin.getAccountName') }}/" + accNum);
        if (res.status == 200) {

            const name = await res.text();
            document.getElementById(elementID).value = name;

        } else {
            document.getElementById(elementID).value = '';
            swal('Please input correct Account Number.');
        }
    }

    //
</script>





@endsection
@extends('backend.master')

@section('pageTitle', 'Transaction Authorization | ' . config('app.name'))


@section('content')

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y" id="top">

    <div class="card mb-2 views " id="view_2">
        <div class="card-header py-2">
            <div class="d-flex justify-content-between align-items-center ">

                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0">Transaction Authorization</h5>
                    <span class="d-none" id="taskIndicator"><img src="{{ asset('assets') }}/img/icons/processing-indicator.gif" height="30" /></span>
                </div>

                <div class="d-flex align-items-center gap-1">

                    <button class="btn btn-sm btn-success" onclick="loadRecords('authorize')">Authorize</button>
                    <button class="btn btn-sm btn-warning" onclick="loadRecords('unauthorize')">Unauthorize</button>
                    <button class="btn btn-sm btn-info" onclick="loadRecords('pending')">Pending</button>
                    <button class="btn btn-sm btn-danger" onclick="loadRecords('reject')">Reject</button>

                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search...." onblur="loadRecords(this.value)" />
                        <span class="input-group-text" role="button"><i class='bx bx-search'></i></span>
                    </div>
                </div>
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
</style>

@endsection

@section('script')

<script>
    //   

    document.onload = loadRecords();

    async function loadRecords(filter = 'pending') {

        document.getElementById('taskIndicator').classList.remove('d-none');

        const res = await fetch("{{ route('transaction.pull.transactions') }}/" + filter);

        if (res.status == 200) {
            const Records = await res.json();
            showRecords(Records);
        } else {
            document.getElementById('taskIndicator').classList.add('d-none');
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

            let authBtn = `<button class='btn btn-success btn-sm p-1' onclick="changeAuth('authorize','${ record.id }')" title="Authorize"><i class='bx bxs-check-circle'></i></button>`;

            let unAuthBtn = `<button class='btn btn-warning btn-sm p-1' onclick="changeAuth('unauthorize','${ record.id }')" title="Unauthorize"><i class='bx bxs-x-circle'></i></button>`;

            let rejectBtn = `<button class='btn btn-danger btn-sm p-1' onclick="changeAuth('reject','${ record.id }')" title="Reject"><i class='bx bx-trash'></i></button>`;

            let pendingBtn = `<button class='btn btn-primary btn-sm p-1' onclick="changeAuth('pending','${ record.id }')" title="Pending"><i class='bx bx-refresh'></i></button>`;

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
                            ${ record.isAuth == null || record.isAuth == 0 ? authBtn : unAuthBtn}  
                            ${ record.isAuth == -1 ? pendingBtn : rejectBtn}  
                        </td> 
                    </tr>

                    `;
        }

        tBody.innerHTML = tr;
        document.getElementById('taskIndicator').classList.add('d-none');
    }

    // sned api request for account name
    function changeAuth(change = '', id = '') {
        swal({
                title: `Are you sure to ${change} ?`,
                buttons: true,
                dangerMode: true,
            })
            .then((next) => {
                if (next) {

                    document.getElementById('taskIndicator').classList.remove('d-none');

                    fetch("{{ route('transaction.authorization.request') }}/" + change + '/' + id)
                        .then(res => {
                            document.getElementById('taskIndicator').classList.add('d-none');
                            if (res.status == 201) {
                                swal("Request successfully applied", {
                                    icon: "success",
                                });
                                loadRecords(change);
                            } else {
                                swal("Request not successfull.", "", "error")
                            };
                        });
                }
            });

    }

    //
</script>





@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Admin Dashboard</h1>
            <p>Welcome Admin {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Accounts</h5>
                    <p class="card-text">Manage all accounts</p>
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary">View Accounts</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <p class="card-text">Authorize and monitor transactions</p>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary">View Transactions</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Branches</h5>
                    <p class="card-text">Manage branches</p>
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-primary">View Branches</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account Categories</h5>
                    <p class="card-text">Manage account categories</p>
                    <a href="{{ route('admin.account-categories.index') }}" class="btn btn-primary">View Categories</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

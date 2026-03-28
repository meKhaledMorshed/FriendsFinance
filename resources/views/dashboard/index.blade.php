@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Welcome {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Accounts</h5>
                    <p class="card-text">Manage your accounts</p>
                    <a href="{{ route('accounts.index') }}" class="btn btn-primary">View Accounts</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <p class="card-text">View your transactions</p>
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary">View Transactions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

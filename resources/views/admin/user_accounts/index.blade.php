@extends('adminlte::page')

@section('content_header')
   <h1></h1>
@endsection
@section('content')
<div class="card">
<div class="card-header">
        <div class="row">
            <div class="col-10"> <h1>User Accounts</h1>
            </div>
            <div class="col-2 text-right">
            <a href="{{ route('admin.user_accounts.create') }}" class="btn btn-success float-right">Create Kyc</a>
            </div>
        </div>
    </div>
    <div class="card-body">
    <!-- <a href="{{ route('admin.user_accounts.create') }}" class="btn btn-primary">Add User Account</a> -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>KYC Status</th>
                <th>Document Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userAccounts as $account)
                <tr>
                    <td>{{ $account->id }}</td>
                    <td>{{ $account->u_code }}</td>
                    <td>{{ $account->kyc_status }}</td>
                    <td>{{ $account->account_type }}</td>
                    <td>
                        <a href="{{ route('admin.user_accounts.edit', $account) }}" class="btn btn-warning">Edit</a>
                        <form method="POST" action="{{ route('admin.user_accounts.destroy', $account) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@endsection

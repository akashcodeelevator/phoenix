@extends('adminlte::page')

@section('title', 'User Details')

@section('content_header')
    <h1>User Details</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>{{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>First Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
            <p><strong>Referral User:</strong> {{ @$user->ReferralUser->username }}</p>
            <p><strong>Main Wallet:</strong> {{ $user->WalletUsers->c1??0 }}$</p>
            <p><strong>Fund Wallet:</strong> {{ $user->WalletUsers->c2??0 }}$</p>
            <p><strong>Withdrawal Address:</strong> {{ $user->eth_address??'' }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</p>
            <!-- <p><strong>Updated At:</strong> {{ $user->updated_at->format('Y-m-d H:i:s') }}</p> -->
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@endsection

@extends('user.layouts.app')

@section('content')
    <div class="container pages">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="pe-3">Fund Add</div>

        </div>
        <!--end breadcrumb-->

        <!-- Fund Transfer Form -->
        <div class="row">
            <!-- Display Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display General Error Message -->
            @if ($errors->has('error'))
                <div class="alert alert-danger">
                    {{ $errors->first('error') }}
                </div>
            @endif
            <div class="col-md-6">
                <div class="card card-body card-bg-1">
                    <form action="{{ route('user.fundtransfer.submit') }}" method="POST" accept-charset="utf-8">
                        @csrf
                        <!-- Wallet Balance -->
                        <p>Main Wallet: {{ number_format($main_wallet, 2) }} $</p>
                        <p>Fund Wallet: {{ number_format($fund_wallet, 2) }} $</p>

                        <!-- Select Wallet -->
                        <div class="form-group mt-3">
                            <label for="selected_wallet" style="color: #fff;">Select Wallet</label>
                            <select name="selected_wallet" id="selected_wallet" class="form-control mt-1">
                                <option value="">Select Wallet</option>
                                <option value="main_wallet">Main Wallet</option>
                                <option value="fund_wallet">Fund Wallet</option>
                            </select>
                            @error('selected_wallet')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="form-group mt-3">
                            <label for="tx_username">Username</label>
                            <input type="text" name="tx_username" value="{{ old('tx_username') }}" id="tx_username"
                                class="form-control mt-1 check_username_exist" placeholder="Enter Username"
                                data-response="username_res" aria-describedby="helpId">
                            <span class="text-danger" id="username_res"></span>
                            @error('tx_username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="form-group mt-3">
                            <label for="amount">Enter Amount</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                                class="form-control mt-1" placeholder="Enter Amount" aria-describedby="helpId">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="transaction_type">Transaction Type</label>
                            <select name="transaction_type" id="transaction_type" class="form-control" required>
                                <option value="credit">Credit</option>
                                <option value="debit">Debit</option>
                            </select>
                            @error('transaction_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Submit Button -->
                        <div class="user_form_row_data mt-3">
                            <div class="user_submit_button mb-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-remove">Transfer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

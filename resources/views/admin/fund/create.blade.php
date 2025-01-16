@extends('adminlte::page')

@section('title', 'Add Fund')

@section('content_header')
    <h1>Add Fund</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
        @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fundrequests.create') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required onblur="fetchUserData()">
                    @error('username')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div id="error-message" class="alert alert-danger" style="display:none;"></div>
                <div id="user-info" class="ml-5 mb-3" style="display:none;">
                    <p><strong>Name:</strong> <span id="user-name"></span></p>
                    <p><strong>Wallet Balance:</strong> <span id="wallet-balance"></span></p>
                </div>
                <div class="form-group">
                    <label for="selected_wallet">Wallet</label>
                    <select name="wallet_type" id="selected_wallet" class="form-control" required>
                        <option value="main_wallet">Main Wallet</option>
                        <option value="fund_wallet">Fund Wallet</option>
                    </select>
                    @error('wallet')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="transaction_type">Transaction Type</label>
                    <select name="transaction_type" id="transaction_type" class="form-control" required>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                    </select>
                    @error('transaction_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="remark">Remark</label>
                    <textarea name="remark" id="remark" class="form-control" required>{{ old('remark') }}</textarea>
                    @error('remark')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" name="transfer_btn">Transfer</button>
            </form>
        </div>
    </div>
    <script>
        function fetchUserData() {
            const username = document.getElementById('username').value;
            const errorMessageDiv = document.getElementById('error-message'); // Get the error message div
            errorMessageDiv.style.display = 'none'; 
            if (username.trim() !== '') {
                const url = `{{ route('admin.username', ':username') }}`.replace(':username', username);

                console.log('Request URL:', url); // Debugging: Check the final URL
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const walletBalances = data.wallets;
                            document.getElementById('user-info').style.display = 'block';
                            document.getElementById('user-name').innerText = data.name;
                            document.getElementById('wallet-balance').innerHTML = 
                        `Main Wallet: <strong>${walletBalances['MainWallet']} $</strong> and 
                         Fund Wallet: <strong>${walletBalances['FundWallet']} $</strong>`;
                        } else {
                            document.getElementById('user-info').style.display = 'none';
                            errorMessageDiv.style.display = 'block'; // Make the error message div visible
                            errorMessageDiv.innerHTML = 'Wallet data not found for the user';
                            //alert('User not found');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching user data:', error);
                        document.getElementById('user-info').style.display = 'none';
                        //alert('Error fetching user data.');
                        errorMessageDiv.style.display = 'block'; // Make the error message div visible
                        errorMessageDiv.innerHTML = 'Error fetching wallet data. Please try again later.';
                    });
            }
        }
    </script>
@endsection

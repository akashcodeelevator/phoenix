@extends('user.layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid main-content px-2 px-lg-4">
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
                <!-- Payout Request Section -->
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="payout_request_widthraw price-box">
                                <h4>Payout Request</h4>
                                <div class="payout_request_payout card_ac">
                                    <form action="{{ route('user.withdrawsubmit') }}" method="POST">
                                        @csrf
                                        <span>Main Wallet: $ {{ number_format($main_wallet, 2) }}</span>
                                        <input type="hidden" name="selected_wallet" value="main_wallet">

                                        <div class="form_group detail mt-3">
                                            <div class="input_data_widtr">
                                                <h6>PAYOUT AMOUNT *</h6>
                                            </div>
                                            <div class="payout_rquest">
                                                <input type="text" id="amount" name="amount"
                                                    value="{{ old('amount') }}" class="form-control">
                                                <span class="text-danger">
                                                    @error('amount')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form_group detail mt-3">
                                            <div class="input_data_widtr">
                                                <h6>SELECTED ADDRESS *</h6>
                                            </div>
                                            <div class="payout_rquest">
                                                <input type="text" id="selected_address" name="selected_address"
                                                    value="{{ old('selected_address', $user->eth_address) }}"
                                                    class="form-control">
                                                <span class="text-danger">
                                                    @error('selected_address')
                                                        {{ $message }}
                                                    @enderror
                                                </span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-3"
                                            onclick="return confirm('Are you sure? You want to submit?')">
                                            Submit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Details Section -->
                <div class="col-lg-6 col-md-12 col-sm-12 pb-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="widthraw_page_style price-box">
                                <h4>Withdrawal</h4>
                                <div class="new_box_widthraw card_ac">
                                    <div class="main_Withdraw">
                                        <div class="row">
                                            <!-- Payout Paid Amount -->
                                            <div class="col-lg-6 col-md-12 col-sm-12">
                                                <div class="main-balance mt-3">
                                                    <h5>PAYOUT PAID AMOUNT</h5>
                                                    <h5>$ 0</h5>
                                                </div>
                                                <div class="main_box mt-3">
                                                    <h5>Minimum Payout Amount</h5>
                                                    <h6>$ 50</h6>
                                                </div>
                                            </div>
                                            <!-- Withdrawal Conditions -->
                                            <div class="col-lg-6 col-md-12 col-sm-12">
                                                <div class="main_box mt-3">
                                                    <h5>Withdrawal Conditions</h5>
                                                    <h6>
                                                        <ul style="padding-left: 0;">
                                                            <li style="color:#5dabff">
                                                                <ul>
                                                                    <li style="color:#FFF">All withdrawals are available
                                                                        24x7 daily.</li>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let price = 0; // Replace with actual price data if available.

                document.getElementById('amount').addEventListener('input', function() {
                    const usdtAmount = parseFloat(this.value);
                    if (!isNaN(usdtAmount) && price > 0) {
                        const jasmyAmount = usdtAmount / price;
                        document.getElementById('convertedValue').textContent =
                        `FTM: ${jasmyAmount.toFixed(6)}`;
                    } else {
                        document.getElementById('convertedValue').textContent = 'Invalid amount or price data';
                    }
                });
            });
        </script>
    </div>
@endsection

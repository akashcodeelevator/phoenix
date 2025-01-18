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
                <!-- Form Topup Section -->
                <div class="col-lg-6 col-md-12 col-sm-12 mt-4">
                    <div class="form_topup card price-box">
                        <div class="card-body">
                            <div class="pin_top_page_content"></div>
                            <form action="{{ route('user.viptopupsubmit') }}" method="POST">
                                @csrf
                                <span id="wallet">Fund wallet: ${{ number_format($fund_wallet ?? 100, 2) }}</span>
                                <input type="hidden" name="selected_wallet" value="fund_wallet">
                                <span class="text-danger">
                                    @error('selected_wallet')
                                        {{ $message }}
                                    @enderror
                                </span>

                                <!-- Username Input -->
                                <div class="form-group mt-3">
                                    <label for="tx_username">Username*</label>
                                    <input type="text" id="tx_username" name="tx_username"
                                        value="{{ old('tx_username') }}" class="form-control mt-1 check_username_exist"
                                        placeholder="Enter Username" aria-describedby="helpId" data-response="username_res">
                                    <span class="text-danger">
                                        @error('tx_username')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- Package Selection -->
                                <div class="form-group mt-3">
                                    <label for="selected_pin">Select Package*</label>
                                    <select class="form-control mt-1 selected_pins" name="selected_pin" id="selected_pin"
                                        data-response="total_pins" required>
                                        <option value="">Select Package</option>
                                        <option value="Package4">$ 10,000</option>
                                    </select>
                                    <span class="text-danger">
                                        @error('selected_pin')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary mb-2" name="topup_btn"
                                        onclick="return confirm('Are you sure? You want to submit?')">
                                        Promotion
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Steps for Topup Section -->
                <div class="col-lg-6 col-md-12 col-sm-12 mt-4">
                    <div class="detail_topup card price-box">
                        <div class="card-body">
                            <h4>Steps for Topup</h4>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>You can top up any user ID</p>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>Enter the username you want to top
                                up</p>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>Select a package from the dropdown
                                menu and then click on the top-up button</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

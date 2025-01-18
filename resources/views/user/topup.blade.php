@extends('user.layouts.app')

@section('content')
    <div class="main-content">
        <div class="container-fluid main-content px-2 px-lg-4">

            <div class="row">
                <!-- Form Section -->
                <div class="col-lg-6 col-md-12 col-sm-12 mt-4">
                    <div class="form_topup card price-box">
                        <div class="card-body">
                            <div class="pin_top_page_content">
                                <!-- Additional content can be added here -->
                            </div>
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
                            <form action="{{ route('user.topupsubmit') }}" method="POST">
                                @csrf
                                <span id="wallet">Fund wallet: $ {{ number_format($fund_wallet, 2) }}</span>
                                <input type="hidden" name="selected_wallet" value="fund_wallet">
                                <span class="text-danger">
                                    @error('selected_wallet')
                                        {{ $message }}
                                    @enderror
                                </span>

                                <!-- Username Field -->
                                <div class="form-group mt-3">
                                    <label>Username*</label>
                                    <input type="text" name="tx_username" value="{{ old('tx_username') }}"
                                        data-response="username_res" class="form-control mt-1 check_username_exist"
                                        placeholder="Enter Username" aria-describedby="helpId">
                                    <span id="username_res"></span>
                                    <span class="text-danger">
                                        @error('tx_username')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- Package Selection -->
                                <div class="form-group mt-3">
                                    <label>Select Package*</label>
                                    <select class="form-control mt-1 selected_pins" name="selected_pin" id="selected_pin"
                                        data-response="total_pins" required>
                                        <option value="">Select Package</option>
                                        <option value="Package1">$50 - $250</option>
                                        <option value="Package2">$500 - $1500</option>
                                        <option value="Package3">$2500 - $5000</option>
                                    </select>
                                    <span class="text-danger">
                                        @error('selected_pin')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>

                                <!-- Amount Field -->
                                <div class="form-group mt-3">
                                    <label>Amount*</label>
                                    <input type="text" name="amount" value="{{ old('amount') }}"
                                        class="form-control mt-1" placeholder="Enter Amount" aria-describedby="helpId">
                                    <span class="text-danger">
                                        @error('amount')
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

                <!-- Steps Section -->
                <div class="col-lg-6 col-md-12 col-sm-12 mt-4">
                    <div class="detail_topup card price-box">
                        <div class="card-body">
                            <h4>Steps for Top-up</h4>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>You can top-up any user ID.</p>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>Enter the username you want to
                                top-up.</p>
                            <p><i class="fa fa-check-square me-2" aria-hidden="true"></i>Select a package from the drop-down
                                menu, and then click on the "Promotion" button.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

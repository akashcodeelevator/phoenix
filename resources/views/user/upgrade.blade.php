@extends('user.layouts.app')

@section('content')
    <div class="main-content">
        <style>
            input.user_btn_button {
                margin-top: 10px;
            }

            span#wallet {
                color: #fff;
            }

            .pin_top_page_content h5 {
                color: var(--text2);
            }

            .pin_top_page_content {
                text-align: end;
            }

            .detail_topup p i {
                font-size: 14px;
                margin-right: 10px;
            }

            .form_topup {
                margin-top: 20px;
                padding: 1.5rem 1.5rem;
                background: var(--first) !important;
                border: none !important;
                box-shadow: rgb(0 0 0 / 20%) 0px 5px 15px;
                border-radius: 8px;
            }

            button.user_btn_button {
                padding: 6px 12px;
                border: none;
                background: #5030ab;
                font-size: 14px;
                border-radius: 4px;
                text-transform: capitalize;
                color: #fff;
                font-weight: 500;
            }

            .detail_topup {
                padding: 16px 16px;
                border: none;
                background: #5030ab;
                font-size: 14px;
                border-radius: 4px;
                text-transform: capitalize;
                color: #fff;
                font-weight: 500;
                margin-top: 20px;
            }

            .detail_topup h4 {
                font-size: 20px;
                font-weight: 500;
                text-transform: uppercase;
            }

            h4 {
                color: #fff;
            }
        </style>

        <div class="container pages">
            <div class="pin_topup_page">
                <div class="container">
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
                        <!-- Form Section -->
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="form_topup">
                                <div class="pin_top_page_content"></div>
                                <form action="{{ route('user.upgradesubmit') }}" method="POST">
                                    @csrf
                                    <span id="wallet">Fund wallet:
                                        ${{ number_format($user->fund_wallet ?? 100, 2) }}</span>
                                    <input type="hidden" name="selected_wallet" value="fund_wallet">
                                    <span class="text-danger">
                                        @error('selected_wallet')
                                            {{ $message }}
                                        @enderror
                                    </span>

                                    <!-- Username Field -->
                                    <div class="form-group mt-3">
                                        <label for="tx_username">Username*</label>
                                        <input type="text" id="tx_username" name="tx_username"
                                            value="{{ old('tx_username') }}" class="form-control check_username_exist"
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
                                        <label for="selected_pin">Select Package*</label>
                                        <select class="form-control selected_pins" name="selected_pin" id="selected_pin"
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
                                        <label for="amount">Amount*</label>
                                        <input type="text" id="amount" name="amount" value="{{ old('amount') }}"
                                            class="form-control" placeholder="Enter Amount" aria-describedby="helpId">
                                        <span class="text-danger">
                                            @error('amount')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group mt-4">
                                        <input type="submit" class="user_btn_button btn-remove detail btn btn-primary"
                                            name="topup_btn" onclick="return confirm('Are you sure? You want to Submit.')"
                                            value="Promotion">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Instructions Section -->
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="detail_topup">
                                <h4>Steps for Retopup</h4>
                                <p><i class="fa fa-check-square" aria-hidden="true"></i>You can retopup any user ID</p>
                                <p><i class="fa fa-check-square" aria-hidden="true"></i>Enter the username you want to
                                    retopup</p>
                                <p><i class="fa fa-check-square" aria-hidden="true"></i>Select a package from the dropdown
                                    menu and then click on the retopup button</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function($) {
                $(".btn-remove").click(function() {
                    $(this).css("display", "none");
                });
            })(jQuery);
        </script>
    </div>
@endsection

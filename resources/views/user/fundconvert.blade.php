@extends('user.layouts.app')
@section('content')
    <div class="main-content">
        <div class="container pages">
            <div class="row pt-2 pb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                    </ol>
                </div>
            </div>

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
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card card-body card-bg-1">
                        <form action="{{ route('user.fundconvert.submit') }}" method="POST" accept-charset="utf-8">
                            @csrf {{-- Protect the form with CSRF token --}}

                            <div class="form-group">
                                <label for="from_wallet" class="">Select From Wallet</label>
                                <select name="from_wallet" class="check_balance form-control" data-response="from_area">
                                    <option value="" class="text-dark">Select Wallet</option>
                                    <option value="roi_wallet">Roi wallet</option>
                                    <option value="refferal_wallet">Referral wallet</option>
                                    <option value="autopool_wallet">Autopool wallet</option>
                                </select>
                                <span id="from_area" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="to_wallet" class="">Select To Wallet</label>
                                <select name="to_wallet" class="form-control">
                                    <option value="" class="text-dark">Select Wallet</option>
                                    <option value="fund_wallet">Fund wallet</option>
                                </select>
                                <span class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label for="amount" class="">Enter Amount</label>
                                <input type="number" name="amount" id="amount" value="" class="form-control"
                                    placeholder="Enter Amount" aria-describedby="helpId">
                                <span class="text-danger"></span>
                            </div>

                            <br>
                            <input type="submit" class="btn btn-primary" name="convert_btn" value="Convert">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

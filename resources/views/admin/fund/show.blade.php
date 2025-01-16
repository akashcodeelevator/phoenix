@extends('adminlte::page')

@section('title', 'Fund Details')

@section('content_header')
    <h1>Fund Details</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>{{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <!-- <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>First Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</p> -->
            <!-- <p><strong>Updated At:</strong> {{ $user->updated_at->format('Y-m-d H:i:s') }}</p> -->
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tr>
                                <th>User</th>
                                <td>:</td>
                                <td>{{ $user->name }} ( {{ $user->username }} )</td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <td>:</td>
                                <td>{{ $transactions->amount }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>:</td>
                                <td>{{ $transactions->cripto_address }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>:</td>
                                <td>{{ $transactions->payment_type }}</td>
                            </tr>
                            <tr>
                                <th>UTR Number</th>
                                <td>:</td>
                                <td>{{ $transactions->cripto_type }}</td>
                            </tr>
                            <tr>
                                <th>Payment Slip</th>
                                <td>:</td>
                                <td>
                                    <a href="{{ $transactions->payment_slip }}" target="_blank">
                                        <img src="{{ $transactions->payment_slip }}" style="height:50px;width:50px;">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>:</td>
                                <td>
                                    @if($transactions->status == '0')
                                        <span class="badge bg-warning badge-sm">Pending</span>
                                    @elseif($transactions->status == '1')
                                        <span class="badge bg-success badge-sm">Approved</span>
                                    @elseif($transactions->status == '2')
                                        <span class="badge bg-danger badge-sm">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>:</td>
                                <td>{{ $transactions->date }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    @if($transactions->status == '0')   
                    <form action="{{ route('admin.fundrequests.approve') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="reason">Reason (Give Reason on cancellation)</label>
                            <input type="hidden" name="fund_request_id" value="{{ request()->segment(3) }}">
                            <textarea name="reason" id="reason" class="form-control"></textarea>
                            <small class="text-muted">@error('reason') {{ $message }} @enderror</small>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="approve_btn" value="approve" class="btn btn-success">Approve</button>
                            <button type="submit" name="cancel_btn" value="cancel" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

        </div>
        <div class="card-footer">
            <a href="{{ route('admin.fundrequests.index', ['status' => 'pending']) }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@endsection

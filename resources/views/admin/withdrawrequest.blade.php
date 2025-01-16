@extends('adminlte::page')

@section('title', 'Withdrawal Requests')
<style>
    .image {
        height: 100px;
        width: 100px;
    }
</style>
@section('content_header')
    <h1>Withdrawal Requests</h1>
@stop

@section('content')
    <div class="card">
    <div class="card-header">
            <h3 class="card-title">Withdrawal Request List</h3>
            <div class="card-tools">
                <!-- Status filter links -->
                <a href="{{ route('admin.withdrawrequest.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">Pending</a>
                <a href="{{ route('admin.withdrawrequest.index', ['status' => 'approve']) }}" class="btn btn-success btn-sm">Approved</a>
                <a href="{{ route('admin.withdrawrequest.index', ['status' => 'cancel']) }}" class="btn btn-danger btn-sm">Cancelled</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>UTR Number</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction as $tran)
                        <tr>
                            <td>{{ $tran->id }}</td>
                            <td>{{ $tran->name .'( '.$tran->username.' )'}}</td>
                            <td>{{ number_format($tran->amount, 2) }}</td>
                            <td>
                                @if($tran->status == '0')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($tran->status == '1')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($tran->status == '2')
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $tran->cripto_address }}</td>
                            <td>{{ $tran->date}}</td>
                            <td>
                                <a href="{{ route('admin.withdrawrequest.show', $tran->id) }}" class="btn btn-info btn-sm">View</a>
                                <!-- <a href="" class="btn btn-warning btn-sm">Edit</a> -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

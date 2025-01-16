@extends('adminlte::page')

@section('title', 'Fund Requests')
<style>
    .image {
        height: 100px;
        width: 100px;
    }
</style>
@section('content_header')
    <h1>Fund History</h1>
@stop

@section('content')
    <div class="card">
    <div class="card-header">
            <h3 class="card-title">Fund History List</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Wallet Type</th>
                        <th>Txn Type</th>
                        <th>Date</th>
                        <th>Reasons</th>
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
                            <td>{{ $tran->wallet_type_name }}</td>
                            <td>{{ $tran->debit_credit}}</td>
                            <td>{{ $tran->date}}</td>
                            <td>{{ $tran->reason}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

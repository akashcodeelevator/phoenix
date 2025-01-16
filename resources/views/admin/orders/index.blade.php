@extends('adminlte::page')

@section('title', 'Orders List')

@section('content_header')
    <h1>Orders List</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Orders</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Order Amount</th>
                        <th>Date</th>
                        <th>Topup From User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->username }}</td>
                            <td>{{ $order->order_amount }}</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $order->topupFromUser->name ?? 'N/A' }}</td> <!-- Get user name -->
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $orders->links() }} <!-- This generates the pagination links -->
            </div>
        </div>
    </div>
@endsection

@extends('user.layouts.app')

@section('content')
<div class="main-content">
    <div class="container-fluid main-content px-2 px-lg-4">

        <div class="row income_direct">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="total_report_data card price-box">
                    <div class="card-body">
                        <h4>Total Report</h4>
                        <form action="" method="get">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="report_table">
                                        <div class="form-group">
                                            <label class="control-label">Date wise income</label>
                                            <select name="income_type" id="income_type" class="form-control form-select">
                                                <option selected value="all">Overall</option>
                                                <option value="today">Today</option>
                                                <option value="24hour">Yesterday</option>
                                                <option value="currweek">Current Week</option>
                                                <option value="lastweek" selected>Last Week</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="report_data_new">
                                        <button type="submit" class="btn btn-primary mt-3" name="btn_submit">View</button>
                                        <button onclick="printDiv('report_section')" type="button" class="btn btn-success mt-3">Print</button>
                                        <a href="{{ url('/user/report/income?income_type=all') }}" class="btn btn-danger mt-3">
                                            <i class="fa-solid fa-arrows-rotate"></i> All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="total_number card_ac price-box">
                            <h4>Username: <span class="text-success">{{$user->username}}</span></h4>
                            <h4 class="mt-3">Total Income: <span class="text-success">0</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 price-box" id="report_section">
            <div class="col-8 m-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table_report_income card_ac">
                            <table class="table tab1">
                                <tbody>
                                    <tr class="rep_table">
                                        <th>Staking Income</th>
                                        <th>0</th>
                                    </tr>
                                    <tr class="rep_table">
                                        <th>Level Income</th>
                                        <th>0</th>
                                    </tr>
                                    <tr class="rep_table text-white">
                                        <td><b>Total</b></td>
                                        <td><b>0</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection

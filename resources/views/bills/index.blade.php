<!DOCTYPE html>

<html>
    <head>
        <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">

        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ URL::asset('assets/css/vendors.css') }}">


    </head>
    <style>
        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        body {
          font-family: 'Open Sans', sans-serif;
          background-color: #f1f1f1;
        }

        .container {
          max-width: 800px;
          margin: 0 auto;
          padding: 50px 20px;
        }

        .card {
          border-radius: 10px;
          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
          overflow: hidden;
        }

        .card-header {
          padding: 20px;
          background-color: #20a854;
          color: #fff;
        }

        .card-header h5 {
          margin: 0;
          font-size: 24px;
        }

        .table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
          background-color: #fff;
        }

        .table th,
        .table td {
          padding: 12px;
          text-align: left;
          vertical-align: middle;
        }

        .table th {
          font-weight: bold;
          text-transform: uppercase;
          border-bottom: 2px solid #20a854;
          color: #20a854;
        }

        .table td {
          border-bottom: 1px solid #f1f1f1;
        }

        .table td:last-child {
          text-align: center;
        }

        .table a {
          color: #20a854;
          text-decoration: none;
        }

        .table a:hover {
          text-decoration: underline;
        }
      </style>
    <body>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <center>
                <h5 class="mb-md-0 h6">{{ translate('My bills') }}</h5>
            </center>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th>{{translate('Date')}}</th>
                <th data-breakpoints="lg">{{translate('Amount')}}</th>
                <th data-breakpoints="lg">{{translate('QR')}}</th>
                <th width="10%">{{translate('Options')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($customerPackagePayment as  $payment)
                <tr>
                    <td>{{$payment->date_invoice}}</td>
                    <td>{{$payment->amount}}</td>

                    <td>
                        <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{$payment->qr_code}}&choe=UTF-8" />
                    </td>
                    <td>
                        <a href="{{route('bills.bill_details', encrypt($payment->id))}}">
                            {{translate('Details')}}
                        </a>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

    </body>
</html>



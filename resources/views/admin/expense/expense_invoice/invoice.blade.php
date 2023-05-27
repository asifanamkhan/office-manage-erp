<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/34590e0ca8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="">
    <title>invoice</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>

        .text-right{
            text-align: end;
        }

        .invoice-container {
            padding: 1rem;
        }
        .invoice-container .invoice-header .invoice-logo {
            margin: 0.8rem 0 0 0;
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 700;
            color: #2e323c;
        }
        .invoice-container .invoice-header .invoice-logo img {
            max-width: 130px;
        }
        .invoice-container .invoice-header address {
            font-size: 0.8rem;
            color: #9fa8b9;
            margin: 0;
        }
        .invoice-container .invoice-details {
            margin: 1rem 0 0 0;
            padding: 1rem;
            line-height: 180%;
            background: #f5f6fa;
        }
        .invoice-container .invoice-details .invoice-num {
            text-align: right;
            font-size: 0.8rem;
        }
        .invoice-container .invoice-body {
            padding: 1rem 0 0 0;
        }
        .invoice-container .invoice-footer {
            text-align: center;
            font-size: 0.7rem;
            margin: 5px 0 0 0;
        }

        .invoice-status {
            text-align: center;
            padding: 1rem;
            background: #ffffff;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .invoice-status h2.status {
            margin: 0 0 0.8rem 0;
        }
        .invoice-status h5.status-title {
            margin: 0 0 0.8rem 0;
            color: #9fa8b9;
        }
        .invoice-status p.status-type {
            margin: 0.5rem 0 0 0;
            padding: 0;
            line-height: 150%;
        }
        .invoice-status i {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            display: inline-block;
            padding: 1rem;
            background: #f5f6fa;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
        }
        .invoice-status .badge {
            text-transform: uppercase;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 1rem;
            }
        }


        .custom-table {
            width: 100%;
            border: 0px solid #e0e3ec;
            border-collapse: collapse;
        }

        .custom-table th,
        .custom-table td,
        .custom-table tr{
            padding: 12px 6px;
        }


        .custom-table thead {
            background: #007ae1 !important;
        }
        .custom-table thead th {
            border: 0;
            color: #ffffff;
        }
        .custom-table > tbody tr:hover {
            background: #fafafa;
        }
        .custom-table > tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .custom-table > tbody td {
            border: 1px solid #e6e9f0;
        }


        .card {
            background: #ffffff;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }

        .text-success {
            color: #00bb42 !important;
        }

        .text-muted {
            color: #9fa8b9 !important;
        }

        .custom-actions-btns {
            margin: auto;
            display: flex;
            justify-content: flex-end;
        }

        .custom-actions-btns .btn {
            margin: .3rem 0 .3rem .3rem;
        }

        .invoice-container .invoice-header .invoice-type {
            width: 158px;
            text-align: center;
            margin: auto;
            border: 1px solid gray;
            display: block;
            padding: 2px 4px;
            text-decoration: none;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="row gutters">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="invoice-container">
                        <div class="invoice-header">
                            <!-- Row start -->
                            <div class="row gutters">

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <address class="text-center">
                                        Maxwell admin Inc, 45 NorthWest Street.<br>
                                        Sunrise Blvd, San Francisco.<br>
                                        00000 00000
                                    </address>
                                </div>
                                <div class="col-12">
                                    <a href="#" class="invoice-type">
                                        Expense Invoice
                                    </a>
                                </div>
                            </div>
                            <!-- Row end -->
                            <!-- Row start -->
                            <div class="row gutters">

                                <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
                                    <div class="invoice-details">
                                        <div>
                                            <div>Invoice : #{{ $expenseInvoice->invoices_id }}</div>
                                            <div>{{ \Carbon\Carbon::parse($expenseInvoice->date)->format('j F, Y') }}</div>
                                            <div>Expense By: {{ $expenseInvoice->expenseBy->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Row end -->
                        </div>
                        <div class="invoice-body">
                            <!-- Row start -->
                            <div class="row gutters">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table custom-table m-0">
                                            <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Date</th>
                                                <th>File</th>
                                                <th>Description</th>
                                                <th>Sub Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($expenseInvoiceDetails as $expenseInvoiceDetail)
                                                <tr>
                                                    <td>
                                                        {{ $expenseInvoiceDetail->expenseCategory->name }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($expenseInvoiceDetail->expense_date)->format('j F, Y') }}</td>
                                                    <td>
                                                        @if(!empty($expenseInvoiceDetail->document))
                                                            <a href="{{ asset('upload/expanse/'.$expenseInvoiceDetail->document) }}" target="_blank" >Click View</a>
                                                        @else
                                                            No File
                                                        @endif

                                                    </td>
                                                    <td>
                                                        {{ $expenseInvoiceDetail->description }}
                                                    </td>
                                                    <td>
                                                        {{ $expenseInvoiceDetail->amount }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td colspan="2">
                                                    <p>Subtotal<br>
                                                        Adjustment Type<br>
                                                        Adjustment Balance<br>
                                                    </p>
                                                    <h5 class="text-success"><strong>Grand Total</strong></h5>
                                                </td>
                                                <td>
                                                    <p>
                                                        {{ $expenseInvoice->total }}<br>
                                                        @if($expenseInvoice->adjustment_type == 1)
                                                            Addition
                                                        @else
                                                            Subtraction
                                                        @endif
                                                        <br>
                                                        {{ $expenseInvoice->adjustment_balance }}<br>
                                                    </p>
                                                    <h5 class="text-success"><strong>{{ $expenseInvoice->total_balance }}</strong></h5>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Row end -->
                        </div>
                        <div class="invoice-footer">
                            Thank you for your Business.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        function auto_print() {
            window.print()
        }
        setTimeout(auto_print, 1000);
    </script>
</body>
</html>

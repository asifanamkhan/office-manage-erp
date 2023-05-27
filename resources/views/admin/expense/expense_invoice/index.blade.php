@extends('layouts.dashboard.app')

@section('title', 'Expense')

@push('css')

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        #table_filter,#table_paginate{
            float: right;
        }
        .dataTable{
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive{
            overflow-x: hidden !important;
        }

    </style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Expense</span>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.expense.expense.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table border mb-0" id="table">
                                <thead class="table-header fw-semibold dataTableHeader">
                                    <tr class="align-middle table">
                                        <th class="table-th">#</th>
                                        <th class="table-th">Invoices no</th>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Amount</th>
                                        <th class="table-th">ExpenseBy</th>
                                        <th class="table-th">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection


@push('script')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script>
        $(document).ready(function () {
            var searchable = [];
            var selectable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#table').DataTable({
                order: [],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                processing: true,
                responsive: false,
                serverSide: true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('admin.expense.expense.index')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'expense_invoice_no', name: 'expense_invoice_no', orderable: true, searchable: true},
                    {data: 'expense_invoice_date', name: 'expense_invoice_date' ,orderable: true, searchable: true},
                    {data: 'total', name: 'total' ,orderable: true, searchable: true},
                    {data: 'expenseBy', name: 'expenseBy' ,orderable: true, searchable: true},
                    //only those have manage_user permission will get access
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    deleteItem(id);
                }
            });
        }

        // Delete Button
        function deleteItem(id) {
            var url = '{{ route("admin.expense.expense.destroy",":id") }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id ),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        // show toast message
                        toastr.success(resp.message);
                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }
    </script>
@endpush

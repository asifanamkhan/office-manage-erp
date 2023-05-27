@extends('layouts.dashboard.app')

@section('title', 'Deposit')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.account.deposit.index')}}">Deposit</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.account.deposit.create')}}">Create</a>
            </li>
        </ol>
        <a href="{{ route('admin.account.deposit.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.account.deposit.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_way"> <b>Transaction Type</b><span class="text-danger">*</span></label>
                                <select name="transaction_way" id="transaction_way"class="form-select" onchange="expenseTransactionWay()">
                                    <option value="" >--Select Transaction Type--</option>
                                    <option value="1" >Cash</option>
                                    <option value="2" >Bank</option>
                                </select>
                                    @error('transaction_way')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_title"><b>Transaction Title</b><span class="text-danger">*</span></label>
                                <input type="text" name="transaction_title" id="transaction_title"class="form-control " value="{{ old('transaction_title') }}"placeholder="Enter Transaction Title">
                                    @error('transaction_title')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_date"> <b> Date </b><span class="text-danger">*</span></label>
                                    <div class="form-group{{ $errors->has('transaction_date') ? ' has-error' : '' }} has-feedback">
                                        <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{ old('transaction_date') }}">
                                            @error('transaction_date')
                                            <span class="text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" style="display: none">
                                <label for="account_id"> <b>Bank Account</b><span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id"class="form-select" onchange="getBalance()">
                                    <option >--Select Bank Account--</option>
                                    @foreach ($bank_accounts as $account)
                                         <option value="{{$account->id}}" >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                    @error('account_id')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="deposit_source"> <b> Amount </b><span class="text-danger">*</span><span class="text-info" id="balance" style="display: none"></span></label>
                                <input type="hidden" value="" id="amount_balance">
                                <div class="form-group has-feedback">
                                    <input type="number" min="0"name="amount"id="amount"class="form-control"value="{{ old('amount') }}" placeholder="1200 ...">
                                    @error('amount')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" style="display: none">
                                <label for="cheque_number"> <b>Cheque Number</b> </label>
                                <div class="form-group{{ $errors->has('cheque_number') ? ' has-error' : '' }} has-feedback">
                                    <input type="text" name="cheque_number" id="cheque_number" class="form-control"value="{{ old('cheque_number') }}" placeholder=" ...">
                                    @error('cheque_number')
                                        <span class="help-block">
                                            <strong>{{ $message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"class="form-control " value="{{ old('description') }}"placeholder="Description..."></textarea>
                                @error('description')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</form>
    <div class="mb-5"></div>


@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        CKEDITOR.replace('description', {
            toolbarGroups: [
                {"name": "styles","groups": ["styles"]},
                {"name": "basicstyles","groups": ["basicstyles"]},
                {"name": "paragraph","groups": ["list", "blocks"]},
                {"name": "document","groups": ["mode"]},
                {"name": "links","groups": ["links"]},
                {"name": "insert","groups": ["insert"]},
                {"name": "undo","groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });


        function expenseTransactionWay() {
            var transaction_way = $("#transaction_way").val();
            if (transaction_way == 2) {
                $('.bank-way').show();
            } else {
                 $('.bank-way').hide();
                 var url = '{{ route("admin.account.bank.account.balance",":id") }}';
                 $.ajax({
                     type: "GET",
                     url: url.replace(':id', 0),
                     success: function (resp) {
                         $('#balance').show();
                         document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                         $('#amount_balance').val(resp);
                         //document.getElementById('amount').max = resp;
                     }, // success end
                     error: function (error) {
                         // location.reload();
                     } // Error
                 })

             }
        };
        function getBalance() {
             var transactionWay = $('#transaction_way').val();
             var accountId = $('#account_id').val();
             if (accountId !== null) {
                 if (transactionWay == 2   ) {
                     var url = '{{ route("admin.account.bank.account.balance",":id") }}';
                     url: url.replace(':id', accountId),
                     $.ajax({
                         type: "GET",
                         url: url.replace(':id', accountId),
                         success: function (resp) {
                             $('#balance').show();
                             document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                             $('#amount_balance').val(resp);
                             console.log(resp);
                         }, // success end
                         error: function (error) {
                             // location.reload();
                         } // Error
                     })
                 }

                 else {
                     $('#balance').hide();
                 }
             }

        }

    </script>
@endpush

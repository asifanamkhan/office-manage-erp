@push('css')
@endpush
<form action="{{ route('admin.loan-return.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
      <input type="hidden" name="loan_id" id="loan_id" value="{{$loan->id}}">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="return_title"><b>Return Title</b><span class="text-danger">*</span></label>
                <input type="text" name="return_title" id="return_title"class="form-control"value="{{ old('return_title') }}" placeholder="Enter Return Title...">
                @if ($errors->has('return_title'))
                    <span class="alert text-danger">
                        {{ $errors->first('return_title') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="loan_author_id"><b>Select Authority</b><span class="text-danger">*</span></label>
                <input type="text" class="form-control" value="{{ $loan->author->name }}"readonly>
                <input type="hidden" class="form-control" id="loan_author_id" name="loan_author_id"value="{{ $loan->author->id }}">
                @if ($errors->has('loan_author_id'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_author_id') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="loan_amount"><b>Loan amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="" class="form-control" readonly value="{{ $loan_amount }}">
                @if ($errors->has('loan_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="return_amount"><b>Return amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="" class="form-control" readonly value="{{ $return_amount }}">
                @if ($errors->has('return_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('return_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="investment_due_amount"><b>Due amount</b><span class="text-danger">*</span></label>
            <input  type="text" name="investment_due_amount" id="investment_due_amount" class="form-control" readonly value="{{ $due }}">
                @if ($errors->has('investment_due_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('investment_due_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="loan_type"><b>Loan Type</b><span class="text-danger">*</span></label>
            <input type="text" class="form-control"
                    @if ($loan->loan_type == 2) value="Giving"
                    @else value="Taking" @endif readonly>
             <input type="hidden" class="form-control" id="loan_type" name="loan_type" value="{{ $loan->loan_type }}">
                @if ($errors->has('loan_type'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_type') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
                <select name="transaction_way" id="transaction_way" class="form-control"
                        onchange="expenseTransactionWay()">
                    <option value="" selected>-- Select --</option>
                    <option value="1">Cash</option>
                    <option value="2">Bank</option>
                </select>
                @if ($errors->has('transaction_way'))
                    <span class="alert text-danger">
                        {{ $errors->first('transaction_way') }}
                    </span>
                @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{ old('transaction_date') }}">
                @if ($errors->has('transaction_date'))
                    <span class="alert text-danger">
                        {{ $errors->first('transaction_date') }}
                    </span>
                @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" style="display: none">
            <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span></label>
                <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                    <option value="" selected>-- Select Bank Account --</option>
                    @foreach($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                            | {{ $bankAccount->account_number }}</option>
                    @endforeach
                </select>
                @if ($errors->has('account_id'))
                    <span class="alert text-danger">
                        {{ $errors->first('account_id') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" style="display: none">
            <label for="cheque_number"><b>Cheque Number</b></label>
                <input type="text" name="cheque_number" id="cheque_number" class="form-control"value="{{ old('cheque_number') }}" placeholder=" ...">
                @if ($errors->has('cheque_number'))
                    <span class="alert text-danger">
                     {{ $errors->first('cheque_number') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="amount_balance"><b>Return Amount</b><span class="text-danger">*</span><span class="text-info" id="balance" style="display: none"></span></label>
            <input type="hidden" value="" id="amount_balance">
            <input type="number" name="amount" id="amount" class="form-control"value="{{ old('amount') }}" placeholder="1200 ..." onkeyup="checkAmount(this)">
            @if ($errors->has('amount'))
                <span class="alert text-danger">
                    {{ $errors->first('amount') }}
                </span>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="note"><b>Note</b><span class="text-danger">*</span></label>
            <textarea name="note" id="description" rows="3" class="form-control " value="{{ old('note') }}"placeholder="Enter Note..."></textarea>
                @if ($errors->has('note'))
                    <span class="alert text-danger">
                       {{ $errors->first('note') }}
                    </span>
                @endif
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
        </div>
    </div>

</form>



@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js" integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace('description', {
            height:'100px',
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"] },
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links","groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo","groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

    $('#transaction_way').on('change', function () {
            $('#amount').val(0);
    })
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
                        $.ajax({
                            type: "GET",
                            url: url.replace(':id', accountId),
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
                    else {
                        $('#balance').hide();
                    }
                }
    }
    $('#amount').on('keyup', function () {
            if ($('#transaction_way').val()) {
                if($('#transaction_way').val() == 1){
                    checkAmount($('#amount').val());
                }else{
                    if($('#account_id').val()){
                        checkAmount($('#amount').val());
                    }else{
                        $('#amount').val(0);
                        alert('please select account');
                    }
                }

            } else {
                $('#amount').val(0);
                alert('please select transaction type first');
            }
    })
    function checkAmount(amount) {
            console.log($('#transaction_purpose').val());
            var amount = amount;
            var amountBalance = 0;
            var amountBalanceX = parseFloat($('#amount_balance').val()); //accblnce
            var invest_amount_balance = parseFloat($('#investment_due_amount').val()); //invst blnce

            if ($('#transaction_purpose').val() != 11) {
                if (!isNaN(amountBalanceX) && !isNaN(invest_amount_balance)) {
                    if (parseFloat(amountBalanceX) > parseFloat(invest_amount_balance)) {
                        amountBalance = invest_amount_balance;
                    } else {
                        amountBalance = amountBalanceX;
                    }

                    if (parseFloat(amountBalance) < parseFloat(amount)) {
                        swal({
                            title: `Alert?`,
                            text: "You have entered a wrong balance.",
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $('#amount').val(0);
                            }
                        });
                    }
                }
            } else {
                if (parseFloat(amountBalanceX) < parseFloat(amount)) {
                    swal({
                        title: `Alert?`,
                        text: "You don't have enough balance.",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#amount').val(0);
                        }

                    });
                }
            }
        }
    </script>
@endpush

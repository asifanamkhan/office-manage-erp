@extends('layouts.dashboard.app')

@section('title', 'Investment')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.investment.index') }}">Investment</a>
            </li>
        </ol>
        <a href="{{ route('admin.investment.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <form action="{{ route('admin.investment.update',$investment->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="investor_id"><b>Select Investor</b><span class="text-danger">*</span></label>
                                    <select name="investor_id" id="investor_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach($investors as $investor)
                                            <option value="{{ $investor->id }}"  {{($investor->id == $investment->investor_id) ? 'selected' : ''}}>{{ $investor->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('investor_id'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('investor_id') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_title"><b>Investment Title</b><span class="text-danger">*</span></label>
                                    <input type="text" name="transaction_title" id="transaction_title" class="form-control"value="{{$investment->transaction_title}}" placeholder="Enter Transaction_title...">
                                    @if ($errors->has('transaction_title'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_title') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{$investment->date}}">
                                    @if ($errors->has('transaction_date'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_date') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           value="{{ $investment->transaction_way == 1 ? 'Cash' : 'Bank' }}" readonly>
                                    <input type="hidden" name="transaction_way" id="transaction_way"
                                           value="{{ $investment->transaction_way}}">
                                    @if ($errors->has('transaction_way'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('transaction_way') }}
                                        </span>
                                    @endif
                            </div>
                            @if($investment->transaction_way == 2)
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" >
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span></label>
                                    <select name="account_id" id="account_id" class="form-control">
                                        <option value="" selected>-- Select Bank Account --</option>
                                        @foreach($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}" @foreach ($investment->transaction as $transaction)
                                            {{$transaction->account_id ==  $bankAccount->id ? 'selected' : ''}}
                                            @endforeach  >{{ $bankAccount->name }}
                                                | {{ $bankAccount->account_number }}  </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('account_id'))
                                        <span class="alert text-danger">
                                            {{ $errors->first('account_id') }}
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way" >
                                <label for="cheque_number"><b>Cheque Number</b></label>
                                    <input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder=" ..." @foreach ($investment->transaction as $transaction)
                                    value="{{$transaction->cheque_number}}"@endforeach>
                                    @if ($errors->has('cheque_number'))
                                        <span class="alert text-danger">
                                          {{ $errors->first('cheque_number') }}
                                        </span>
                                    @endif
                            </div>
                            @endif
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="amount_balance"><b>Amount</b><span class="text-danger">*</span></label>
                                <input type="hidden" value="" id="amount_balance">
                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} has-feedback">
                                    <input type="number" name="amount" id="amount" class="form-control"
                                           value="{{$investment->amount}}" placeholder="1200 ...">
                                    @if ($errors->has('amount'))
                                        <span class="alert text-danger">
                                           {{ $errors->first('amount') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <label for="note"><b>Note</b><span class="text-danger">*</span></label>
                                <textarea name="note" id="note" rows="3" class="form-control " value="{{ old('note') }}"placeholder="Enter Note..."></textarea>
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
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>


@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    CKEDITOR.replace('note', {
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
            console.log(transaction_way);
            if (transaction_way == 2) {
                $('.bank-way').show();
            } else {
                $('.bank-way').hide();
            }
        };
</script>
@endpush

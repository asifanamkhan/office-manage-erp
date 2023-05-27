<span class="badge rounded-pill bg-success fs-6"><span class="">Total Budget :</span> {{$total_budget}}</span>
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.budget-receipt.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
        <input type="hidden" name="total_budget" id="total_budget" value="{{$total_budget}}">
       <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="amount-type"> <b> Amount Type</b><span class="text-danger">*</span></label>
            <select name="amount_type" id="amount-type" class="form-select" onchange="expenseTransactionWay()">
                <option value=""  selected>--Select Amount Type--</option>
                <option value="1" >Full</option>
                <option value="2" >Partial</option>
            </select>
            @error('amount_type')
                <span class="alert text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
       <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="receipt-amount"> <b>Receipt Amount </b><span class="text-danger">*</span></label>
            <input type="number"  id="receipt-amount" value="{{ old('receipt-amount')}}" class="form-control " name="receipt_amount" placeholder="Enter Receipt Amount" onkeyup="checkAmount()" >
            @error('receipt_amount')
            <span class="alert text-danger" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 transaction" >
            <label for="transaction_way"><b>Transaction Type</b><span
                    class="text-danger">*</span></label>
            <select name="transaction_way" id="transaction_way" class="form-control"
                onchange="expenseTransactionWay()">
                <option value="" selected>-- Select --</option>
                <option value="1">Cash</option>
                <option value="2">Bank</option>
            </select>
            @if ($errors->has('transaction_way'))
                <span class="alert text-danger">
                    <strong>{{ $message }}</strong>
                </span>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way " style="display: none">
            <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span></label>
            <select name="account_id" id="account_id" class="form-control" >
                <option value="" selected>-- Select Bank Account --</option>
                @foreach ($bankAccounts as $bankAccount)
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
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way " style="display: none">
            <label for="cheque_number"><b>Cheque Number</b></label>
            <input type="text" name="cheque_number" id="cheque_number"
                class="form-control"value="{{ old('cheque_number') }}" placeholder=" ...">
            @if ($errors->has('cheque_number'))
                <span class="alert text-danger">
                    {{ $errors->first('cheque_number') }}
                </span>
            @endif
        </div>
        <div class="form-group col-12 my-3">
            <label><b>Add Document</b></label>
            <input class="form-check-input " type="checkbox" id="add-document-check-btn" value="">
        </div>
        <div class="col-sm-6 mb-2 document" style="display:none">
            <div class="form-group">
                <label for="document_title"><b>Document Title</b></label>
                <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >
                @error('document_title')
                    <span class="text-danger" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group col-sm-5 mb-2 document mt-4" style="display:none">
            <input type="file" id="document" data-height="25"data-default-file=""class="dropify form-control " name="documents[]">
        </div>
        <div class="col-sm-1 document" style="display:none">
            <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                X
            </button>
        </div>
        <div class="documentRow document" style="display:none">

        </div>
        <div class="col-sm-4 document " style="display:none">
            <button type="button" style="margin-top:0px" id="add_document"
                    class="btn btn-sm btn-success text-white">
                + Add Document
            </button>
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="receiptdescription" rows="10" cols="40" class="form-control description" value="{{ old('description') }}" placeholder="Description..."></textarea>
                @error('description')
                   <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
        </div>
        <div class="form-group my-3">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace(receiptdescription,{
            toolbarGroups: [
                {
                "name": "styles",
                "groups": ["styles"]
                },
                {
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                },

                {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                },
                {
                    "name": "document",
                    "groups": ["mode"]
                },
                {
                    "name": "links",
                    "groups": ["links"]
                },
                {
                    "name": "insert",
                    "groups": ["insert"]
                },

                {
                    "name": "undo",
                    "groups": ["undo"]
                },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })
         //document append
         $(document).ready(function () {
            var wrapper = $(".documentRow");
            var x = 0;
            $("#add_document").click(function () {
                    x++;
                    $(wrapper).append('<div class="row mt-2 document-table-tr" id="document-table-tr-' + x + '">' +
                                            '<div class="col-sm-6 mb-2 document">'+
                                                ' <div class="form-group">'+
                                                        '<input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >'+
                                                        '@error("document_title")'+
                                                            '<span class="text-danger" role="alert">'+
                                                            ' <p>{{ $message }}</p>'+
                                                            '</span>'+
                                                    ' @enderror'+
                                                    '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-5 mb-2">'+
                                                '<div class="form-group">'+
                                                    '<input type="file" id="document" data-height="25"data-default-file=""class="dropify form-control " name="documents[]">'+
                                                '</div>'+
                                            '</div>'+
                                            '<div class="col-sm-1 ">' +
                                                '<button type="button"  class=" btn btn-sm btn-danger " onclick="documentRemove(' + x + ')">' +
                                                'X' +
                                                '</button>' +
                                            '</div>'+
                                        '</div>' );
                                        $('.dropify').dropify();

            });
        });
        $(document).on("click", "#add-document-check-btn", function () {
            if ($('#add-document-check-btn').is(":checked"))
                $(".document").show();
            else
                $(".document").hide();
        });
        function documentRemove(id) {
            $('#document-table-tr-' + id).remove();
        }
        function expenseTransactionWay() {
            var transaction_way = $("#transaction_way").val();
            if (transaction_way == 2) {
                $('.bank-way').show();
            } else {
                $('.bank-way').hide();
                var url = '{{ route('admin.account.bank.account.balance', ':id') }}';
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', 0),
                    success: function(resp) {
                        $('#balance').show();
                        document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                        $('#amount_balance').val(resp);
                        document.getElementById('amount').max = resp;
                    }, // success end
                    error: function(error) {
                        location.reload();
                    } // Error
                })

            }
        };
        function checkAmount(){
            var totalAmount = $('#total_budget').val();
            var receiptAmount = $('#receipt-amount').val();
            if(parseFloat(totalAmount) < parseFloat(receiptAmount)){
                $('#receipt-amount').val(0);
                swal({
                        title: `Please Enter Correct Amount`,
                        text: receiptAmount + "  Greater Than " + totalAmount,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#receipt-amount').val(0);
                        }
                    });
            }
        }
    </script>
@endpush

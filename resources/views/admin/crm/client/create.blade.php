@extends('layouts.dashboard.app')

@section('title', 'Client ')

@section('breadcrumb')

    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Client </span>
            </li>
        </ol>
        <div>
            <p class="text-warning">Client default password is "client". </p>
        </div>
        <a href="{{ route('admin.crm.client.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.crm.client.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_name"><b>Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="client_name" id="client_name"
                                       class="form-control @error('client_name') is-invalid @enderror"
                                       value="{{ old('client_name') }}" placeholder="Enter Name">
                                @error('client_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option>--Select Status--</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="primary_phone"><b>Primary Phone</b><span
                                        class="text-danger">*</span></label>
                                <input type="text" name="primary_phone" id="primary_phone"
                                       class="form-control @error('primary_phone') is-invalid @enderror"
                                       value="{{ old('primary_phone') }}"
                                       placeholder="Enter Primary Phone">
                                @error('primary_phone')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_type"><b>Client Type</b><span class="text-danger">*</span></label>
                                <select name="client_type" id="client_type"
                                        class="form-select @error('client_type') is-invalid @enderror">
                                    <option selected>--Select Client Type--</option>
                                    @foreach ($ClientTypes as $clienttype )
                                        <option value="{{$clienttype->id}}">{{$clienttype->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_type_priority"><b>Client Priority</b><span
                                        class="text-danger">*</span></label>
                                <select name="client_type_priority" id="client_type_priority"
                                        class="form-select ">
                                    <option>--Select Priority--</option>
                                    @foreach ($priorities as $key => $priority )
                                        <option value="{{$priority->id}}">{{$priority->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_type_priority')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="contact_through"><b>Contact Through</b><span
                                        class="text-danger">*</span></label>
                                <select name="contact_through" id="contact_through"
                                        class="form-select @error('contact_through') is-invalid @enderror">
                                    <option selected>--Select Contact Through--</option>
                                    @foreach ($ContactThrough as $contact_through )
                                        <option value="{{$contact_through->id}}">{{$contact_through->name}}</option>
                                    @endforeach
                                </select>
                                @error('contact_through')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="interested_on"><b>Interested On</b></label>
                                <select name="interested_on" id="interested_on"
                                        class="form-select @error('interested_on') is-invalid @enderror">
                                    <option selected>--Select Contact Through--</option>
                                    @foreach ($InterestedsOn as $InterestedOn )
                                        <option value="{{$InterestedOn->id}}">{{$InterestedOn->name}}</option>
                                    @endforeach
                                </select>
                                @error('interested_on')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="email"><b>Email</b><span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="Enter Email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="10" cols="40"
                                          class="form-control @error('description') is-invalid @enderror"
                                          value="{{ old('description') }}"
                                          placeholder="Description..."></textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        CKEDITOR.replace('description', {
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
        // client_typeData
        $(document).on('change', '#client_type', function () {
            var id = $("#client_type").val();
            var url = '{{ route('admin.crm.client.type.priority', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#client_type_priority').val(resp.priority);
                },
                error: function () {
                    location.reload();
                }
            });
        });
    </script>
@endpush

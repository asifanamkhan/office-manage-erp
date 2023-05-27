@extends('layouts.dashboard.app')

@section('title', 'Client Type')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.crm.client-type.index') }}">Client Type</a>
            </li>
            <li class="breadcrumb-item">
                <span>Update</span>
            </li>
        </ol>
        <a href="{{ route('admin.crm.client-type.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.crm.client-type.update', $ClientType->id) }}" enctype="multipart/form-data"
          method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="name"><b>Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $ClientType->name) }}" placeholder="Name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            {{-- client priority --}}
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="priority"><b>Client Priority</b><span class="text-danger">*</span></label>
                                <select name="priority" id="priority"
                                        class="form-select @error('priority') is-invalid @enderror">
                                    <option >--Select Priority--</option>
                                    @foreach ($priorities as $key => $priority )
                                        <option value="{{$priority->id}}" {{ $ClientType->priority == $priority->id ? 'selected' : '' }}>{{$priority->name}}</option>
                                    @endforeach
                                </select>
                                @error('priority')
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
                                    <option value="1" {{ $ClientType->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $ClientType->status == 0 ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Current Address</b></label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Description...">{{ old('description', $ClientType->description) }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
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
            toolbarGroups: [{
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
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>
@endpush

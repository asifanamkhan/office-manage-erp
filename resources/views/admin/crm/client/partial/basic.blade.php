@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
          integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }

        .dropify-wrapper {
            height: 180px;
            width: 180px;
        }
    </style>
@endpush

<form action="{{ route('admin.crm.client.update',$Client->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <input type="file" id="image" class="dropify form-control @error('image') is-invalid @enderror"
                   @if ($Client->image) data-default-file="{{ asset('img/client/' . $Client->image) }}"
                   @else
                       data-default-file="{{asset('img/no-image/noman.jpg')}}"
                   @endif name="image">
            @error('image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="client_name"> <b>Name</b><span class="text-danger">*</span></label>
            <input type="text" name="client_name" id="client_name"
                   class="form-control @error('client_name') is-invalid @enderror" value="{{$Client->name}}"
                   placeholder="Enter Name">
            @error('client_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="primary_phone"><b> Primary Phone </b><span class="text-danger">*</span></label>
            <input type="text" name="primary_phone" id="primary_phone"
                   class="form-control @error('primary_phone') is-invalid @enderror" value="{{$Client->phone_primary}}"
                   placeholder="Enter Primary Phone">
            @error('primary_phone')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone_secondary"><b>Secondary Phone</b></label>
            <input type="text" name="phone_secondary" id="phone_secondary"
                   class="form-control @error('phone_secondary') is-invalid @enderror"
                   value="{{$Client->phone_secondary}}" placeholder="Enter Secondary Phone">
            @error('phone_secondary')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="client_type"><b>Client Type</b><span class="text-danger">*</span></label>
            <select name="client_type" id="client_type" class="form-select @error('client_type') is-invalid @enderror">
                <option selected>--Select Client Type--</option>
                @foreach ($ClientTypes as $clienttype )
                    <option
                        value="{{$clienttype->id}}" {{$Client->client_type == $clienttype->id ? 'selected' : ''}}>{{$clienttype->name}}</option>
                @endforeach
            </select>
            @error('client_type')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="client_type_priority"><b>Client Priority</b><span class="text-danger">*</span></label>
            <select name="client_type_priority" id="client_type_priority" class="form-select ">
                <option>--Select Priority--</option>
                <option value="1" {{$Client->client_type_priority == 1 ? 'selected' : ''}}>First</option>
                <option value="2" {{$Client->client_type_priority == 2 ? 'selected' : ''}}>Second</option>
                <option value="3" {{$Client->client_type_priority == 3 ? 'selected' : ''}}>Third</option>
            </select>
            @error('client_type_priority')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="contact_through"><b>Contact Through</b><span class="text-danger">*</span></label>
            <select name="contact_through" id="contact_through"
                    class="form-select @error('contact_through') is-invalid @enderror">
                <option selected>--Select Contact Through--</option>
                @foreach ($ContactThrough as $contact_through )
                    <option
                        value="{{$contact_through->id}}" {{$Client->contact_through == $contact_through->id ? 'selected' : ''}}>{{$contact_through->name}}</option>
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
                    <option
                        value="{{$InterestedOn->id}}" {{$Client->interested_on == $InterestedOn->id? 'selected' : ''}}>{{$InterestedOn->name}}</option>
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
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{$Client->email }}" placeholder="Enter Email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="10" cols="40"
                      class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                      placeholder="Description...">{{$Client->description}}</textarea>
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
</form>
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
            integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function () {
            $('.dropify').dropify();
        });
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

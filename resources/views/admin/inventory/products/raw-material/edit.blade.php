@extends('layouts.dashboard.app')

@section('title', 'Edit Product Category')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
        integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;

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
             <a href="{{route('admin.raw-material.index')}}">Raw Material</a>
            </li>
        </ol>
        <a href="{{route('admin.raw-material.index')}}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.raw-material.update', $rawMaterial->id )}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                <label for="">Name</label>
                               <input name="name" class="form-control  @error('name') is-invalid @enderror" value="{{$rawMaterial->name}}" type="text" placeholder="Name">
                               @error('name')
                               <span class="alert text-danger" role="alert">
                                   <strong>{{ $message }}</strong>
                               </span>
                              @enderror
                            </div>
                                <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                    <label for="">Select Product</label>
                                    <select class="form-select  @error('product_id') is-invalid @enderror" name="product_id" id="">
                                        <option {{ $rawMaterial->product_id== 1 ? 'selected': '' }} value="1">p name</option>    
                                        <option  {{ $rawMaterial->product_id== 2 ? 'selected': '' }}  value="2">p name2</option>    
                                    </select>     
                                    @error('product_id')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror                               
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-6 mb-2">  
                                    <label for="">Select Wirehouse</label>
                                    <select class="form-select  @error('wirehouse_id') is-invalid @enderror" name="wirehouse_id" id=""> 
                                       @foreach ($warehouses as $warehouse )
                                       <option  {{ $rawMaterial->wirehouse_id== $warehouse->id ? 'selected': '' }}   value="{{$warehouse->id}}">{{$warehouse->name}}</option> 
                                       @endforeach
                                          
                                   </select>
                                   @error('wirehouse_id')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror 
                                </div>


                                <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                    <label for="">Select Type</label>
                                    <select class="form-select @error('type') is-invalid @enderror" name="type" id="">
                                       <option  {{ $rawMaterial->type== 1 ? 'selected': '' }} value="1">{{$rawMaterial->type}} type</option>    
                                       <option  {{ $rawMaterial->type== 2 ? 'selected': '' }} value="2">{{$rawMaterial->type}} type</option>    
                                   </select>
                                   @error('type')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                   @enderror 
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                    <label for="">Select Unit</label>
                                    <select class="form-select @error('unit_id') is-invalid @enderror" name="unit_id" id="">
                                        @foreach ($units as $unit)
                                        <option {{ $unit->id== $warehouse->unit_id ? 'selected': '' }} value="{{$unit->id}}">{{$unit->name}}</option>   
                                        @endforeach
                                   </select>
                                   @error('unit_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                   @enderror
                                </div>
                            

                            <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                <label for="">Price</label>
                               <input name="base_price" class="form-control  @error('base_price') is-invalid @enderror" value="{{$rawMaterial->base_price}}" type="text" placeholder="Price">
                               @error('base_price')
                               <span class="alert text-danger" role="alert">
                                   <strong>{{ $message }}</strong>
                               </span>
                              @enderror
                            </div>
                            <div class="form-group col-12 col-sm-6 col-md-6 mb-2">
                                <label for="">Select Status</label>
                                <select class="form-select @error('base_price') is-invalid @enderror"  name="status" id="">
                                   <option {{ $rawMaterial->status== 1 ? 'selected': '' }} value="1">Active</option>    
                                   <option {{ $rawMaterial->status== 1 ? 'selected': '' }} value="0">In-active</option>    
                               </select>
                               @error('status')
                               <span class="alert text-danger" role="alert">
                                   <strong>{{ $message }}</strong>
                               </span>
                              @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                               <label for="description"><b>Description</b></label>
                               <textarea name="description" id="description" rows="3"
                                   class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                   placeholder="Description...">{{$rawMaterial->description}}</textarea>
                               @error('description')
                                   <span class="alert text-danger" role="alert">
                                       <strong>{{ $message }}</strong>
                                   </span>
                               @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="mb-5"></div>


@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });

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

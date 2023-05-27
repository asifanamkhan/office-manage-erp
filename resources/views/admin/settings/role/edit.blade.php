@extends('layouts.dashboard.app')

@section('title', 'Edit Role')

@push('css')
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{ route('admin.settings.role.index') }}">Edit Role</a>
            </li>

        </ol>
        <a class="btn btn-sm btn-dark" href="{{ route('admin.settings.role.index') }}">
            Back to list
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <form class="forms-sample" method="POST" action="{{ route('admin.settings.role.update', $role->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group mb-2">
                            <label for="role">Role name</label>
                            <input type="text" class="form-control is-valid" id="role" name="name"
                                   value="{{ $role->name }}" placeholder="Insert Role">
                            <input type="hidden" name="id" value="{{$role->id}}" >
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="form-group">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description"  class="form-control " id="description" cols="100" rows="3" placeholder="Enter Role Details">{{$role->description}}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 m7-2 ">
                            <h6 for="exampleInputEmail3"><strong>Assign Permission</strong></h6>
                            <div class="col-sm-4">
                                 <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="all_permission_checkbox" value="">
                                        <span class="custom-control-label">All Permissions</span>
                                    </label>
                                 </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-4 mt-2">
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th >Name</th>
                                    <th >Create</th>
                                    <th >Update</th>
                                    <th >Delete</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Employee</td>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions-" name="permissions[]"
                                        value="5"  @foreach ($role->permissions as $rPermission){{ $rPermission->id == 5 ? 'checked' : '' }}@endforeach>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions-" name="permissions[]"
                                        value="6" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 6 ? 'checked' : '' }}@endforeach>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions-" name="permissions[]"
                                        value="7" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 7 ? 'checked' : '' }}@endforeach>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>Client</td>

                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="2" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 2 ? 'checked' : '' }}@endforeach>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions-" name="permissions[]"
                                        value="3" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 3 ? 'checked' : '' }}@endforeach>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                        id="permissions-" name="permissions[]"
                                        value="4" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 4 ? 'checked' : '' }}@endforeach>
                                    </td>

                                  </tr>
                                </tbody>
                              </table>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    $('#all_permission_checkbox').on('click', function(){
        if($(this).is(':checked')){
            $('input[type=checkbox]').prop('checked', true);
        }else{
            $('input[type=checkbox]').prop('checked', false);
        }
    });
</script>
@endpush

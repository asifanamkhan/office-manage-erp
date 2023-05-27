@extends('layouts.dashboard.app')

@section('title', 'Create Role')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{ route('admin.settings.role.create') }}">Create Role</a>
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
                <form class="forms-sample" method="POST" action="{{ route('admin.settings.role.store') }}">
                    @csrf
                    <div class="col-sm-12 mb-2">
                        <div class="form-group">
                            <label for="role"><b>Role</b><span class="text-danger">*</span></label>
                            <input type="text" class="form-control is-valid" id="role" name="name"
                                placeholder="Role Name" required >
                        </div>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="form-group">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" class="form-control " id="description" cols="100" rows="3"placeholder="Enter Role Details"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-4">
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

                    <div class="col-sm-12 mb-4 mt-4">
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
                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions-" name="permissions[]"
                                    value="5">
                                </td>
                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions-" name="permissions[]"
                                    value="6">
                                </td>
                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions-" name="permissions[]"
                                    value="7">
                                </td>
                              </tr>
                              <tr>
                                <td>Client</td>

                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions" name="permissions[]"
                                    value="2">
                                </td>
                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions-" name="permissions[]"
                                    value="3">
                                </td>
                                <td><input type="checkbox" class="form-check-input"
                                    id="permissions-" name="permissions[]"
                                    value="4">
                                </td>

                              </tr>
                            </tbody>
                          </table>
                    </div>
                    <div class="form-group mb-4 text-center" >
                        <button title="Submit Button" type="submit" class="btn btn-primary btn-rounded">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('#all_permission_checkbox').on('click', function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true);
            } else {
                $('input[type=checkbox]').prop('checked', false);
            }
        });
    </script>
@endpush

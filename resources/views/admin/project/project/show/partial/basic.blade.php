<form action="{{ route('admin.projects.update',$project->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_code"><b>Project Code</b><span class="text-danger">*</span></label>
              <input type="text" name="project_code" id="project_code"class="form-control @error('project_code') is-invalid @enderror"value="{{$project->project_code}}" placeholder="Enter Project Code" readonly>
              @error('project_code')
                  <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_title"><b>Project Title</b><span class="text-danger">*</span></label>
              <input type="text" name="project_title" id="project_title"class="form-control @error('project_title') is-invalid @enderror"value="{{$project->project_title}}" placeholder="Enter Project Title">
              @error('project_title')
                  <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_category"><b>Project Category</b><span class="text-danger">*</span></label>
              <select name="project_category" id="project_category"class="form-select @error('project_category') is-invalid @enderror">
                  <option value="" >--Select Project Category--</option>
                    @foreach($projectCategory as $category)
                        <option value="{{$category->id}}"@if($category->id == $project->project_category) {{ "selected" }}@endif>
                            {{$category->name}}
                        </option>
                    @endforeach
              </select>
              @error('project_category')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_type"><b>Project Type</b><span class="text-danger">*</span></label>
              <select name="project_type" id="project_type"class="form-select @error('project_type') is-invalid @enderror" onclick="projectType()">
                  <option value="" selected>--Select Project Type--</option>
                  <option value="1" {{$project->project_type == 1 ? 'selected' : ''}} >Own Project </option>
                  <option value="2" {{$project->project_type == 2 ? 'selected' : ''}}>Client Project</option>
                  <option value="3" {{$project->project_type == 3 ? 'selected' : ''}}>Public Project </option>
              </select>
              @error('project_type')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2 client" @if($project->project_type != 2 ) style="display:none"  @endif>
              <label for="client"><b>Client</b></label>
              <select name="client[]" id="client" class="form-select @error('client') is-invalid @enderror" multiple="multiple">
                  <option value="">--Select Client--</option>
                @if ($project->project_type == 2)
                    @foreach($clients as $client)
                    <option value="{{$client->id}}"@if(in_array($client->id, $client_ids  )) {{ "selected" }}@endif>
                        {{$client->name}}
                    </option>
                    @endforeach
                @endif
              </select>
              @error('client')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="project_priority"><b>Project Priority</b><span class="text-danger">*</span></label>
              <select name="project_priority" id="project_priority" class="form-select @error('project_priority') is-invalid @enderror">
                  <option value="" selected>--Select Project Priority--</option>
                  <option value="1" {{$project->project_priority == 1 ? 'selected' : ''}} >First</option>
                  <option value="2" {{$project->project_priority == 2 ? 'selected' : ''}} >Second</option>
                  <option value="3" {{$project->project_priority == 3 ? 'selected' : ''}}>Third</option>
              </select>
              @error('project_priority')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
              <label for="status"><b>Status</b><span class="text-danger">*</span></label>
              <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                  <option>--Select Status--</option>
                  <option value="1" {{$project->status == 1 ? 'selected' : ''}} >Up Coming</option>
                  <option value="2" {{$project->status == 2 ? 'selected' : ''}}>On Going</option>
                  <option value="3" {{$project->status == 3 ? 'selected' : ''}}>Complete</option>
                  <option value="4" {{$project->status == 4 ? 'selected' : ''}}>Cancel</option>
              </select>
              @error('status')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class="form-group col-12 mb-2">
              <label for="description"><b>Description</b></label>
              <textarea name="description" id="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                         placeholder="Description...">{{$project->description}}</textarea>
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


</form>
<div class="mb-5"></div>
@push('script')
    <script>
        ckEditor('description');
        $('#project_category').select2({
            ajax: {
                url: '{{route('admin.projects.category.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });
        $('#client').select2({
            placeholder:'Select Client',
                ajax: {
                    url: '{{route('admin.crm.client.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    value: item.id,
                                    id: item.id,
                                }
                            })
                        };
                    }
                }
        });

        function projectType() {
            var projectType = $("#project_type").val();
            if (projectType == 2) {
                $('.client').show();
            } else {
                $('.client').hide();
                $('#client').val('');
            }
        };

    </script>
@endpush

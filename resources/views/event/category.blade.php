@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Event</li>
        <li class="active">{{ isset($category_data) ? "Edit" : "Add" }} Category</li>
    </ol>
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success" id="successMessage">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" id="errorMessage">
                    {{ session('error') }}
                </div>
            @endif
            <div class="{{ isset($category_data) ? 'col-md-8' : 'col-md-6' }}">
                <div class="panel">
                    <div class="panel-heading nopaddingbottom">
                        <h4 class="panel-title">{{ isset($category_data) ? "Edit" : "Add" }} Category</h4>
                    </div>
                    <div class="panel-body category_container">
                        <hr>
                        <form action="{{ url('insert-event-category') }}" method="post" class="form-horizontal">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ isset($category_data) ? $category_data->id : '' }}">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Category Name <span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', isset($category_data) ? $category_data->name : '') }}"
                                           class="form-control" 
                                           placeholder="Enter Category Name">
                                    @error('name')
                                        <label class="error" for="name">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Active</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="isActive" 
                                               value="1" 
                                               {{ (isset($category_data) && $category_data->isActive) || !isset($category_data) ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button type="submit" class="btn btn-success btn-quirk btn-wide mr5" name="submit">
                                        Save
                                    </button>
                                    <a href="{{ route('event-category-list') }}"
                                       class="btn btn-success btn-quirk btn-wide mr5">Cancel</a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                        </form>
                    </div>
                </div>
            </div>
            @if(!isset($category_data))
                <div class="col-md-6">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="category_list" class="table table-hover table-borderednomargin nomargin">
                                <thead>
                                    <tr>
                                        <th style="width:60%;">Category Name</th>
                                        <th style="width:20%;">Status</th>
                                        <th style="width:20%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($category_data_array as $value)
                                        <tr>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->isActive ? "Active" : "Inactive" }}</td>
                                            <td>
                                                <span class="editicn">
                                                    <a href="{{ url('edit-event-category/'.$value->id) }}">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                                <span class="deleteicn">
                                                    <a onclick="javascript:deleteEventCategory({{ $value->id }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @extends('common.footer')
    
    @section('jquerysection')
        <script type="text/javascript">
            function deleteEventCategory(category_id) {
                if (category_id != "") {
                    if (confirm("Are you sure want to delete category?")) {
                        window.location = '{{ url("delete-event-category") }}' + "/" + category_id;
                    }
                } else {
                    alert("Category id not found");
                }
            }
        </script>
    @endsection
@endsection
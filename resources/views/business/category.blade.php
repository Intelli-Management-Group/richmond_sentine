@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Business</li>
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
                        <form action="{{ url('insert-business-category') }}" method="post" class="form-horizontal">
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
                                <label class="col-sm-4 control-label">Parent category</label>
                                <div class="col-sm-5">
                                    <div class="parent_category_container">
                                        <ul class="tree">
                                            @foreach (($category_data_array ?? []) as $key => $value)
                                                <li class="has">
                                                    <input type="radio" 
                                                           name="parentId[]" 
                                                           value="{{ $key }}"
                                                           {{ old('parentId.0', isset($category_data) ? $category_data->parentId : '') == $key ? 'checked' : '' }}>
                                                    <label>{{ $value['name'] }}</label>
                                                    @if (count($value['child_list']) > 0)
                                                        <ul>
                                                            @foreach ($value['child_list'] as $ckey => $cvalue)
                                                                <li class="has">
                                                                    <input type="radio" 
                                                                           name="parentId[]" 
                                                                           value="{{ $ckey }}"
                                                                           {{ old('parentId.0', isset($category_data) ? $category_data->parentId : '') == $ckey ? 'checked' : '' }}>
                                                                    <label>{{ $cvalue['name'] }}</label>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Actual category</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="isActualCategory" 
                                               value="1"
                                               {{ isset($category_data) && $category_data->isActualCategory ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Active</label>
                                <div class="col-sm-2">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="isActive" 
                                               value="1"
                                               {{ !isset($category_data) || $category_data->isActive ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button class="btn btn-success btn-quirk btn-wide mr5" name="submit">Save</button>
                                    <a href="{{ route('business-category-list') }}"
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
                                    <th style="width:60%;">Category Name</th>
                                    <th style="width:20%;">Action</th>
                                </thead>
                                <tbody>
                                    @forelse ($category_data_array as $key => $value)
                                        <tr data-id="{{ $key }}" 
                                            data-parent="{{ $value['parent_id'] }}" 
                                            data-level="{{ $value['level'] }}">
                                            <td data-column="name" 
                                                class="{{ count($value['child_list']) > 0 ? '' : 'padding_left40' }}">
                                                {{ $value["name"] }}
                                            </td>
                                            <td>
                                                <span class="editicn">
                                                    <a href="{{ url('edit-business-category/'.$key) }}">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                                <span class="deleteicn">
                                                    <a onclick="javascript:deleteBusinessCategory({{ $key }})">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>
                                        @foreach ($value["child_list"] as $ckey => $cvalue)
                                            <tr data-id="{{ $ckey }}" 
                                                data-parent="{{ $cvalue['parent_id'] }}" 
                                                data-level="{{ $cvalue['level'] }}">
                                                <td data-column="name" class="padding_left65">
                                                    {{ $cvalue["name"] }}
                                                </td>
                                                <td>
                                                    <span class="editicn">
                                                        <a href="{{ url('edit-business-category/'.$ckey) }}">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                    <span class="deleteicn">
                                                        <a onclick="javascript:deleteBusinessCategory({{ $ckey }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
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
        <script src="{{ asset('ui/js/javascript.js') }}"></script>
        <script type="text/javascript">
            $(document).on('click', '.tree label', function(e) {
                $(this).next('ul').fadeToggle();
                e.stopPropagation();
            });
            $('.tree ul').fadeIn();

            function deleteBusinessCategory(category_id) {
                if (category_id != "") {
                    if (confirm("Are you sure want to delete category?")) {
                        window.location = '{{ url("delete-business-category") }}' + "/" + category_id;
                    }
                } else {
                    alert("Category id not found");
                }
            }
        </script>
    @endsection
@endsection
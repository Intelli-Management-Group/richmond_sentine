@extends('common.header')
@extends('common.nav')
@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Add Category </li>
</ol>
<div class="row">
    <div class="col-md-12">
        @if(session()->has('success'))
        <div class="alert alert-success" id="successMessage">
            {!! session('success') !!}
        </div>
        @endif @if(session()->has('error'))
        <div class="alert alert-danger" id="errorMessage">
            {!! session('error') !!}
        </div>
        @endif
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-heading nopaddingbottom">
                    <h4 class="panel-title">Add Category</h4>
                </div>
                <div class="panel-body category_container">
                    <hr>
                    <form class="form-horizontal" method="POST" action="{!! route('insert-category') !!}" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Category Name <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="name" class="form-control" placeholder="Enter Category Name" value="{!! old('name')  !!}" /> @if ($errors->has('name'))
                                <label class="error" for="name">{!! $errors->first('name') !!}</label> @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Parent category <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <div class="parent_category_container">
                                    <ul class="tree">
                                        @foreach (count($category_data_array) > 0 ? $category_data_array : array() as $key => $value)
                                        <li class="has">
                                            <input type="radio" name="parentId[]" value="{!! $key !!}" {!! ((old('parentId')[0] ?? null)==$key ? "checked" : "" ) !!}>
                                            <label>{!! $value['name'] !!}</label> @if (count($value['child_list']) > 0)
                                            <ul>
                                                @foreach (count($value['child_list']) > 0 ? $value['child_list'] : array() as $ckey => $cvalue)
                                                <li class="has">
                                                    <input type="radio" name="parentId[]" value="{!! $ckey !!}" {!! ((old('parentId')[0] ?? null)==$ckey ? "checked" : "" ) !!}>
                                                    <label>{!! $cvalue['name'] !!}</label> @if (count($cvalue['child_list'])
                                                    > 0)
                                                    <ul>
                                                        @foreach (count($cvalue['child_list']) > 0 ? $cvalue['child_list'] : array() as $lkey => $lvalue)
                                                        <li>
                                                            <input type="radio" name="parentId[]" value="{!! $lkey !!}" {!! ((old('parentId')[0] ?? null)==$lkey ? "checked" : "" ) !!}>
                                                            <label>{!! $lvalue['name'] !!}</label>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @if ($errors->has('parentId'))
                                <label class="error" for="parentId">{!! $errors->first('parentId') !!}</label> @endif
                            </div>
                        </div>
                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-4 control-label">Icon</label>
                            <div class="col-sm-5">
                                <div class="upload_container text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i> Upload Image</span>
                                        <input name="iconImage" id="iconImage" type="file" accept=".jpeg,.png,.jpg,.gif,.svg" /></div>
                                    <img class="thumbnail" id="thumbnail" /><br>
                                    <span>W 120px X H 120px</span>
                                    <label class="image_format_label">Please select (jpeg,png,jpg,gif,svg) image format</label>
                                </div>
                                <label id="iconImageError" class="error" for="iconImage">@if($errors->has('iconImage')){!! $errors->first('iconImage') !!} @endif</label>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Level <span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <select class="form-control" name="level">
                                    <option value="0" {!! (old('level')=="0" ? "selected" : "" ) !!}>0</option>
                                    <option value="1" {!! (old('level')=="1" ? "selected" : "" ) !!}>1</option>
                                    <option value="2" {!! (old('level')=="2" ? "selected" : "" ) !!}>2</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Actual category</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isActualCategory" {!! (old('isActualCategory') ? "checked" : "" ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Display in More</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isDisplayInMore" id="isDisplayInMore" {!! (old('isDisplayInMore') ? "checked" : "" ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group more_menu_index {!! (old('isDisplayInMore') ? " " : " hidden") !!}">
                            <label class="col-sm-4 control-label">Order index in more<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="more_section_index" class="form-control width70" value="{!! old('more_section_index') !!}" /> @if ($errors->has('more_section_index'))
                                <label class="error" for="heading">{!! $errors->first('more_section_index') !!}</label> @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Display in menu</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isDisplayInMenu" id="isDisplayInMenu" {!! (old('isDisplayInMenu') ? "checked" : "" ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group menu_section_index {!! (old('isDisplayInMenu') ? " " : " hidden ") !!}">
                            <label class="col-sm-4 control-label">Order index in menu<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="nav_menu_index" class="form-control width70" value="{!! old('nav_menu_index') !!}" /> @if ($errors->has('nav_menu_index'))
                                <label class="error" for="heading">{!! $errors->first('nav_menu_index') !!}</label> @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Display in app</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isDisplayInApp" {!! (old('isDisplayInApp') ? "checked" : "" ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Display in frontend</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isDisplayInFrontend" id="isDisplayInFrontend" {!! (old("isDisplayInFrontend") ? 'checked' : '' ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group frontend_index {!! (old('isDisplayInFrontend') ? '' : 'hidden') !!}">
                            <label class="col-sm-4 control-label">Order index in frontend<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="frontend_menu_index" class="form-control width70" value="{!! old('frontend_menu_index') !!}" /> @if ($errors->has('frontend_menu_index'))
                                <label class="error" for="heading">{!! $errors->first('frontend_menu_index') !!}</label> @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Coupon category</label>
                            <div class="col-sm-2">
                                <label class="switch">
                                    <input type="checkbox" name="isCouponCategory" {!! (old('isCouponCategory') ? "checked" : "" ) !!}>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button class="btn btn-success btn-quirk btn-wide mr5" name="submit" id="add_more" class="upload">Save</button>
                                <a href="{!! route('category-list') !!}" class="btn btn-success btn-quirk btn-wide mr5">Cancel</a>
                            </div>
                        </div>
                        <div class="clearfix"></div><br />
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="category_list" class="table table-hover table-borderednomargin nomargin">
                        <thead>
                            <th style="width:80%;">Category Name</th>
                            <th style="width:20%;">Action</th>
                        </thead>
                        <tbody>
                            @forelse ($category_data_array as $key => $value)
                            <tr data-id="{!! $key !!}" data-parent="{!! $value['parent_id'] !!}" data-level="{!! $value['level'] !!}">
                                <td data-column="name" class="{!! (count($value['child_list'])> 0) ? '' : 'padding_left40' !!}">{!! $value["name"] !!}</td>
                                <td>
                                    <span class="editicn">
                                        <a href="{!! url('edit-category').'/'.$key !!}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    </span>
                                    <span class="deleteicn">
                                        <a onclick="javascript:deleteCategory({!! $key !!})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </span></td>
                            </tr>
                            @foreach (count($value["child_list"]) > 0 ? $value["child_list"] : array() as $ckey => $cvalue)
                            <tr data-id="{!! $ckey !!}" data-parent="{!! $cvalue['parent_id'] !!}" data-level="{!! $cvalue['level'] !!}">
                                <td data-column="name" class="padding_left65">{!! $cvalue["name"] !!}</td>
                                <td><span class="editicn"><a href="{!! url('edit-category').'/'.$ckey !!}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
                                    <span class="deleteicn"><a onclick="javascript:deleteCategory({!! $ckey !!})"><i class="fa fa-trash" aria-hidden="true"></i></a></span></td>
                            </tr>
                            @foreach (count($cvalue["child_list"]) > 0 ? $cvalue["child_list"] : array() as $lkey => $lvalue)
                            <tr data-id="{!! $lkey !!}" data-parent="{!! $lvalue['parent_id'] !!}" data-level="{!! $lvalue['level'] !!}">
                                <td data-column="name" class="padding_left95">{!! $lvalue["name"] !!}</td>
                                <td><span class="editicn"><a href="{!! url('edit-category').'/'.$lkey !!}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
                                    <span class="deleteicn"><a onclick="javascript:deleteCategory({!! $lkey !!})"><i class="fa fa-trash" aria-hidden="true"></i></a></span></td>
                            </tr>
                            @endforeach @endforeach @empty
                            <tr>
                                <td colspan="2">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




@extends('common.footer')
@section('jquerysection')
<script src="{{ asset('ui/js/javascript.js') }}"></script>
<script src="{{ asset('js/category.js').'?t='.Carbon\Carbon::now()->timestamp }}"></script>
<script type="text/javascript">
    function deleteCategory(category_id) {
        if (category_id != "") {
            if (confirm("Are you sure want to delete category?")) {
                window.location = '{!! url("delete-category") !!}' + "/" + category_id;
            }
        } else {
            alert("Category id not found");
        }
    }
</script>
@endsection

@endsection
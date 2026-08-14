@extends('common.header')
@extends('common.nav')
@section('content')
<ol class="breadcrumb breadcrumb-quirk">
    <li><a href="{!! url('dashboard') !!}"><i class="fa fa-home mr5"></i> Home</a></li>
    <li class="active">Edit Category </li>
</ol>
<div class="row">
    <div class="col-md-9">
        <div class="panel">
            <div class="panel-heading nopaddingbottom">
                <h4 class="panel-title">Edit Category</h4>
            </div>
            <div class="panel-body category_container">
                <hr>
                <form class="form-horizontal" method="POST" action="{!! route('insert-category') !!}" enctype="multipart/form-data">
                    <input type="hidden" name="category_id" value="{!! $category_data->id !!}" />
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Category Name <span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" name="name" class="form-control" placeholder="Enter Category Name" value="{!! $category_data->name  !!}" /> @if ($errors->has('name'))
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
                                        <input type="radio" name="parentId[]" value="{!! $key !!}" {!! ($category_data->parentId
                                        == $key ? "checked" : "") !!}>
                                        <label>{!! $value['name'] !!}</label> @if (count($value['child_list']) > 0)
                                        <ul>
                                            @foreach (count($value['child_list']) > 0 ? $value['child_list'] : array() as $ckey => $cvalue)
                                            <li class="has">
                                                <input type="radio" name="parentId[]" value="{!! $ckey !!}" {!! ($category_data->parentId
                                                == $ckey ? "checked" : "") !!}>
                                                <label>{!! $cvalue['name'] !!}</label> @if (count($cvalue['child_list'])
                                                > 0)
                                                <ul>
                                                    @foreach (count($cvalue['child_list']) > 0 ? $cvalue['child_list'] : array() as $lkey => $lvalue)
                                                    <li>
                                                        <input type="radio" name="parentId[]" value="{!! $lkey !!}" {!! ($category_data->parentId
                                                        == $lkey ? "checked" : "") !!}>
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
                                <img class="thumbnail" id="thumbnail" {!! ($category_data->iconImage != null ? "src='".("../uploads/category/". $category_data->iconImage)."'"
                                : "") !!}/><br>
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
                                <option value="0" {!! ($category_data->level == "0" ? "selected" : "") !!}>0</option>
                                <option value="1" {!! ($category_data->level == "1" ? "selected" : "") !!}>1</option>
                                <option value="2" {!! ($category_data->level == "2" ? "selected" : "") !!}>2</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Actual category</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isActualCategory" {!! $category_data->isActualCategory ? "checked" : "" !!}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Display in More</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isDisplayInMore" id="isDisplayInMore" {!! $category_data->isDisplayInMore ? "checked" : "" !!}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group more_menu_index {!! $category_data->isDisplayInMore ? " " : " hidden " !!}">
                        <label class="col-sm-4 control-label">Order index in more<span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" name="more_section_index" class="form-control width70" value="{!! $category_data->more_section_index !!}" /> @if ($errors->has('more_section_index'))
                            <label class="error" for="heading">{!! $errors->first('more_section_index') !!}</label> @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Display in menu</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isDisplayInMenu" id="isDisplayInMenu" {!! $category_data->isDisplayInMenu ? "checked" : "" !!}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group menu_section_index {!!  $category_data->isDisplayInMenu ? " " : " hidden " !!}">
                        <label class="col-sm-4 control-label">Order index in menu<span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" name="nav_menu_index" class="form-control width70" value="{!!  $category_data->nav_menu_index !!}" /> @if ($errors->has('nav_menu_index'))
                            <label class="error" for="heading">{!! $errors->first('nav_menu_index') !!}</label> @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Display in app</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isDisplayInApp" {!! $category_data->isDisplayInApp ? "checked" : "" !!}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Display in frontend</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isDisplayInFrontend" id="isDisplayInFrontend" {!! $category_data->isDisplayInFrontend ? "checked" : "" !!}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group frontend_index {!!  $category_data->isDisplayInFrontend ? " " : " hidden " !!}">
                        <label class="col-sm-4 control-label">Order index in frontend<span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="text" name="frontend_menu_index" class="form-control width70" value="{!!  $category_data->frontend_menu_index !!}" /> @if ($errors->has('frontend_menu_index'))
                            <label class="error" for="heading">{!! $errors->first('frontend_menu_index') !!}</label> @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Coupon category</label>
                        <div class="col-sm-2">
                            <label class="switch">
                                <input type="checkbox" name="isCouponCategory" {!! $category_data->isCouponCategory ? "checked" : "" !!}>
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
</div>

@extends('common.footer')
@section('jquerysection')
<script src="{{ asset('js/category.js').'?t='.Carbon\Carbon::now()->timestamp }}"></script>
@endsection

@endsection

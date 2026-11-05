@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('coupon-list') }}"> Coupon List</a></li>
        <li class="active">{{ isset($coupon_data) ? "Edit" : "Add" }} Coupon</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="panel coupon_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>{{ isset($coupon_data) ? "Edit" : "Add" }} Coupon</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form action="{{ url('insert-coupon') }}" method="post" enctype="multipart/form-data" class="form-horizontal" id="coupon_form">
                        @csrf
                        <input type="hidden" name="coupon_id" value="{{ isset($coupon_data) ? $coupon_data->id : '' }}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="name" value="{{ old('name', isset($coupon_data) ? $coupon_data->name : '') }}" class="form-control" placeholder="Enter name">
                                <label class="error" for="name"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Heading <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="heading" value="{{ old('heading', isset($coupon_data) ? $coupon_data->heading : '') }}" class="form-control" placeholder="Enter heading">
                                <label class="error" for="heading"></label>
                            </div>
                            <label class="col-sm-2 control-label">Category <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select name="categoryId" class="form-control">
                                    <option value="">Select category</option>
                                    @foreach($coupon_category_array as $key => $value)
                                        <option value="{{ $key }}" {{ old('categoryId', isset($coupon_data) ? $coupon_data->categoryId : '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <label class="error" for="categoryId"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Highlights <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea name="highlights" id="coupon_highlights" class="form-control" placeholder="Enter highlights" cols="4" rows="3">{{ old('highlights', isset($coupon_data) ? $coupon_data->highlights : '') }}</textarea>
                                <label class="error" for="highlights"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">FinePrint <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea name="finePrints" id="coupon_finePrints" class="form-control" placeholder="Enter fineprints" cols="4" rows="3">{{ old('finePrints', isset($coupon_data) ? $coupon_data->finePrints : '') }}</textarea>
                                <label class="error" for="finePrints"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Detail <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea name="detail" id="coupon_detail" class="form-control" placeholder="Enter detail" cols="4" rows="3">{{ old('detail', isset($coupon_data) ? $coupon_data->detail : '') }}</textarea>
                                <label class="error" for="detail"></label>
                            </div>
                        </div>

                        <h4>Company Information</h4>
                        <hr>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Company name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="companyName" value="{{ old('companyName', isset($coupon_data) ? $coupon_data->companyName : '') }}" class="form-control" placeholder="Enter company name">
                                <label class="error" for="companyName"></label>
                            </div>
                            <label class="col-sm-2 control-label">Company phone</label>
                            <div class="col-sm-3">
                                <input type="text" name="companyPhone" value="{{ old('companyPhone', isset($coupon_data) ? $coupon_data->companyPhone : '') }}" class="form-control" placeholder="Enter company phone">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Company email</label>
                            <div class="col-sm-4">
                                <input type="text" name="companyEmail" value="{{ old('companyEmail', isset($coupon_data) ? $coupon_data->companyEmail : '') }}" class="form-control" placeholder="Enter company email">
                            </div>
                            <label class="col-sm-2 control-label">Company address <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <textarea name="companyAddress" class="form-control" placeholder="Enter company address" cols="4" rows="3" style="resize:none;">{{ old('companyAddress', isset($coupon_data) ? $coupon_data->companyAddress : '') }}</textarea>
                                <label class="error" for="companyAddress"></label>
                            </div>
                        </div>

                        <h4>Daywise time setting</h4>
                        <hr>

                        <div>
                            @foreach($day_name_array as $key => $value)
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{ strtoupper($value) }}</label>
                                    <div class="col-sm-2">
                                        <div class="input-group clockpicker">
                                            <input type="text" name="starttime[{{ $key }}]" value="{{ old("starttime.$key", isset($coupon_data) ? $coupon_data->starttime[$key] ?? '' : '') }}" class="form-control">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group clockpicker">
                                            <input type="text" name="endtime[{{ $key }}]" value="{{ old("endtime.$key", isset($coupon_data) ? $coupon_data->endtime[$key] ?? '' : '') }}" class="form-control">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group">
                                <div class="col-sm-offset-2">
                                    <label class="error" for="coupontime"></label>
                                </div>
                            </div>
                        </div>

                        <h4>Images</h4>
                        <hr>

                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">Banner <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="browsebtn browsebtn-edit" style="margin-bottom: 0px;">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                        <input type="file" name="bannerImage[]" accept=".png,.jpg,.jpeg,.gif" multiple id="bannerImage">
                                    </div>
                                </div>
                                <label class="error" for="bannerImage" id="bannerImage_error"></label>
                            </div>
                            <div class="col-sm-3">
                                <span>W 800px X H 500px</span>
                                <label class="image_format_label">Please select (png,jpg,jpeg,gif) image format</label>
                            </div>
                        </div>

                        <div id="banner_image_container" class="col-sm-offset-2 mb20">
                            @if(isset($coupon_data))
                                @foreach($coupon_data->banner_image_array as $key => $b)
                                    <div class="file-preview col-sm-2 mr10" id="div_{{ $key }}">
                                        <div class="file-preview-thumbnails">
                                            <div class="file-preview-frame">
                                                <img src="{{ "../$b" }}" class="file-preview-image">
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <input type="button" class="btn btn-primary remove-file" value="Remove" data-id="{{ $key }}" onclick="javascript:deleteFile('div_{{ $key }}')" />
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="clearfix"></div>

                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">Thumbnail <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                        <input type="file" name="thumbnailImage" accept=".png,.jpg,.jpeg,.gif" id="thumbnailImage">
                                    </div>
                                    <img class="thumbnail" id="thumbnailImage_preview" @if(isset($coupon_data) && $coupon_data->thumbnailImage != "") src="{{ "../{$coupon_data->thumbnailImage}" }}" @endif />
                                    <br>
                                    <span>W 500px X H 500px</span>
                                    <label class="image_format_label">Please select (png,jpg,jpeg,gif) image format</label>
                                </div>
                                <label class="error" for="thumbnailImage" id="thumbnailImage_error"></label>
                            </div>
                        </div>

                        <h4>Price</h4>
                        <hr>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Original price <span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-addon">C$</span>
                                    <input type="number" name="originalPrice" value="{{ old('originalPrice', isset($coupon_data) ? $coupon_data->originalPrice : '') }}" class="form-control" min="0.00" step="0.01">
                                </div>
                                <label class="error" for="originalPrice"></label>
                            </div>
                            <label class="col-sm-2 control-label col-sm-offset-2">Discount price <span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <span class="input-group-addon">C$</span>
                                    <input type="number" name="discountPrice" value="{{ old('discountPrice', isset($coupon_data) ? $coupon_data->discountPrice : '') }}" class="form-control" min="0.00" step="0.01">
                                </div>
                                <label class="error" for="discountPrice"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Offer</label>
                            <div class="col-sm-4">
                                <input type="text" name="offerDetail" value="{{ old('offerDetail', isset($coupon_data) ? $coupon_data->offerDetail : '') }}" class="form-control" placeholder="Enter offer detail">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-2">
                                <button type="submit" class="btn btn-success btn-quirk btn-wide mr5" id="save_coupon">Save</button>
                                <a href="{{ url('coupon-list') }}" class="btn btn-success btn-quirk btn-wide">Cancel</a>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div><br>
                </div>
            </div>
        </div>
    </div>

    @include('common.filePreview')

    @extends('common.footer')

    @section('jquerysection')
        <script src="{{ asset('ui/lib/clockpicker/bootstrap-clockpicker.js') }}"></script>
        <script src="{{ asset('js/coupon.js') . '?t=' . now()->timestamp }}"></script>
        <link rel="stylesheet" href="{{ asset('css/couponzone.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/lib/clockpicker/bootstrap-clockpicker.css') }}">
        <link rel="stylesheet" href="{{ asset('ui/css/fileinput.css') . '?t=' . now()->timestamp }}">
    @endsection
@endsection
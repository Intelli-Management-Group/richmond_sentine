@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('business-list') }}"> Business List</a></li>
        <li class="active">Business {{ isset($business_data) ? "Edit" : "Add" }}</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="panel business_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Business {{ isset($business_data) ? "Edit" : "Add" }}</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form 
                        action="{{ url('insert-business') }}" 
                        method="POST" 
                        enctype="multipart/form-data" 
                        class="form-horizontal" 
                        id="business_form" 
                        data-url="insert-business"
                    >
                        @csrf
                        <input type="hidden" name="business_id" value="{{ isset($business_data) ? $business_data->id : '' }}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Featured</label>
                            <div class="col-sm-4 padding10">
                                <input type="checkbox" name="isFeatured" value="1" {{ old('isFeatured') ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">Logo <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="upload_container text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i> Upload Image</span>
                                        <input name="logo" id="logoImage" type="file" accept=".jpeg,.png,.jpg,.gif,.svg"/>
                                    </div>
                                    <img 
                                        class="thumbnail" 
                                        id="logoThumbnail" 
                                        {{ isset($business_data) && $business_data->logo ? "src='" . asset("uploads/business/logo/" . $business_data->logo) . "'" : "" }}
                                    /><br>
                                    <span>W 384px X H 280px</span>
                                    <label class="image_format_label">Please select (jpeg,png,jpg,gif,svg) image format</label>
                                </div>
                                <label id="logoError" class="error" for="logo">
                                    @error('logo') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Category <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select name="categoryId[]" multiple class="form-control">
                                    @foreach($categories as $id => $category)
                                        <option value="{{ $id }}" {{ in_array($id, old('categoryId', [])) ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                <label class="error" for="categoryId">
                                    @error('categoryId') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    class="form-control" 
                                    placeholder="Enter business name" 
                                    id="business_name"
                                >
                                <label class="error" for="name">
                                    @error('name') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Short code <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="shortCode" 
                                    value="{{ old('shortCode') }}" 
                                    class="form-control" 
                                    placeholder="Enter business short code"
                                >
                                <label class="image_format_label">It will display in browser url for business details, must be unique</label>
                                <label class="error" for="shortCode">
                                    @error('shortCode') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Address <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="address" 
                                    value="{{ old('address') }}" 
                                    class="form-control" 
                                    placeholder="Enter address"
                                >
                                <label class="error" for="address">
                                    @error('address') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">About <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea 
                                    name="about" 
                                    id="business_description" 
                                    class="form-control" 
                                    placeholder="Enter description" 
                                    cols="4" 
                                    rows="3"
                                >{{ old('about') }}</textarea>
                                <label class="error" for="about">
                                    @error('about') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    class="form-control" 
                                    placeholder="Enter email Id"
                                >
                                <label class="error" for="email">
                                    @error('email') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Website</label>
                            <div class="col-sm-4">
                                <input 
                                    type="text" 
                                    name="website" 
                                    value="{{ old('website') }}" 
                                    class="form-control" 
                                    placeholder="Enter website"
                                >
                                <label class="error" for="website">
                                    @error('website') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Phone</label>
                            <div class="col-sm-2">
                                <input 
                                    type="text" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    class="form-control" 
                                    placeholder="Enter phone"
                                >
                                <label class="error" for="phone">
                                    @error('phone') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <h4>Add Media</h4>
                        <hr/>
                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">Media <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                        <input type="file" name="business_photos" accept="image/*" id="business_photos">
                                        <label class="image_format_label">Please select images</label>
                                    </div>
                                </div>
                                <label class="error" for="allMediaFiles" id="mediaFile_error">
                                    @error('mediaFile') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div id="business_media_container">
                                @foreach(isset($file_data) ? $file_data : [] as $f)
                                    <div id="div_{{ $f->id }}" class="file-preview col-sm-2 mr10">
                                        <div class="file-preview-thumbnails">
                                            <div class="file-preview-frame">
                                                <img 
                                                    src="{{ asset('uploads/business/' . $f->businessId . '/' . $f->fileName) }}"
                                                    class="file-preview-image"
                                                >
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <input 
                                                type="button" 
                                                class="btn btn-primary remove-file" 
                                                value="Remove" 
                                                data-id="{{ $f->id }}"
                                                onclick="javascript:deleteFile('div_{{ $f->id }}')"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <h4>Daywise time setting</h4>
                        <hr/>
                        <div>
                            @if(isset($business_data))
                                @foreach($business_data->businessHours as $day => $hours)
                                    @include('business.dayWiseTime', ['key' => $day, 'value' => $day_array[$day], 'hours' => $hours])
                                @endforeach
                            @else
                                @foreach($day_array as $key => $value)
                                    @include('business.dayWiseTime', ['key' => $key, 'value' => $value])
                                @endforeach
                            @endif
                            <div class="form-group">
                                <div class="col-sm-offset-2">
                                    <label class="error" for="businesstime"></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt20">
                            <div class="col-sm-9 col-sm-offset-2">
                                <button 
                                    type="button" 
                                    id="save_business" 
                                    class="btn btn-success btn-quirk btn-wide mr5" 
                                    onclick="javascript:saveBusiness();"
                                >
                                    {{ !isset($business_data) ? 'Save' : 'Update' }}
                                </button>
                                <a href="{{ url('business-list') }}" class="btn btn-success btn-quirk btn-wide">Cancel</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include("common.cropModal")
    <div class="modal fade" id="logoCropModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="upload-logo-demo"></div>
                </div>
                <div class="modal-footer">
                    <div class="text-right">
                        <input 
                            type="button" 
                            class="btn btn-primary" 
                            value="Save" 
                            onclick="javascript:saveCroppedLogo()"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('common.filePreview')
    @include('business.timeSelection')
    @extends('common.footer')

@section('jquerysection')
    <link rel="stylesheet" href="{{ asset('css/business.css') . '?t=' . now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('ui/css/fileinput.css') . '?t=' . now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('css/croppie.css') . '?t=' . now()->timestamp }}">

    <script src="{{ asset('js/business.js') . '?t=' . now()->timestamp }}"></script>
    <script src="{{ asset('js/croppie.js') }}"></script>
@endsection
@endsection
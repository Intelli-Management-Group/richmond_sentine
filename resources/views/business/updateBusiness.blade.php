@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ $page_title['url'] }}">{{ $page_title['title'] }}</a></li>
        <li class="active">Business Edit</li>
    </ol>
    <div class="row">
        <div class="col-md-12">
            <div class="alert hidden alert-success" id="successMessage">
                {{ session('success') }}
            </div>

            <div class="alert hidden alert-danger" id="errorMessage">
                {{ session('error') }}
            </div>
            <div class="panel business_container">
                <div class="panel-heading nopaddingbottom">
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="row display-flex">
                        <div class="col-sm-3 mb20 padding10 border-efefef">
                            <ul id="nav-tabs-wrapper" class="nav nav-pills nav-stacked">
                                <li class="active"><a href="#business_details" data-toggle="tab" class="text-uppercase">Business
                                        details</a></li>
                                <li><a href="#business_contact" data-toggle="tab" class="text-uppercase">contact
                                        information</a></li>
                                <li><a href="#business_photos_container" data-toggle="tab" class="text-uppercase">logo &
                                        photos</a>
                                </li>
                                <li><a href="#business_hours" data-toggle="tab" class="text-uppercase">Business
                                        hours</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-sm-9">
                            <form action="{{ $action_url }}" method="POST" enctype="multipart/form-data" class="form-horizontal" id="regiration_form" data-url="{{ $action_url }}" data-validate="{{ url('validate-business') }}">
                                @csrf
                                <input type="hidden" name="business_id" value="{{ isset($business_data) ? $business_data->id : '' }}">

                                <div class="tab-content mb20 border-efefef">
                                    <div role="tabpanel" class="tab-pane fade in active" id="business_details">
                                        @include('business.businessDetail')
                                        <div class="mb-0 text-right">
                                            <input type="button" name="submit" class="submit btn btn-success btn-quirk" value="Save" onclick="javascript:updateBusiness(this)">
                                            <a href="{{ $page_title['url'] }}" class="btn btn-success btn-quirk">Cancel</a>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="business_contact">
                                        @include('business.businessContact', ['business_data' => $business_data])
                                        <div class="mb-0 text-right">
                                            <input type="button" name="submit" class="submit btn btn-success btn-quirk" value="Save" onclick="javascript:updateBusiness(this)">
                                            <a href="{{ $page_title['url'] }}" class="btn btn-success btn-quirk">Cancel</a>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="business_photos_container">
                                        @include('business.businessPhotos')
                                        <div class="mb-0 text-right">
                                            <input type="button" name="submit" class="submit btn btn-success btn-quirk" value="Save" onclick="javascript:updateBusiness(this)">
                                            <a href="{{ $page_title['url'] }}" class="btn btn-success btn-quirk">Cancel</a>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="business_hours">
                                        @include('business.businessHours')
                                        <div class="mb-0 text-right">
                                            <input type="button" name="submit" class="submit btn btn-success btn-quirk" value="Save" onclick="javascript:updateBusiness(this)">
                                            <a href="{{ $page_title['url'] }}" class="btn btn-success btn-quirk">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @include('common.cropModal')
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
                                    <label class="image_format_label w-100">(Resize or Crop image and drag to position. Slide towards right side to zoom in and left side to zoom out)</label>
                                    <div id="upload-logo-demo"></div>
                                </div>
                                <div class="modal-footer">
                                    <div class="text-right">
                                        <input type="button" class="btn btn-primary" value="Save" onclick="javascript:saveCroppedLogo()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('common.filePreview')
                    @include('business.timeSelection')
                </div>
            </div>
        </div>
    </div>

    @extends('common.footer')

    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('css/business.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/css/fileinput.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('css/croppie.css') . '?t=' . now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/lib/summernote/summernote.css') }}">
        <link rel="stylesheet" href="{{ asset('ui/lib/clockpicker/bootstrap-clockpicker.css') }}">
        <script src="{{ asset('ui/lib/summernote/summernote.js') }}"></script>
        <script src="{{ asset('ui/lib/clockpicker/bootstrap-clockpicker.js') }}"></script>
        <script src="{{ asset('js/business.js') . '?t=' . now()->timestamp }}"></script>
        <script src="{{ asset('js/croppie.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                @if(isset($business_data))
                    var existing_business_category = '{{ $business_data->categoryId }}';
                    @if($business_data->otherCategoryName != '')
                        $("#business_category").val("other").change();
                    @else
                        var cloneSelect = $("#subcategoryClone").clone();
                        $(cloneSelect).find("option").each(function () {
                            if($(this).val() != "") {
                                if (existing_business_category.indexOf($(this).val()) != -1) {
                                    $("#business_category").val($(this).data("parent")).change();
                                    $('.subcategory').val($(this).val());
                                }
                            }
                        });
                    @endif
                @endif
            });
        </script>
    @endsection
@endsection
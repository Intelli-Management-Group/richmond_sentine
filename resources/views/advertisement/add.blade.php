@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('advertisement-list') }}"> Advertisement List</a></li>
        <li class="active">Advertisement {{ isset($advertisement_data) ? "Edit" : "Add" }}</li>
    </ol>

    @php
        $advertisement_image_resolution = config('constants.advertisement_image_resolution');

    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="panel advertisement_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Advertisement {{ isset($advertisement_data) ? "Edit" : "Add" }}</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form action="{{ route(isset($advertisement_data) ? 'update-advertisement' : 'insert-advertisement') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="form-horizontal"
                          id="advertisement_form">
                        @csrf

                        <input type="hidden" name="advertisement_id" value="{{ isset($advertisement_data) ? $advertisement_data->id : '' }}">

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="name" class="form-control" placeholder="Enter name" value="{{ old('name', isset($advertisement_data) ? $advertisement_data->name : '') }}">
                                @error('name')
                                    <label class="error" for="name">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="link" class="col-sm-2 control-label">Link <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" name="link" class="form-control" placeholder="Enter redirection link" value="{{ old('link', isset($advertisement_data) ? $advertisement_data->link : '') }}">
                                @error('link')
                                    <label class="error" for="link">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="advertisementFor" class="col-sm-2 control-label">Display in web</label>
                            <div class="col-sm-5 padding10">
                                <input type="checkbox" name="advertisementFor[]" value="web" {{ in_array('web', old('advertisementFor', isset($advertisement_data) ? $advertisement_data->advertisementFor ?? [] : [])) ? 'checked' : '' }}>
                                @error('advertisementFor')
                                    <label class="error" for="advertisementFor">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group" id="web_container">
                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="web_position[]" value="sidebar" {{ in_array('sidebar', old('web_position', isset($advertisement_data) ? $advertisement_data->web_position ?? [] : [])) ? 'checked' : '' }}> Sidebar
                                </div>
                                <div class="col-sm-8 hidden" id="sidebar_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="text-center">
                                            <div class="browsebtn browsebtn-edit">
                                                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                <input type="file" name="position_image[sidebar]" accept=".png,.jpg,.gif" class="position_image">
                                            </div>
                                            @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["sidebar"]) && !empty($advertisement_data["position_image"]["sidebar"]))
                                                <img class="thumbnail" id="sidebar_preview" src="{{ $advertisement_data['position_image']['sidebar'] }}">
                                            @endif
                                            <br>
                                            <span>W {{ $advertisement_image_resolution['web_sidebar']['width'] }}px X H {{ $advertisement_image_resolution['web_sidebar']['height'] }}px</span>
                                            <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                        </div>
                                    </div>
                                    <label class="error" id="sidebar_error">
                                        @error('position_image.sidebar') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[sidebar][]" 
                                                       value="{{ count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all', old('image_category.sidebar', isset($advertisement_data) ? $advertisement_data->image_category['sidebar'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($web_category_data_array) > 0 ? $web_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[sidebar][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.sidebar', isset($advertisement_data) ? $advertisement_data->image_category['sidebar'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.sidebar')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="web_position[]" value="sidebar_responsive" 
                                           onclick="return false;"
                                           {{ in_array('sidebar_responsive', old('web_position', isset($advertisement_data) ? $advertisement_data->web_position ?? [] : [])) ? 'checked' : '' }}> Sidebar(for responsive view)
                                </div>
                                <div class="col-sm-8 hidden" id="sidebar_responsive_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="text-center">
                                            <div class="browsebtn browsebtn-edit">
                                                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                <input type="file" name="position_image[sidebar_responsive]" accept=".png,.jpg,.gif" class="position_image">
                                            </div>
                                            @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["sidebar_responsive"]) && !empty($advertisement_data["position_image"]["sidebar_responsive"]))
                                                <img class="thumbnail" id="sidebar_responsive_preview" src="{{ $advertisement_data['position_image']['sidebar_responsive'] }}">
                                            @endif
                                            <br>
                                            <span>W {{ $advertisement_image_resolution['web_sidebar_responsive']['width'] }}px X H {{ $advertisement_image_resolution['web_sidebar_responsive']['height'] }}px</span>
                                            <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                        </div>
                                    </div>
                                    <label class="error" id="sidebar_responsive_error">
                                        @error('position_image.sidebar_responsive') {{ $message }} @enderror
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="web_position[]" value="middle"
                                           {{ in_array('middle', old('web_position', isset($advertisement_data) ? $advertisement_data->web_position ?? [] : [])) ? 'checked' : '' }}> Middle
                                </div>
                                <div class="col-sm-8 hidden" id="middle_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="browsebtn browsebtn-edit">
                                            <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                            <input type="file" name="position_image[middle]" accept=".png,.jpg,.gif" class="position_image">
                                        </div>
                                        @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["middle"]) && !empty($advertisement_data["position_image"]["middle"]))
                                            <img class="thumbnail" id="middle_image_preview" src="{{ $advertisement_data['position_image']['middle'] }}">
                                        @endif
                                        <br>
                                        <span>W {{ $advertisement_image_resolution['web_middle']['width'] }}px X H {{ $advertisement_image_resolution['web_middle']['height'] }}px</span>
                                        <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                    </div>
                                    <label class="error" id="middle_error">
                                        @error('position_image.middle') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[middle][]" 
                                                       value="{{ count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all', old('image_category.middle', isset($advertisement_data) ? $advertisement_data->image_category['middle'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($web_category_data_array) > 0 ? $web_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[middle][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.middle', isset($advertisement_data) ? $advertisement_data->image_category['middle'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.middle')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="web_position[]" value="bottom"
                                           {{ in_array('bottom', old('web_position', isset($advertisement_data) ? $advertisement_data->web_position ?? [] : [])) ? 'checked' : '' }}> Bottom
                                </div>
                                <div class="col-sm-8 hidden" id="bottom_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="browsebtn browsebtn-edit">
                                            <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                            <input type="file" name="position_image[bottom]" accept=".png,.jpg,.gif" class="position_image">
                                        </div>
                                        @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["bottom"]) && !empty($advertisement_data["position_image"]["bottom"]))
                                            <img class="thumbnail" id="bottom_image_preview" src="{{ $advertisement_data['position_image']['bottom'] }}">
                                        @endif
                                        <br>
                                        <span>W {{ $advertisement_image_resolution['web_bottom']['width'] }}px X H {{ $advertisement_image_resolution['web_bottom']['height'] }}px</span>
                                        <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                    </div>
                                    <label class="error" id="bottom_error">
                                        @error('position_image.bottom') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[bottom][]" 
                                                       value="{{ count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($web_category_data_array) > 0 ? implode(',', array_keys($web_category_data_array)) : 'all', old('image_category.bottom', isset($advertisement_data) ? $advertisement_data->image_category['bottom'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($web_category_data_array) > 0 ? $web_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[bottom][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.bottom', isset($advertisement_data) ? $advertisement_data->image_category['bottom'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.bottom')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            @error('web_position')
                                <div class="col-sm-offset-2">
                                    <label class="error" for="web_position">{{ $message }}</label>
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="advertisementFor" class="col-sm-2 control-label">Display in app</label>
                            <div class="col-sm-5 padding10">
                                <input type="checkbox" name="advertisementFor[]" value="app" 
                                       {{ in_array('app', old('advertisementFor', isset($advertisement_data) ? $advertisement_data->advertisementFor ?? [] : [])) ? 'checked' : '' }}>
                                @error('advertisementFor')
                                    <label class="error col-sm-offset-2" for="advertisementFor">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group" id="app_container">
                            <div class="col-sm-3 col-sm-offset-2">
                                <div>
                                    <input type="checkbox" name="app_position[]" value="square"
                                           {{ in_array('square', old('app_position', isset($advertisement_data) ? $advertisement_data->app_position ?? [] : [])) ? 'checked' : '' }}> Square
                                </div>
                                <div class="col-sm-8 hidden" id="square_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="text-center">
                                            <div class="browsebtn browsebtn-edit">
                                                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                <input type="file" name="position_image[square]" accept=".png,.jpg,.gif" class="position_image">
                                            </div>
                                            @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["square"]) && !empty($advertisement_data["position_image"]["square"]))
                                                <img class="thumbnail" id="square_preview" src="{{ $advertisement_data['position_image']['square'] }}">
                                            @endif
                                            <br>
                                            <span>W {{ $advertisement_image_resolution['app_square']['width'] }}px X H {{ $advertisement_image_resolution['app_square']['height'] }}px</span>
                                            <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                        </div>
                                    </div>
                                    <label class="error" id="square_error">
                                        @error('position_image.square') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[square][]" 
                                                       value="{{ count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all', old('image_category.square', isset($advertisement_data) ? $advertisement_data->image_category['square'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($app_category_data_array) > 0 ? $app_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[square][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.square', isset($advertisement_data) ? $advertisement_data->image_category['square'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.square')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="app_position[]" value="tablet_square"
                                           {{ in_array('tablet_square', old('app_position', isset($advertisement_data) ? $advertisement_data->app_position ?? [] : [])) ? 'checked' : '' }}> Square(Tablets/iPads)
                                </div>
                                <div class="col-sm-8 hidden" id="tablet_square_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="text-center">
                                            <div class="browsebtn browsebtn-edit">
                                                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                <input type="file" name="position_image[tablet_square]" accept=".png,.jpg,.gif" class="position_image">
                                            </div>
                                            @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["tablet_square"]) && !empty($advertisement_data["position_image"]["tablet_square"]))
                                                <img class="thumbnail" id="tablet_square_preview" src="{{ $advertisement_data['position_image']['tablet_square'] }}">
                                            @endif
                                            <br>
                                            <span>W {{ $advertisement_image_resolution['tablet_square']['width'] }}px X H {{ $advertisement_image_resolution['tablet_square']['height'] }}px</span>
                                            <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                        </div>
                                    </div>
                                    <label class="error" id="tablet_square_error">
                                        @error('position_image.tablet_square') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[tablet_square][]" 
                                                       value="{{ count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all', old('image_category.tablet_square', isset($advertisement_data) ? $advertisement_data->image_category['tablet_square'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($app_category_data_array) > 0 ? $app_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[tablet_square][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.tablet_square', isset($advertisement_data) ? $advertisement_data->image_category['tablet_square'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.tablet_square')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div>
                                    <input type="checkbox" name="app_position[]" value="horizontal"
                                           {{ in_array('horizontal', old('app_position', isset($advertisement_data) ? $advertisement_data->app_position ?? [] : [])) ? 'checked' : '' }}> Horizontal
                                </div>
                                <div class="col-sm-8 hidden" id="horizontal_container">
                                    <div class="uploadimgs customdesign mt20">
                                        <div class="text-center">
                                            <div class="browsebtn browsebtn-edit">
                                                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                <input type="file" name="position_image[horizontal]" accept=".png,.jpg,.gif" class="position_image">
                                            </div>
                                            @if(isset($advertisement_data) && isset($advertisement_data["position_image"]["horizontal"]) && !empty($advertisement_data["position_image"]["horizontal"]))
                                                <img class="thumbnail" id="horizontal_preview" src="{{ $advertisement_data['position_image']['horizontal'] }}">
                                            @endif
                                            <br>
                                            <span>W {{ $advertisement_image_resolution['app_horizontal']['width'] }}px X H {{ $advertisement_image_resolution['app_horizontal']['height'] }}px</span>
                                            <label class="image_format_label">Please select (png,jpg,gif) image format</label>
                                        </div>
                                    </div>
                                    <label class="error" id="horizontal_error">
                                        @error('position_image.horizontal') {{ $message }} @enderror
                                    </label>
                                    <div class="advertisement_category_container">
                                        <label class="control-label"><strong>Select category</strong></label>
                                        <ul class="tree">
                                            <li>
                                                <input type="checkbox" name="image_category[horizontal][]" 
                                                       value="{{ count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all' }}" 
                                                       class="is_default"
                                                       {{ in_array(count($app_category_data_array) > 0 ? implode(',', array_keys($app_category_data_array)) : 'all', old('image_category.horizontal', isset($advertisement_data) ? $advertisement_data->image_category['horizontal'] ?? [] : [])) ? 'checked' : '' }}>
                                                <label>Default in all</label>
                                            </li>
                                            @foreach(count($app_category_data_array) > 0 ? $app_category_data_array : [] as $key => $value)
                                                <li>
                                                    <input type="checkbox" name="image_category[horizontal][]" value="{{ $key }}"
                                                           {{ in_array($key, old('image_category.horizontal', isset($advertisement_data) ? $advertisement_data->image_category['horizontal'] ?? [] : [])) ? 'checked' : '' }}>
                                                    <label>{{ $value }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @error('image_category.horizontal')
                                        <label class="error" for="advertisementFor">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            @error('app_position')
                                <div class="col-sm-offset-2">
                                    <label class="error" for="app_position">{{ $message }}</label>
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expiryDate" class="col-sm-2 control-label">Expiry date</label>
                            <div class="col-sm-2">
                                <input type="text" name="expiryDate" class="form-control" id="expiryDate" autocomplete="off" value="{{ old('expiryDate', isset($advertisement_data) ? $advertisement_data->expiryDate : '') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="isLocationDetection" class="col-sm-2 control-label">Location detection</label>
                            <div class="col-sm-5 padding10">
                                <input type="checkbox" name="isLocationDetection" value="1" {{ old('isLocationDetection', isset($advertisement_data) ? $advertisement_data->isLocationDetection : false) ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="form-group address_container">
                            <label for="address" class="col-sm-2 control-label">Address</label>
                            <div class="col-sm-5">
                                <input type="text" name="address" class="form-control" placeholder="Enter address" value="{{ old('address', isset($advertisement_data) ? $advertisement_data->address : '') }}">
                                @error('address')
                                    <label class="error" for="address">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-2">
                                @if(!isset($advertisement_data))
                                    <a href="javascript:void(0)" onclick="javascript:saveAsDraft();" class="btn btn-success btn-quirk btn-wide mr5">Save as draft</a>
                                @endif
                                <button type="submit" class="btn btn-success btn-quirk btn-wide mr5">
                                    {{ !isset($advertisement_data) ? 'Publish' : 'Update' }}
                                </button>
                                <a href="{{ route('advertisement-list') }}" class="btn btn-success btn-quirk btn-wide mr5">Cancel</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br />
                    </form>
                </div>
            </div>
        </div>
    </div>

    @extends('common.footer')

    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('ui/css/bootstrap-datepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('css/advertisement.css').'?t='.now()->timestamp }}">
        <script src="{{ asset('ui/js/bootstrap-datepicker.js') }}"></script>
        <script type="text/javascript">
            var advertisement_images_resolution = '{{ json_encode($advertisement_image_resolution) }}';

            
            if (advertisement_images_resolution != "") {
                advertisement_images_resolution = $.parseJSON(advertisement_images_resolution.replace(/&quot;/g, '"'));
                
            }
        </script>
        <script src="{{ asset('js/advertisement.js').'?t='.now()->timestamp }}"></script>
    @endsection
@endsection
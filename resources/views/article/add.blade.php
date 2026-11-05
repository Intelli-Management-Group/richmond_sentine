@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('article-list') }}"> Article List</a></li>
        <li class="active">{{ $title }} {{ isset($article_data) ? "Edit" : "Add" }}</li>
    </ol>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel article_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>{{ $title }} {{ isset($article_data) ? "Edit" : "Add" }}</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form action="{{ url($is_video ? 'insert-video' : 'insert-article') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="form-horizontal" 
                          id="article_form" 
                          data-url="{{ url($is_video ? 'insert-video' : 'insert-article') }}">
                        @csrf
                        
                        <input type="hidden" name="is_video" value="{{ $is_video }}">
                        <input type="hidden" name="article_id" value="{{ isset($article_data) ? $article_data->id : '' }}">
                        <input type="hidden" name="youtube_duration" 
                               id="youtube_duration" 
                               value="{{ (isset($article_data) && $article_data->isYoutubeVideo) ? $article_data->videoDuration : old('youtube_duration') }}">
                        <input type="hidden" name="video_duration" 
                               id="video_duration" 
                               value="{{ (isset($article_data) && !$article_data->isYoutubeVideo) ? $article_data->videoDuration : null }}">
                        <textarea name="thumbnail_image" id="thumbnail_image" class="hidden"></textarea>

                        @if($is_video)
                            <div class="form-group uploadimgs customdesign">
                                <label for="videoFile" class="col-sm-2 control-label">Video</label>
                                <div class="col-sm-3">
                                    <div class="text-center">
                                        <div class="browsebtn browsebtn-edit">
                                            <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                            <input type="file" name="videoFile" accept=".mp4,.avi,.mov,.3gp,.ogg">
                                            <label class="image_format_label">Please select (mp4 ,avi ,mov ,3gp ,ogg) video format</label>
                                        </div>
                                        <video id="video_element" width="200" height="100" controls disablepictureinpicture controlslist="nodownload" frameborder="0">
                                            <source src="{{ (isset($article_data) && $article_data->videoFile != '') ? '../../uploads/video/'.$article_data->videoFile : 'mov_bbb.mp4' }}"
                                                    id="videoFile_preview">
                                            Your browser does not support HTML5 video.
                                        </video>
                                    </div>
                                    <label class="error" for="videoFile" id="videoFile_error">
                                        @error('videoFile') {{ $message }} @enderror
                                    </label>
                                </div>
                                <div class="col-sm-1 text-center"><strong> OR </strong></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="youtubeUrl" class="col-sm-3 control-label">Youtube URL <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <span class="input-group-addon">https://www.youtube.com/watch?v=</span>
                                                <input type="text" name="youtubeUrl" 
                                                       class="form-control" 
                                                       placeholder="Enter youtube URL" 
                                                       value="{{ !empty($article_data) ? $article_data->youtubeUrl : old('youtubeUrl') }}">
                                            </div>
                                            <label class="error" for="youtubeUrl" id="youtubeUrl_error">
                                                @error('youtubeUrl') {{ $message }} @enderror
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group uploadimgs customdesign">
                                        <label for="videoThumbnail" class="col-sm-3 control-label">Youtube Thumbnail <span class="text-danger">*</span></label>
                                        <div class="col-sm-5">
                                            <div class="text-center">
                                                <div class="browsebtn browsebtn-edit">
                                                    <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                                    <input type="file" name="videoThumbnail" accept=".jpg">
                                                </div>
                                                @if(isset($article_data) && $article_data->isYoutubeVideo == 1 && $article_data->videoThumbnail != null)
                                                    <img class="thumbnail" 
                                                         id="videoThumbnail_preview" 
                                                         src="{{ '../../uploads/video_thumbnail/'.$article_data->videoThumbnail }}">
                                                @endif
                                                <br>
                                                <span>W {{ config('constants.article_image_width') }}px X H {{ config('constants.article_image_height') }}px</span>
                                                <label class="image_format_label">Please select (jpg) image format</label>
                                            </div>
                                            <label class="error" for="videoThumbnail" id="videoThumbnail_error">
                                                @error('videoThumbnail') {{ $message }} @enderror
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 col-sm-offset-2"></div>
                            </div>
                        @endif

                        <div class="form-group">
                            @if(!$is_video)
                                <label for="featureId" class="col-sm-2 control-label">Feature</label>
                                <div class="col-sm-3 padding10">
                                    @foreach($feature_array as $key => $value)
                                        <input type="checkbox" name="featureId[]" value="{{ $key }}"> {{ $value }}
                                    @endforeach
                                    <label class="error" for="featureId">
                                        @error('featureId') {{ $message }} @enderror
                                    </label>
                                </div>
                            @endif
                            <label for="authorId" class="col-sm-2 control-label">Author <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select name="authorId" class="form-control">
                                    <option value="">Select author</option>
                                    @foreach($author_array as $key => $value)
                                        <option value="{{ $key }}" {{ old('authorId') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <label class="error" for="authorId">
                                    @error('authorId') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        @if(!$is_video)
                            <div class="form-group">
                                <label for="categoryId" class="col-sm-2 control-label">Category <span class="text-danger">*</span></label>
                                <div class="col-sm-3">
                                    <div class="parent_category_container" style="height:200px;">
                                        <ul class="tree">
                                            @foreach(count($category_data_array) > 0 ? $category_data_array : [] as $key => $value)
                                                <li class="has">
                                                    <input type="radio" 
                                                           name="categoryId[]" 
                                                           value="{{ $key }}"
                                                           {{ count($value['child_list']) > 0 ? 'disabled' : '' }}>
                                                    <label>{{ $value['name'] }}</label>
                                                    @if(count($value['child_list']) > 0)
                                                        <ul>
                                                            @foreach(count($value['child_list']) > 0 ? $value['child_list'] : [] as $ckey => $cvalue)
                                                                <li class="has">
                                                                    <input type="radio" name="categoryId[]" value="{{ $ckey }}">
                                                                    <label>{{ $cvalue['name'] }}</label>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <label class="error" for="categoryId">
                                        @error('categoryId') {{ $message }} @enderror
                                           </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="source" class="col-sm-2 control-label">Source</label>
                            <div class="col-sm-4">
                                <input type="text" name="source" 
                                       class="form-control" 
                                       placeholder="Enter source" 
                                       value="{{ !empty($article_data) ? $article_data->source : old('source') }}">
                                <label class="error" for="source">
                                    @error('source') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="heading" class="col-sm-2 control-label">Heading <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="heading" 
                                       class="form-control" 
                                       placeholder="Enter heading" 
                                       value="{{ !empty($article_data) ? $article_data->heading :  old('heading') }}">
                                <label class="error" for="heading">
                                    @error('heading') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="summary" class="col-sm-2 control-label">Summary <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="summary" 
                                       class="form-control" 
                                       placeholder="Enter summary" 
                                       value="{{ old('summary') }}">
                                <label class="error" for="summary">
                                    @error('summary') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="metaTag" class="col-sm-2 control-label">Meta tags</label>
                            <div class="col-sm-9">
                                <input type="text" name="metaTag" id="metaTag"
                                       class="form-control" 
                                       placeholder="Enter metaTag" 
                                       value="{{ old('metaTag') }}">
                                <label class="error" for="metaTag">
                                    @error('metaTag') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-sm-2 control-label">Description <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea name="description" 
                                          id="article_description" 
                                          class="form-control" 
                                          placeholder="Enter description" 
                                          cols="4" 
                                          rows="3">{{ !empty($article_data) ? $article_data->description : old('description') }}</textarea>
                                <label class="error" for="description">
                                    @error('description') {{ $message }} @enderror
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="expiryDate" class="col-sm-2 control-label">Expiry date</label>
                            <div class="col-sm-3">
                                <input type="text" 
                                       name="expiryDate" 
                                       class="form-control" 
                                       id="expiryDate" 
                                       autocomplete="off" 
                                       value="{{ !empty($article_data) ? $article_data->expiryDate : old('expiryDate') }}">
                            </div>
                        </div>

                        @if(!$is_video)
                            <h4>Add media</h4>
                            <hr/>
                            <div class="form-group uploadimgs customdesign">
                                <label for="mediaFile" class="col-sm-2 control-label">Media</label>
                                <div class="col-sm-3">
                                    <div class="text-center">
                                        <div class="browsebtn browsebtn-edit">
                                            <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                            <input type="file" 
                                                   name="mediaFile[]" 
                                                   accept=".mp4,.avi,.mov,.3gp,.ogg,.png,.jpg,.gif" 
                                                   multiple 
                                                   id="mediaFile">
                                            <label class="image_format_label">Please select (mp4 ,avi ,mov ,3gp , ogg) videos or (png, jpg, gif (W {{ config('constants.article_image_width') }} px X H {{ config('constants.article_image_height') }}px)) images </label>
                                        </div>
                                    </div>
                                    <label class="error" for="allMediaFiles" id="mediaFile_error">
                                        @error('mediaFile') {{ $message }} @enderror
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div id="media_container">
                                    @foreach(isset($file_data) ? $file_data : [] as $f)
                                        <div class="file-preview col-sm-2 mr10" id="div_{{ $f->id }}">
                                            <div class="file-preview-thumbnails">
                                                <div class="file-preview-frame">
                                                    @if($f->type == "image")
                                                        <img src="{{ '../../uploads/article/'.$f->fileName }}" 
                                                             class="file-preview-image">
                                                    @else
                                                        <video class="video_element" 
                                                               width="170" 
                                                               controls 
                                                               disablepictureinpicture 
                                                               controlslist="nodownload" 
                                                               frameborder="0">
                                                            <source src="{{ '../../uploads/article/'.$f->fileName }}">
                                                            Your browser does not support HTML5 video.
                                                        </video>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <input type="button" 
                                                       class="btn btn-primary remove-file" 
                                                       value="Remove" 
                                                       data-id="{{ $f->id }}" 
                                                       onclick="javascript:deleteFile('div_{{ $f->id }}')"/>
                                            </div>
                                            <div class="padding10">
                                                <input type="radio" 
                                                       name="isMain[]" 
                                                       class="isMain" 
                                                       value="{{ $f->id }}" 
                                                       {{ $f->isMain ? 'checked' : '' }}> Display as main
                                            </div>
                                            <div class="mb5">
                                                <input type="text" 
                                                       name="caption[{{ $f->id }}]" 
                                                       placeholder="Caption" 
                                                       class="form-control caption" 
                                                       value="{{ $f->caption }}">
                                            </div>
                                            <div>
                                                <input type="text" 
                                                       name="credit[{{ $f->id }}]" 
                                                       placeholder="Credit" 
                                                       class="form-control credit" 
                                                       value="{{ $f->credit }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-offset-2">
                                    <label class="error" for="isMainSelected">
                                        @error('isMainSelected') {{ $message }} @enderror
                                    </label>
                                </div>
                            </div>
                        @endif

                        @if($is_video)
                            <div class="form-group">
                                <label for="videoCredit" class="col-sm-2 control-label">Video credit</label>
                                <div class="col-sm-4">
                                    <input type="text" 
                                           name="videoCredit" 
                                           class="form-control" 
                                           placeholder="Enter video credit" 
                                           value="{{ !empty($article_data) ? $article_data->videoCredit : old('videoCredit') }}">
                                    <label class="error" for="videoCredit">
                                        @error('videoCredit') {{ $message }} @enderror
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="viewCount" class="col-sm-2 control-label">View count</label>
                            <div class="col-sm-2">
                                <input type="number" 
                                       name="viewCount" 
                                       class="form-control" 
                                       value="{{ !empty($article_data) ? $article_data->viewCount : old('viewCount') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sendNotification" class="col-sm-2 control-label">Send notification</label>
                            <div class="col-sm-4 padding10">
                                <input type="checkbox" name="sendNotification" value="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-2">
                                @if(!isset($article_data))
                                    <a href="javascript:void(0)" 
                                       onclick="javascript:saveAsDraft();" 
                                       class="btn btn-success btn-quirk btn-wide mr5">Save as draft</a>
                                @endif
                                @if($is_video)
                                    <button type="submit" 
                                            class="btn btn-success btn-quirk btn-wide mr5">
                                        {{ !isset($article_data) ? 'Publish' : 'Update' }}
                                    </button>
                                @else
                                    <button type="button" 
                                            id="save_article" 
                                            class="btn btn-success btn-quirk btn-wide mr5" 
                                            onclick="javascript:saveArticle();">
                                        {{ !isset($article_data) ? 'Publish' : 'Update' }}
                                    </button>
                                @endif
                                <a href="{{ !isset($article_data) ? url('article-list') : ($article_data->status ? url('article-list') : url('unpublished-article-list')) }}"
                                   class="btn btn-success btn-quirk btn-wide">Cancel</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <canvas id="canvas_videothumbnail" 
            width="{{ config('constants.article_image_width') }}" 
            height="{{ config('constants.article_image_height') }}" 
            class="hidden"></canvas>

    @include('common.articleFilePreview')
    @extends('common.footer')

    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('css/article.css').'?t='.now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/css/fileinput.css').'?t='.now()->timestamp }}">
        <link rel="stylesheet" href="{{ asset('ui/css/bootstrap-datepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery.tagsinput-revisited.min.css') }}">

        <script src="{{ asset('ui/js/javascript.js') }}"></script>
        <script src="{{ asset('ui/js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/jquery.tagsinput-revisited.js').'?t='.now()->timestamp }}"></script>
        
        <script type="text/javascript">
            var article_image_width = "{{ config('constants.article_image_width') }}";
            var article_image_height = "{{ config('constants.article_image_height') }}";
            $(document).ready(function () {
                $("#youtubeUrl").off("blur");
                $("#youtubeUrl").on("blur", function () {
                    $("#youtubeUrl_error").text("");
                    $("#heading,#metaTag,#youtube_duration").val("");
                    $('#article_description').summernote("code", "");
                    var value = $.trim($(this).val());
                    if (value != "") {
                        $.ajax({
                            url: "{{ url('get-youtube-video-data') }}",
                            type: "POST",
                            data: {
                                video_id: value
                            },
                            success: function (html) {
                                if (html != "") {
                                    html = $.parseJSON(html);
                                    $("#heading").val(html.title);
                                    $('#article_description').summernote("code", html.description);
                                    $("#metaTag").val(html.tags);
                                    $("#youtube_duration").val(html.duration);
                                } else {
                                    $("#youtubeUrl_error").text("Invalid URL");
                                }
                            }
                        });
                    }
                });
                
                $('#metaTag').tagsInput({
                    'placeholder': 'Add meta tags',
                    'delimiter': [';'],
                    'autocomplete': {
                        source: '{{ url('/meta-tags') }}'
                    }
                });

                @if(isset($article_data))
                    $("#metaTag").importTags('{{ $article_data->metaTag }}');
                @endif

                @if($is_video)
                    $('#article_form').submit(function () {
                        if ($.trim($("#youtubeUrl_error").text()) != "") {
                            return false;
                        }
                    });
                @endif
            });
        </script>
        <script src="{{ asset('js/article.js').'?t='.now()->timestamp }}"></script>
    @endsection
@endsection
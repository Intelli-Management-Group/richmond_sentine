@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('admin/home') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('author-list') }}"> Author List</a></li>
        <li class="active">{{ isset($data) ? "Edit Author" : "Add Author" }}</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading nopaddingbottom">
                    <h4>{{ isset($data) ? "Edit Author" : "Add Author" }}</h4>
                </div>
                <div class="panel-body author_container">
                    <hr>
                    <form method="POST" 
                          action="{{ isset($data) ? url('edit-author/' . $data->id) : url('add-author') }}" 
                          enctype="multipart/form-data" 
                          class="form-horizontal">
                        @csrf
                        <input type="hidden" name="id" value="{{ isset($data) ? $data->id : '' }}">

                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Author Name <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', isset($data) ? $data->name : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Author Name">
                                    @error('name')
                                        <label class="error" for="name">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Author Email</label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="email" 
                                           value="{{ old('email', isset($data) ? $data->email : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Author Email">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Twitter Handle</label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="twitterHandle" 
                                           value="{{ old('twitterHandle', isset($data) ? $data->twitterHandle : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Twitter Id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Facebook Handle</label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="facebookHandle" 
                                           value="{{ old('facebookHandle', isset($data) ? $data->facebookHandle : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Facebook Id">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Position</label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="position" 
                                           value="{{ old('position', isset($data) ? $data->position : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Author Position">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Bio</label>
                                <div class="col-sm-8">
                                    <input type="text" 
                                           name="bio" 
                                           value="{{ old('bio', isset($data) ? $data->bio : '') }}" 
                                           class="form-control" 
                                           placeholder="Enter Bio">
                                </div>
                            </div>
                        </div>

                        <div class="form-group uploadimgs customdesign">
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Profile Image</label>
                                <div class="col-sm-6">
                                    <div class="upload_container text-center">
                                        <div class="browsebtn browsebtn-edit" id="browse">
                                            <span><i class="fa fa-paper-plane" aria-hidden="true"></i> Upload Image</span>
                                            <input name="profileImage" 
                                                   id="profileImage" 
                                                   type="file" 
                                                   accept=".jpeg,.png,.jpg">
                                        </div>
                                        @if(isset($data) && $data->profileImage != "")
                                            <img class="thumbnail" 
                                                 id="thumbnail" 
                                                 src="{{ url('../uploads/team/' . $data->profileImage) }}">
                                        @else
                                            <img class="thumbnail" id="thumbnail">
                                        @endif
                                        <br>
                                        <span>W 50px X H 50px</span>
                                    </div>
                                    @error('profileImage')
                                        <label id="profileImageError" class="error" for="profileImage">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="col-sm-4 control-label">Status</label>
                                <div class="col-sm-8 padding10">
                                    <input type="checkbox" 
                                           name="status" 
                                           value="1" 
                                           {{ (isset($data) && $data->status == 1) ? 'checked' : '' }}> Active
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-4">
                                <button class="btn btn-success btn-quirk btn-wide mr5" 
                                        name="submit" 
                                        id="add_more" 
                                        type="submit">Save</button>
                                <a href="{{ route('author-list') }}" 
                                   class="btn btn-success btn-quirk btn-wide mr5">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @extends('common.footer')

    @section('jquerysection')
        <link rel="stylesheet" href="{{ asset('ui/css/author-info.css') . '?t=' . now()->timestamp }}">
        <script>
            $(document).ready(function() {
                var _URL = window.URL || window.webkitURL;
                $("#profileImage").change(function(e) {
                    var file, img;
                    var fileUpload = $(this);
                    if ((file = this.files[0])) {
                        img = new Image();
                        img.onload = function() {
                            if (this.width != 50 && this.height != 50) {
                                $("#profileImageError").text("Please upload image having width 50px and height 50px.");
                                $(fileUpload).val("");
                            } else {
                                $("#profileImageError").text("");
                                $("#thumbnail").attr("src", _URL.createObjectURL(file));
                            }
                        };
                        img.src = _URL.createObjectURL(file);
                    }
                });
            });
        </script>
    @endsection
@endsection
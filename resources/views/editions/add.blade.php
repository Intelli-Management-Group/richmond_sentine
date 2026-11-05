@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li><a href="{{ url('edition-list') }}"> Edition List</a></li>
        <li class="active">Edition {{ isset($edition_data) ? "Edit" : "Add" }}</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="panel edition_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Edition {{ isset($edition_data) ? "Edit" : "Add" }}</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form action="{{ url('insert-edition') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="edition_id" value="{{ isset($edition_data) ? $edition_data->id : '' }}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="name" value="{{ old('name', isset($edition_data) ? $edition_data->name : '') }}" 
                                       class="form-control" placeholder="Enter name">
                                @error('name')
                                    <label class="error" for="name">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Volume number<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="volumeEdition" value="{{ old('volumeEdition', isset($edition_data) ? $edition_data->volumeEdition : '') }}" 
                                       class="form-control" placeholder="Enter volume number">
                                @error('volumeEdition')
                                    <label class="error" for="volumeEdition">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Edition number<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="editionNumber" value="{{ old('editionNumber', isset($edition_data) ? $edition_data->editionNumber : '') }}" 
                                       class="form-control" placeholder="Enter edition number">
                                @error('editionNumber')
                                    <label class="error" for="editionNumber">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Date<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" name="editionDate" value="{{ old('editionDate', isset($edition_data) ? $edition_data->editionDate : '') }}" 
                                       class="form-control" id="editionDate" autocomplete="off">
                                @error('editionDate')
                                    <label class="error" for="editionDate">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group uploadimgs customdesign">
                            <label class="col-sm-2 control-label">PDF</label>
                            <div class="col-sm-2">
                                <div class="text-center">
                                    <div class="browsebtn browsebtn-edit">
                                        <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                                        <input type="file" name="pdfFile" accept=".pdf" id="pdfFile">
                                    </div>
                                    <span id="selected_file_name"></span>
                                </div>
                                @error('pdfFile')
                                    <label class="error" for="pdfFile" id="pdfFile_error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 col-sm-offset-2">
                                <button type="submit" class="btn btn-success btn-quirk btn-wide mr5">Save</button>
                                <a href="{{ url('edition-list') }}" class="btn btn-success btn-quirk btn-wide">Cancel</a>
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
        <link rel="stylesheet" href="{{ asset('css/edition.css') . '?t=' . now()->timestamp }}">
        <script src="{{ asset('ui/js/bootstrap-datepicker.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('ui/css/bootstrap-datepicker.css') }}">
        <script type="text/javascript">
            $(document).ready(function() {
                $("#editionDate").datepicker({
                    format: "yyyy-mm-dd"
                }).on('changeDate', function(e) {
                    $(this).datepicker('hide');
                });

                $("#pdfFile").change(function(e) {
                    var file;
                    $("#selected_file_name").text("");
                    if ((file = this.files[0])) {
                        $("#selected_file_name").text(file.name);
                    }
                });
            });
        </script>
    @endsection
@endsection
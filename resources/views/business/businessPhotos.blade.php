<div class="form-group uploadimgs customdesign">
    <label for="logo" class="col-sm-2 control-label">Logo <span class="text-danger">*</span></label>
    <div class="col-sm-5">
        <div class="upload_container text-center">
            <div class="browsebtn browsebtn-edit">
                <span><i class="fa fa-paper-plane" aria-hidden="true"></i> Upload Image</span>
                <input name="logo" id="logoImage" type="file" accept=".jpeg,.png,.jpg,.gif,.svg"/>
            </div>
            <img class="thumbnail" id="logoThumbnail" 
                @isset($business_data)
                    @if($business_data->logo != null)
                        src="{{ $base_url . 'uploads/business/logo/' . $business_data->logo }}"
                    @endif
                @endisset
            /><br>
            <label class="image_format_label">Please select (jpeg,png,jpg,gif,svg) image format</label>
        </div>
        <label id="logoError" class="error" for="logo">
            @error('logo')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group uploadimgs customdesign">
    <label for="business_photos" class="col-sm-2 control-label">Media</label>
    <div class="col-sm-5">
        <div class="text-center">
            <div class="browsebtn browsebtn-edit">
                <span><i class="fa fa-paper-plane" aria-hidden="true"></i>Upload</span>
                <input name="business_photos" type="file" accept="image/*" id="business_photos"/>
                <label class="image_format_label">Please select images</label>
            </div>
        </div>
        <label class="error" for="allMediaFiles" id="mediaFile_error">
            @error('mediaFile')
                {{ $message }}
            @enderror
        </label>
    </div>
</div>

<div class="form-group">
    <div id="business_media_container">
        @foreach($file_data ?? [] as $f)
            <div id="div_{{ $f->id }}" class="file-preview col-sm-3 mr10">
                <div class="file-preview-thumbnails">
                    <div class="file-preview-frame">
                        <img 
                            src="{{ $base_url . 'uploads/business/' . $f->businessId . '/' . $f->fileName }}"
                            class="file-preview-image">
                    </div>
                </div>
                <div class="text-center">
                    <input 
                        type="button" 
                        class="btn btn-primary remove-file" 
                        value="Remove" 
                        data-id="{{ $f->id }}"
                        onclick="javascript:deleteFile('div_{{ $f->id }}')"/>
                </div>
            </div>
        @endforeach
    </div>
</div>
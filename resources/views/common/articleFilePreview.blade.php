<div id="file_preview_clone" class="file-preview col-sm-2 hidden mr10">
    <div class="file-preview-thumbnails">
        <div class="file-preview-frame">
            <img src="" class="file-preview-image">
            <video class="video_element" controls disablepictureinpicture controlslist="nodownload" frameborder="0">
                <source src="">
                Your browser does not support HTML5 video.
            </video>
            <canvas class="canvas_videothumbnail" width="{!! config('constants.article_image_width') !!}" height="{!! config('constants.article_image_height') !!}"></canvas>
        </div>
    </div>
    <div class="text-center"><input type="button" class="btn btn-primary remove-file" value="Remove" /></div>
    <div class="padding10"><input type="radio" name="isMain[]" class="isMain" />&nbsp;Display as main
    </div>
    <div class="mb5"><input type="textbox" name="caption[]" placeholder="Caption" class="form-control caption" /></div>
    <div><input type="textbox" name="credit[]" placeholder="Credit" class="form-control credit" /></div>
</div>
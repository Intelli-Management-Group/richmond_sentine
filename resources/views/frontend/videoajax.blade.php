@if($video->isYoutubeVideo=='0')
    <video src="{{ '../../uploads/video/'.$video->videoFile }}"
           poster="{{ '../../uploads/video_thumbnail/'.$video->videoThumbnail }}" controls
           disablepictureinpicture controlslist="nodownload" frameborder="0" autoplay class="w-100">
        <p>If you are reading this, it is because your browser does not support the HTML5 video element.
        </p>
    </video>
@else
    <iframe width="100%" height="464" src="https://www.youtube.com/embed/{!!$video->youtubeUrl!!}?controls=0" frameborder="0" allow="autoplay;" allowfullscreen></iframe>
{{--    <iframe width="100%" height="464" src="http://www.youtube.com/embed/{!!$video->youtubeUrl!!}?autoplay=1" frameborder="0" allowfullscreen></iframe>--}}
@endif
<div id="heading">
<p class="color-blue font-weight-light my-4">{!! $video->heading !!}</p>
<div class="d-inline-block mb-4">
    <!--
    <div class="d-inline-block">
        <img src="{{asset("uploads/team/$author->profileImage")}}">
    </div>
    -->
    <div class="d-inline-block align-middle color-gray">
        <p class="mb-1">Video By <a href="" class="color-lightblue">{!! $author->name !!}</a></p>
        <p class="mb-1">Published {!! date(" g:i T, D F j, Y",strtotime($video->publishDate)) !!} </p>
    </div>
</div>
</div>

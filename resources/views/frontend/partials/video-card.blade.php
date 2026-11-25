{{-- One card in a "videos you should see" carousel (home and category pages). --}}
<div class="video-inner">
    <a href="{!! url('/videos', [$video->id, str_slug(str_limit($video->heading, 50), '-')]) !!}" class="d-block video-card">
        <img src="{{ asset('uploads/video_thumbnail/'.$video->videoThumbnail) }}" class="w-100 video-thumbnail" alt="{{ $video->heading }}" loading="lazy">
        @if($video->isYoutubeVideo)
        <img src="{{ asset('images/frontend/generic/icon_youtube1.png') }}" class="video-youtube-icon" alt="" aria-hidden="true">
        @elseif($video->videoDuration)
        <div class="video-btn">{!! $video->videoDuration !!}</div>
        @endif
    </a>
    <div class="video-detail">
        <a href="{!! url('/videos', [$video->id, str_slug(str_limit($video->heading, 50), '-')]) !!}">
            <p class="text-left">{{ str_limit($video->heading, 70) }}</p>
        </a>
    </div>
</div>

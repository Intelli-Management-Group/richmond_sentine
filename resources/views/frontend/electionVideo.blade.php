<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="w-100 election_video"  style="cursor: pointer;">
        <a data-video-id="{{ $value }}" class="election_video_link" style="display: block;">
            <div class="video-wrapper">
                <img src="https://img.youtube.com/vi/{{ $value }}/mqdefault.jpg" class="w-100" alt="{{ $key }}">
                <div class="">
                    <img src='../../images/frontend/generic/icon_youtube1.png' class="youtube_video_button">
                </div>
            </div>
            <div class="thumbnail-height video-headinng" style="height: auto;margin-bottom: 40px;">
                <p class="mt-2 mb-1 font-weight-light text-left">{{ str_limit($key,80) }}</p>
            </div>
        </a>
    </div>
</div>

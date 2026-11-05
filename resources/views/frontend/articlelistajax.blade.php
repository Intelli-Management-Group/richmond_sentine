{{-- articles --}}
@if(count($articles)!="")
<div class="row">
    <?php

    $numOfCols = 3;
    $rowCount = 0;
    $bootstrapColWidth = 12 / $numOfCols;
    $bootstrapColWidth1 = $bootstrapColWidth * 2;
    $bootstrapColWidth2 = 12;
    ?>

    <?php
    foreach ($articles as $article) {
        ?>

        <div class="col-md-<?php echo $bootstrapColWidth; ?>">
            <div class="article-inner" width=100% style="margin-bottom:20px;">
                <a href="{{ route('article-detail',["$article->id",str_slug("$article->heading", "-")]) }}" class="d-block">
                    <div class="throbber" title="{{ $article->heading }}">
                        @if($article->thumbnailImage=="")
                        <img data-src='{{ "../../uploads/article/$article->fileName" }}' class="img-responsive w-100">
                        @elseif($article->thumbnailImage!="")
                        <img data-src='{{ "../../uploads/video_thumbnail/$article->thumbnailImage" }}' class="img-responsive w-100">
                        <a href="{{ route('article-detail',["$article->id",str_slug("$article->heading", "-")]) }}" class="video-list"><img src='../../images/frontend/generic/icon_video_play.png' class="video-list-play-button"></a>
                        @endif
                        <div class="throbber_after"></div>
                    </div>
                    <div class="thumbnail-height">
                        <p class="mt-2 mb-1 font-weight-light text-left">{!! $article->heading !!}</p>

                        <p class=" mb-1 font-weight-light color-gray" style="font-size: 11px;">
                            @if (Carbon\Carbon::parse($article->created_at)->timestamp != Carbon\Carbon::parse($article->updated_at)->timestamp)
                            {!! Carbon\Carbon::parse($article->updated_at)->format('F j, Y') !!}
                            @else
                            {!! Carbon\Carbon::parse($article->publishDate)->format('F j, Y') !!}
                            @endif
                        </p>
                    </div>
                </a>
            </div>
        </div>
        <?php
        $rowCount++;
        if ($rowCount % $numOfCols == 0) echo '</div> <div class="row">';
    }
    ?>
</div>
@endif

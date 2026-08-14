   @if(count($videos)!="")
   <div class="row">
       <?php

        $numOfCols = 3;
        $rowCount = 0;
        $bootstrapColWidth = 12 / $numOfCols;
        $bootstrapColWidth1 = $bootstrapColWidth * 2;
        $bootstrapColWidth2 = 12;
        $count = 0;
        foreach ($videos as $v) {
            $count++;
            $vc = "videocount" . $count;
            ?>

           <div class="col-md-<?php echo $bootstrapColWidth; ?>">
               <a href="{!!url('/videos',["$v->id",str_slug(str_limit("$v->heading",50), "-")])!!}" class="d-block">
                   <div class="w-100">
                       <div class="video-wrapper">
                           <img src="{{ '../../uploads/video_thumbnail/'.$v->videoThumbnail }}" class="w-100" alt="{{ $v->heading }}">
                           @if($v->isYoutubeVideo==0)
                           <div class="video-btn">
                               {{ $v->videoDuration }}
                           </div>
                           @elseif($v->isYoutubeVideo==1)
                           <div class="video-youtube"><img src='../../images/frontend/generic/icon_youtube1.png' class="video-youtube-play-button"></div>
                           @endif
                       </div>
                       <div class="thumbnail-height video-headinng">
                       <p class="mt-2 mb-1 font-weight-light text-left">{{ str_limit($v->heading,80) }}</p>
                       <p class=" mb-1 font-weight-light color-gray" style="font-size: 11px;">
                        @if (Carbon\Carbon::parse($v->created_at)->timestamp != Carbon\Carbon::parse($v->updated_at)->timestamp)
                            {!! Carbon\Carbon::parse($v->updated_at)->format('F j, Y') !!}
                        @else
                        {!! Carbon\Carbon::parse($v->publishDate)->format('F j, Y') !!}
                       @endif
                        </p>
                        </div>
                   </div>
               </a>
           </div>
           <?php
            $rowCount++;
            if ($rowCount % $numOfCols == 0) echo '</div> <div class="row">';
        }
        ?>
   </div>
   @endif

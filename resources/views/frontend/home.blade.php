@extends('frontend.layout')
@section('content')
<div class="container">
    <div class="row mx-0">
        <div class="d-inline-block">
            <label class="label bg-green text-white">Latest News</label>
        </div>
        <div class="d-inline-block marquee">
            @foreach($latest_news as $l)
            <a href="{{ route('article-detail',["$l->id",str_slug("$l->heading", "-")]) }}">
                <p class="">{{ $l->heading }}</p>
            </a>
            @endforeach
        </div>
    </div>

    <div class="row partition">
        <!--slider-->
        <div class="col-lg-9">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php
                    $count = 0;
                    ?>
                    @foreach($top_slider as $s)

                    <li data-target="#carouselExampleControls" data-slide-to="{{$count}}" class="active"></li>
                    <?php
                    $count++;
                    ?>
                    @endforeach
                </ol>

                <div class="carousel-inner">
                    @foreach($top_slider as $key=> $s)
                    <div class="carousel-item homeslider {!!$key==0 ?" active ":" " !!}">
                        <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}">
                            @if($s->thumbnailImage=="")
                            <img src='{{ "uploads/article/$s->fileName" }}' class="d-block w-100 img-responsive" alt="{{ $s->heading }}">
                            @elseif($s->thumbnailImage!="")
                            <img src='{{ "uploads/video_thumbnail/$s->thumbnailImage" }}' class="d-block w-100 img-responsive" alt="{{ $s->heading }}">
                            <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}" class="video-top"><img src='images/frontend/generic/icon_video_play.png' class="video-top-play-button-home"></a>
                            @endif
                            <h4 class="d-block w-100 text-uppercase font-weight-light my-3 min-60">{!! $s->heading !!}</h4>
                        </a>
                    </div>
                    @endforeach
                </div>

            </div>
            <!--Ad Fadeslider -->
            <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ad_middle as $key => $adm)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!! $adm->link !!}" target="_blank">
                            <img src="{{ 'uploads/advertisement/'.$adm->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            <h3 class="text-uppercase mt-5 color-blue">Top Stories</h3>
            <div class="row">
                <!-- Community News -->
                <div class="col-md-4 mt-3">
                    <div class="border-bottom mb-3">
                        <div class="d-inline-block small-label bg-lightblue text-white">
                            community
                        </div>
                        <div class="d-inline-block faded-text">
                            {{-- See All Stories --}}
                            <a href="{!!url('/articles')."/".$category_id_community."/"."Community"!!}" class="heading-link d-inline color-gray">See All Stories</a>
                        </div>
                    </div>
                    <div>
                        @foreach($community_news as $n)
                        <div class="w-100" style="margin-bottom:20px;">
                            <div class="d-block position-relative">
                                @if($n->thumbnailImage=="")
                                <img src='{{ "uploads/article/$n->fileName" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                @elseif($n->thumbnailImage!="")
                                <img src='{{ "uploads/video_thumbnail/$n->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}" class="video-list"><img src='images/frontend/generic/icon_video_play.png' class="video-list-play-button"></a>
                                @endif
                            </div>
                            <div class="thumbnail-height">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}">
                                    <p class="mt-2 mb-1  font-weight-light text-left">{{ str_limit($n->heading,100) }}</p>
                                </a>
                                <p class=" color-gray" style="font-size: 11px;">
                                    @if (Carbon\Carbon::parse($n->created_at)->timestamp != Carbon\Carbon::parse($n->updated_at)->timestamp)
                                    {!! Carbon\Carbon::parse($n->updated_at)->format('F j, Y') !!}
                                    @else
                                    {!! Carbon\Carbon::parse($n->publishDate)->format('F j, Y') !!}
                                    @endif
                                </p>
                            </div>


                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Canada News -->

                <div class="col-md-4 mt-3">
                    <div class="border-bottom mb-3">
                        <div class="d-inline-block small-label bg-lightblue text-white">
                            canada
                        </div>
                        <div class="d-inline-block faded-text">
                            <a href="{!!url('/articles')."/".$category_id_canada."/"."Canada"!!}" class="heading-link d-inline color-gray">See All Stories</a>
                        </div>
                    </div>
                    <div>
                        @foreach($canada_news as $n)
                        <div class="w-100" style="margin-bottom:20px;">
                            <div class="d-block position-relative">
                                @if($n->thumbnailImage=="")
                                <img src='{{ "uploads/article/$n->fileName" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                @elseif($n->thumbnailImage!="")
                                <img src='{{ "uploads/video_thumbnail/$n->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}" class="video-list"><img src='images/frontend/generic/icon_video_play.png' class="video-list-play-button"></a>
                                @endif
                            </div>
                            <div class="thumbnail-height">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}">
                                    <p class="mt-2 mb-1 font-weight-light text-left">{{ str_limit($n->heading,100) }}</p>
                                </a>
                                <p class="mb-1 color-gray" style="font-size: 11px;">
                                    @if (Carbon\Carbon::parse($n->created_at)->timestamp != Carbon\Carbon::parse($n->updated_at)->timestamp)
                                    {!! Carbon\Carbon::parse($n->updated_at)->format('F j, Y') !!}
                                    @else
                                    {!! Carbon\Carbon::parse($n->publishDate)->format('F j, Y') !!}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
                <!-- International News -->

                <div class="col-md-4 mt-3">
                    <div class="border-bottom mb-3">
                        <div class="d-inline-block small-label bg-lightblue text-white">
                            international
                        </div>
                        <div class="d-inline-block faded-text">
                            <a href="{!!url('/articles')."/".$category_id_international."/"."International"!!}" class="heading-link d-inline color-gray">See All Stories</a>
                        </div>
                    </div>
                    <div>
                        @foreach($international_news as $n)
                        <div class="w-100" style="margin-bottom:20px;">
                            <div class="d-block position-relative">
                                @if($n->thumbnailImage=="")
                                <img src='{{ "uploads/article/$n->fileName" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                @elseif($n->thumbnailImage!="")
                                <img src='{{ "uploads/video_thumbnail/$n->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $n->heading }}">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}" class="video-list"><img src='images/frontend/generic/icon_video_play.png' class="video-list-play-button"></a>
                                @endif
                            </div>
                            <div class="thumbnail-height">
                                <a href="{{ route('article-detail',["$n->id",str_slug("$n->heading", "-")]) }}">
                                    <p class="mt-2 mb-1   font-weight-light text-left">{{ str_limit($n->heading,100) }}</p>
                                </a>
                                <p class="mb-1 color-gray" style="font-size: 11px;">
                                    @if (Carbon\Carbon::parse($n->created_at)->timestamp != Carbon\Carbon::parse($n->updated_at)->timestamp)
                                    {!! Carbon\Carbon::parse($n->updated_at)->format('F j, Y') !!}
                                    @else
                                    {!! Carbon\Carbon::parse($n->publishDate)->format('F j, Y') !!}
                                    @endif
                                </p>
                            </div>

                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>
        <div class="col-lg-3">
            <!--Ad Fadeslider -->
            <div class="carousel slide carousel-fade sidebar" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $ads->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ 'uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            <!--Ad Sidebar For Responsive -->
            <div class="carousel slide carousel-fade sidebarresponsive" data-ride="carousel">

                <div class="carousel-inner ">
                    @foreach($ad_sidebar_responsive as $key=>$adsr)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $adsr->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ 'uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            <div class="border-bottom my-3">
                <label class="label bg-green text-white">Trending</label>
            </div>
            @foreach($trending as $t)
            <div class="pb-3 d-block">

                <div class="w-100">
                    <div class="d-inline-block position position-relative">
                        @if($t->thumbnailImage=="")
                        <img src='{{ "uploads/article/$t->fileName" }}' class="img-responsive" width="80" height="46" alt="{{ $t->heading }}">
                        @elseif($t->thumbnailImage!="")
                        <img src='{{ "uploads/video_thumbnail/$t->thumbnailImage" }}' class="img-responsive " width="80" height="46" alt="{{ $t->heading }}">
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}" class="video-trending"><img src='images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                        @endif

                    </div>
                    <div class="d-inline-block popular-textbox">
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}" class="font-weight-light">
                            {!! $t->heading !!}
                        </a>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!--Ad Fadeslider 3-->

    <!--<div class="row partition">-->
    <!--    <div class="col-lg-12">-->
    <!--        <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">-->
    <!--            <div class="carousel-inner bottom-ad">-->
    <!--                @foreach($ad_bottom as $key => $adb)-->
    <!--                <div class="carousel-item {!!$key==0?" active ":" "!!}">-->
    <!--                    <a href="{!!$adb->link !!}" target="_blank">-->
    <!--                        <img src="{{ 'uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisment">-->
    <!--                    </a>-->
    <!--                </div>-->
    <!--                @endforeach-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- video slider-->


    <div class="row mb-4 mx-0" style="margin-top: 4rem">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue video">videos you should see</h3>
            <a href="{!!url('/videos')!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="videos-carousel">
            @foreach($videos as $video)

            <div class="video-inner">
                <a href="{!!url('/videos',[$video->id,str_slug(str_limit($video->heading,50), '-')])!!}" class="d-block">
                    <video src="{{ 'uploads/video/'.$video->videoFile }}" poster="{{ 'uploads/video_thumbnail/'.$video->videoThumbnail }}" preload="none" class="w-100">
                        <p>If you are reading this, it is because your browser does not support the HTML5 video element.
                        </p>
                    </video>
                    <div class="video-throbber hidden"><i class="fa fa-spinner fa-lg fa-spin" aria-hidden="true"></i></div>
                    <div class="video-btn">{!! $video->videoDuration !!}</div>
                    <div class="video-play-pause"><i class="fa fa-play-circle-o fa-lg" aria-hidden="true"></i></div>
                </a>
                <div class="video-detail">
                    <a href="{!!url('/videos',[$video->id,str_slug(str_limit($video->heading,50), '-')])!!}">
                        <p class="text-left" style="margin-top:-10px;">{{ str_limit($video->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!--Ad Fadeslider 3-->

    <div class="row partition">
        <div class="col-lg-12">
            <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner bottom-ad">
                    @foreach($ad_bottom as $key => $adb)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!!$adb->link !!}" target="_blank">
                            <img src="{{ 'uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisment">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <!--coupon slider -->

    <div class="row my-4 mx-0 partition d-none">
        <div class="w-100">
            <div class="col-lg-4 d-inline"><img src="{{ 'images/frontend/home/logo_couponzone_hm.png' }}" class="img-responsive" alt="Couponzone"></div>&nbsp;
            <div class="col-lg-4 d-inline"><img src="{{ 'images/frontend/home/logo_couponzone_shownsafe_hm.png' }}" class="img-responsive" alt="Couponzone"></div>&nbsp;
            <a href="{!!url('/couponzone')!!}" class="heading-link d-inline color-gray">See All Coupons</a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3 d-none">
        <div class="owl-carousel owl-theme" id="coupon-carousel">
            @foreach($coupons as $coupon)
            <div class="coupon-inner">
                <a href="{!!url('/coupondetail').'/'.$coupon->id!!}"><img src="{{ $coupon->thumbnailImage }}" alt="{{ $coupon->heading }}"></a>
                <div class="coupon-detail">
                    <div class="head_name">
                        <p class="coupon_head text-left">{!! $coupon->heading !!}</p>
                        <p class="company_name text-left">{!! $coupon->companyName!!}</p>
                    </div>
                    <p class="price"><span class="Original_Price">C${!! $coupon->originalPrice !!}</span> &nbsp;<span class="Discount_Price">C${!! $coupon->discountPrice !!}</span></p>
                    <?php
                    $less = $coupon->originalPrice - $coupon->discountPrice;
                    $discount_price = (100 * $less) / $coupon->originalPrice;
                    $offerDetail = (empty($coupon->offerDetail) ? (round($discount_price) . "% OFF") : $coupon->offerDetail);
                    ?>
                    <div><span class="discount-box">{!!$offerDetail!!}</span></div>
                </div>
            </div>
            @endforeach

        </div>
    </div>


</div>
<script src="{!! asset('js/owl.carousel.js') !!}"></script>
<script>
    $('#videos-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        navText: ["<img src='images/frontend/generic/btn_arrow_l_0.png'>", "<img src='images/frontend/generic/btn_arrow_r_0.png'>"],
        navElement: 'div',
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                autoplay: false,
                items: 1
            },
            576: {

                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    })
    $('#coupon-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        navText: ["<img src='images/frontend/generic/btn_arrow_l_0.png'>", "<img src='images/frontend/generic/btn_arrow_r_0.png'>"],
        navElement: 'div',
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            576: {

                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    })
</script>

@endsection

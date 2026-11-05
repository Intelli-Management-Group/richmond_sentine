@extends('frontend.layout')
@section('content')
<div class="container">
    <!--heading-->
    <div class="row mx-0 my-4 justify-content-center">
        <div>
            <div class=" coupon-image w-100">
                <img src="{{ '../uploads/coupon/logo_couponzone.png' }}" class="img-fluid d-inline-block" alt="coupons"><span class="color-blue coupon-title font-weight-bold"> JUST</span><img src="../images/frontend/home/logo_couponzone_shownsafe_hm.png" class="img-fluid d-inline-block" alt="coupons">
            </div>
        </div>
    </div>

    <div class="d-block">
        <ul id="catList">
            <li class="active"><a id="allcoupon" href="#" onclick="javascript:getCategoryWiseCoupon('all')">All <span class="sr-only">(current)</span></a></li>
            @foreach($coupon_category as $cc)
            <li>
                <a href="javascript:void(0)" onclick="javascript:getCategoryWiseCoupon('{!!$cc->id!!}')">{!!$cc->name!!}</a>
            </li>
            @endforeach
        </ul>
    </div>

    <!--coupons-->
    <div class="coupons">

    </div>

    <!--Ad Fadeslider -->

    <div class="row mt-6">
        <div class="col-lg-12">
            <div id="carouselFadead" class="carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ad_bottom as $key => $adb)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!! $adb->link !!}" target="_blank">
                            <img src="{{ '../uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Advertisment">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <!-- community news-->

    <div class="row mb-4 mx-0" style="margin-top: 4rem">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more community news</h3> &nbsp;
            <a href="{!!url('/articles')."/".$category_id_community."/"."Community"!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="community-carousel">
            @foreach($community_news as $community)
            <div class="community-inner">
                <div class="d-block position-relative">
                    <a href="{{ route('article-detail',["$community->id",str_slug("$community->heading", "-")]) }}">
                        {{-- <img src="{{asset('uploads/article/'.$community->landscapeImage)}}" alt="Card image cap"> --}}
                        @if($community->thumbnailImage=="")
                        <img src='{{ "../uploads/article/$community->fileName" }}' class="img-responsive w-100" alt="{{ $community->heading }}">
                        @elseif($community->thumbnailImage!="")
                        <img src='{{ "../uploads/video_thumbnail/$community->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $community->heading }}">
                        <a href="{{ route('article-detail',["$community->id",str_slug("$community->heading", "-")]) }}" class="video-bottom"><img src='../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                        @endif
                    </a>
                </div>
                <div class="community-detail">
                    <a href="{{ route('article-detail',["$community->id",str_slug("$community->heading", "-")]) }}">
                        <p class="mt-2 text-left">{{ str_limit($community->heading,100) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- business news-->

    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more business news</h3> &nbsp;
            <a href="{!!url('/articles')."/".$category_id_business."/"."Community"!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>

    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="business-carousel">
            @foreach($business_news as $business)
            <div class="business-inner">
                <div class="d-block position-relative">
                    @if($business->thumbnailImage=="")
                    <img src='{{ "../uploads/article/$business->fileName" }}' class="img-responsive w-100" alt="{{ $business->heading }}">
                    @elseif($business->thumbnailImage!="")
                    <img src='{{ "../uploads/video_thumbnail/$business->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $business->heading }}">
                    <a href="{{ route('article-detail',["$business->id",str_slug("$business->heading", "-")]) }}" class="video-bottom"><img src='{{ "../images/frontend/generic/icon_video_play.png" }}' class="video-overlay-play-button"></a>
                    @endif
                </div>
                <div class="business-detail">
                    <a href="{{ route('article-detail',["$business->id",str_slug("$business->heading", "-")]) }}">
                        <p class="mt-2 text-left">{{ str_limit($business->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- international news-->

    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more international news</h3>
            <a href="{!!url('/articles')."/".$category_id_international."/"."International"!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="international-carousel">
            @foreach($international_news as $international)
            <div class="international-inner">
                <div class="d-block position-relative">
                    @if($international->thumbnailImage=="")
                    <img src='{{ "../uploads/article/$international->fileName" }}' class="img-responsive w-100" alt="{{ $international->heading }}">
                    @elseif($international->thumbnailImage!="")
                    <img src='{{ "../uploads/video_thumbnail/$international->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $international->heading }}">
                    <a href="{{ route('article-detail',["$international->id",str_slug("$international->heading", "-")]) }}" class="video-bottom"><img src='../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                    @endif
                </div>
                <div class="international-detail">
                    <a href="{{ route('article-detail',["$international->id",str_slug("$international->heading", "-")]) }}">
                        <p class="mt-2 text-left">{{ str_limit($international->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- sports news-->

    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more sports news</h3>
            <a href="{!!url('/articles')."/".$category_id_sports."/"."International"!!}" class=" seeall heading-link d-inline color-gray">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="sports-carousel">
            @foreach($sports_news as $s)
            <div class="sports-inner">
                <div class="d-block position-relative">
                    @if($s->thumbnailImage=="")
                    <img src='{{ "../uploads/article/$s->fileName" }}' class="img-responsive w-100" alt="{{ $s->heading }}">
                    @elseif($s->thumbnailImage!="")
                    <img src='{{ "../uploads/video_thumbnail/$s->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $s->heading }}">
                    <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}" class="video-bottom"><img src='../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                    @endif
                </div>
                <div class="sports-detail">
                    <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}">
                        <p class="mt-2 text-left">{{ str_limit($s->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<script type="text/javascript">
    $("#allcoupon").click();

    function getCategoryWiseCoupon(id) {
        $.ajax({
            url: '{!! url("/couponzone") !!}',
            type: 'POST',
            data: {
                id: id,
            },
            success: function(data) {
                if (data.html == "") {
                    $('.coupons').html('No Coupons Available');
                } else {
                    $('.coupons').html(data.html);
                }

            }
        });
    }
</script>
<script src="js/owl.carousel.js"></script>
<script>
    $('#community-carousel,#business-carousel,#international-carousel,#sports-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        navText: ["<img src='../images/frontend/generic/btn_arrow_l_0.png'>", "<img src='../images/frontend/generic/btn_arrow_r_0.png'>"],
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

@extends('frontend.layout')
@section('content')
<div class="container">

    <!--heading-->
    <div class="row mx-0">
        <h1 class="page-title"><span>Videos</span></h1>
    </div>
    {{-- top --}}
    <div class="row mt-5">
        <!--slider-->
        <div class="col-lg-9">
            <div id="video">
                {!! (isset($view)) ? ($view) : ""!!}
            </div>


            <!--Ad Fadeslider -->
            <div id="detail">
                <div class="ad_carousel carousel slide carousel-fade mt-2 mb-5" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($ad_middle as $key => $adm)
                        <div class="carousel-item {!!$key==0?" active ":" "!!}">
                            <a href="{!! $adm->link !!}" target="_blank">
                                <img src="{{ '../../uploads/advertisement/'.$adm->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisement">
                            </a>
                        </div>
                        @endforeach

                    </div>
                </div>
                <h3 class="text-uppercase my-3 color-blue">Latest Videos</h3>
                <div class="videos">
                    @include("frontend.videoPagination",["videos" => $videos])
                </div>
                @if($videos->count() != $videos->total())
                <div class="row mt-3">
                    <div class="float-left col-md-2 previous_link_container">
                        @if($videos->onFirstPage())
                        <p><img src="../../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                        @else
                        <a href="{{$videos->previousPageUrl()}}" id="previous_link">
                            <p><img src="../../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                        </a>
                        @endif
                    </div>
                    <div class="float-left col-md-8 links text-center">
                        {{ $videos->links('frontend.paginationlink') }}

                    </div>
                    <div class="float-right col-md-2 text-right next_link_container">
                        @if($videos->hasMorePages())
                        <a href="{{$videos->nextPageUrl()}}" id="next_link">
                            <p>Next<img src="../../images/frontend/generic/btn_arrow_r_0.png" class="ml-2"></p>
                        </a>
                        @else
                        <p>Next<img src="../../images/frontend/generic/btn_arrow_r_0.png" class="ml-2"></p>
                        @endif

                    </div>
                </div>
                @endif
            </div>

        </div>

        <div class="col-lg-3">
            <!--Ad Fadeslider -->
            <div class="carousel slide carousel-fade sidebar" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $ads->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../../uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            <!--Ad Sidebar For Responsive -->
            <div class="carousel slide carousel-fade sidebarresponsive" data-ride="carousel">
                <div class="carousel-inner sidebarresponsive">
                    @foreach($ad_sidebar_responsive as $key=>$adsr)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $adsr->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../../uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
            <div class="border-bottom my-3 tr">
                <label class="label bg-green text-white">Trending</label>
            </div>
            @foreach($trending as $t)
            <div class="pb-3 d-block trendingnews">
                <div class="w-100">
                    <div class="d-inline-block position-relative">
                        @if($t->thumbnailImage=="")
                        <img src='{{ "../../uploads/article/$t->fileName" }}' class="img-responsive" width="80" height="46" alt="{{ $t->heading }}">
                        @elseif($t->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/$t->thumbnailImage" }}' class="img-responsive" width="80" height="46" alt="{{ $t->heading }}">
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}" class="video-trending"><img src='../../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                        @endif

                    </div>
                    <div class="d-inline-block popular-textbox">
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }} " class="font-weight-light">
                            {!! $t->heading !!}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <!--Ad Fadeslider 3-->

    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner bottom-ad">
                    @foreach($ad_bottom as $key => $adb)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!! $adb->link !!}" target="_blank"><img src="{{ '../../uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Advertisement">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Community news slider-->

    <div class="row mb-4 mt-6 mx-0">
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
                        @if($community->thumbnailImage=="")
                        <img src='{{ "../../uploads/article/$community->fileName" }}' class="img-responsive w-100" alt="{{ $community->heading }}">
                        @elseif($community->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/$community->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $community->heading }}">
                        <a href="{{ route('article-detail',["$community->id",str_slug("$community->heading", "-")]) }}" class="video-bottom"><img src='../../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
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


    <!-- Canada news slider-->

    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue canada">See more canada news</h3>
            <a href="{!!url('/articles')."/".$category_id_canada."/"."Canada"!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="canada-carousel">
            @foreach($canada_news as $canada)
            <div class="canada-inner">
                <div class="d-block position-relative">
                    @if($canada->thumbnailImage=="")
                    <img src='{{ "../../uploads/article/$canada->fileName" }}' class="img-responsive w-100" alt="{{ $canada->heading }}">
                    @elseif($canada->thumbnailImage!="")
                    <img src='{{ "../../uploads/video_thumbnail/$canada->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $canada->heading }}">
                    <a href="{{ route('article-detail',["$canada->id",str_slug("$canada->heading", "-")]) }}" class="video-bottom"><img src='../../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
                    @endif
                </div>
                <div class="canada-detail">
                    <a href="{{ route('article-detail',["$canada->id",str_slug("$canada->heading", "-")]) }}">
                        <p class="mt-2 text-left">{{ str_limit($canada->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>


    <!-- International news slider-->



    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more international news</h3> &nbsp;
            <a href="{!!url('/articles')."/".$category_id_international."/"."International"!!}" class="seeall heading-link d-inline color-gray">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="international-carousel">
            @foreach($international_news as $international)
            <div class="international-inner">
                <div class="d-block position-relative">
                    @if($international->thumbnailImage=="")
                    <img src='{{ "../../uploads/article/$international->fileName" }}' class="img-responsive w-100" alt="{{ $international->heading }}">
                    @elseif($international->thumbnailImage!="")
                    <img src='{{ "../../uploads/video_thumbnail/$international->thumbnailImage" }}' class="img-responsive w-100" alt="{{ $international->heading }}">
                    <a href="{{ route('article-detail',["$international->id",str_slug("$international->heading", "-")]) }}" class="video-bottom"><img src='../../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
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
</div>
<script src="{!! asset('js/owl.carousel.js') !!}"></script>
<script>
    $(document).ready(function() {
        var height1 = $("#heading").height();
        var height2 = $("#detail").height();
        var height = height1 + height2;
        var treheight = $(".trendingnews").outerHeight();
        var totaltrending = Math.floor(height / treheight);
        $(".trendingnews:gt(" + (totaltrending - 1) + ")").remove();

    });
    $('#canada-carousel,#international-carousel,#community-carousel,#snapshots-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        navText: ["<img src='../../images/frontend/generic/btn_arrow_l_0.png'>", "<img src='../../images/frontend/generic/btn_arrow_r_0.png'>"],
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
    });
    $(document).on("click", ".links li a", function(e) {
        e.preventDefault();
        paginationClick(this);
    });
    $(document).on("click", "#next_link,#previous_link", function(e) {
        e.preventDefault();
        paginationClick(this);
    });

    function paginationClick(a_link) {
        $.ajax({
            url: $(a_link).attr("href"),
            success: function(data) {
                if (data != "") {
                    $('.videos').html(data.html);
                    if (data.next_url != "") {
                        $(".next_link_container").html(data.next_url);
                    }
                    if (data.previous_url != "") {
                        $(".previous_link_container").html(data.previous_url);
                    }
                    $(".links").html(data.links);
                }
            }
        });
    }
</script>
@endsection

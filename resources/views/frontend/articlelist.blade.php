@extends('frontend.layout')


@section('content')
<div class="container">
    <!--heading-->
    <div class="row mx-0">
        <h1 class="page-title"><span>{!!$category_name->name!!}</span></h1>
    </div>

    {{-- top --}}
    <div class="row mt-5">
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
                        @if($s->thumbnailImage=="")
                        <img src='{{ "../../uploads/article/$s->fileName" }}' class="d-block w-100 img-responsive" alt="{{ $s->heading }}">
                        @elseif($s->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/$s->thumbnailImage" }}' class="d-block w-100 img-responsive" alt="{{ $s->heading }}">
                        {{-- <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}" class="video-top"><img src='{{asset("images/frontend/generic/icon_video_play.png")}}' class="video-top-play-button"></a> --}}
                        <img src='../../images/frontend/generic/icon_video_play.png' class="video-top-play-button" data-toggle="modal" data-target="#exampleModal" onclick="javascript:videoDetail('{{ '../../uploads/video_thumbnail/'.$s->thumbnailImage }}','{{ '../../uploads/article/'.$s->fileName }}')">
                        @endif
                        {{-- <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}"> --}}
                        <a href="{{ route('article-detail',["$s->id",str_slug("$s->heading", "-")]) }}">
                            <h3 class="d-block w-100 text-uppercase font-weight-light my-3 min-60">{!! $s->heading !!}
                            </h3>
                        </a>
                    </div>
                    @endforeach
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <video src="" poster="" controls disablepictureinpicture controlslist="nodownload" frameborder="0" autoplay class="w-100">

                                </div>
                                <div class="modal-footer">


                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div id="detail">
                <!--Ad Fadeslider -->
                <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
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
                <h3 class="text-uppercase mt-5 mb-3 color-blue">Latest Stories</h3>

                <div class="articles">
                    @include("frontend.articlelistajax",["articles" => $articles])
                </div>
                @if($articles->count() != $articles->total())
                <div class="row mt-3">
                    <div class="float-left col-md-2 previous_link_container">
                        @if($articles->onFirstPage())
                        <p><img src="../../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                        @else
                        <a href="{{$articles->previousPageUrl()}}" id="previous_link">
                            <p><img src="../../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                        </a>
                        @endif
                    </div>
                    <div class="float-left col-md-8 links text-center">
                        {{ $articles->links('frontend.paginationlink') }}

                    </div>
                    <div class="float-right col-md-2 text-right next_link_container">
                        @if($articles->hasMorePages())
                        <a href="{{$articles->nextPageUrl()}}" id="next_link">
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
            <div class="border-bottom my-3">
                <label class="label bg-green text-white">Trending</label>
            </div>
            @foreach($trending as $t)
            <div class="pb-3 d-block trendingnews">
                <div class="w-100">
                    <div class="d-inline-block position-relative">
                        @if($t->thumbnailImage=="")
                        <img src='{{ "../../uploads/article/$t->fileName" }}' class="img-responsive" width="80" height="46" alt="{{ $t->heading }}">
                        @elseif($t->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/$t->thumbnailImage" }}' class="img-responsive " width="80" height="46" alt="{{ $t->heading }}">
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}" class="video-trending"><img src='../../images/frontend/generic/icon_video_play.png' class="video-overlay-play-button"></a>
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

    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner bottom-ad">
                    @foreach($ad_bottom as $key => $adb)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!! $adb->link!!}" target="_blank">
                            <img src="{{ '../../uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- videos slider-->
    @if("$category_name->name"!='Videos')
    <div class="row mb-4 mx-0" style="margin-top: 4rem">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">videos you should see</h3> &nbsp;
            <a href="{!!url('/videos')!!}" class="heading-link d-inline color-gray seeall">See All </a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
        <div class="owl-carousel owl-theme" id="videos-carousel">
            @foreach($videos as $video)
            <div class="video-inner">
                <a href="{!!url('/videos',["$video->id",str_slug(str_limit($video->heading,50), "-")])!!}">
                    <video src="{{ '../../uploads/video/'.$video->videoFile }}" class="w-100" poster="{{ '../../uploads/video_thumbnail/'.$video->videoThumbnail }}" preload="none">
                        <p>If you are reading this, it is because your browser does not support the HTML5 video element.
                        </p>
                    </video>
                    <div class="video-throbber hidden"><i class="fa fa-spinner fa-lg fa-spin" aria-hidden="true"></i></div>
                    <div class="video-btn">{!! $video->videoDuration !!}</div>
                    <div class="video-play-pause"><i class="fa fa-play-circle-o fa-lg" aria-hidden="true"></i></div>
                </a>
                <div class="video-detail">
                    <a href="{!!url('/videos',[$video->id,str_slug(str_limit($video->heading,50), '-')]) !!}">
                        <p style="margin-top:-10px;" class="text-left">{{ str_limit($video->heading,70) }}</p>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Community news slider-->
    @if (!in_array($category_name->name,array('Latest News','Arts & Culture','Sports','Business')))
    <div class="row my-4 mx-0">
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
    @endif

    <!-- Canada news slider-->
    @if (!in_array($category_name->name,array('Canada','Provincial News','National News')))
    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue canada">See more canada news</h3>
            <a href="{!!url('/articles')."/".$category_id_canada."/"."Canada"!!}" class="heading-link d-inline color-gray">See All </a>
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
    @endif

    <!-- International news slider-->

    @if("$category_name->name"!='International')

    <div class="row my-4 mx-0">
        <div class="w-100 border-bottom">
            <h3 class="text-uppercase d-inline color-blue">See more international news</h3> &nbsp;
            <a href="{!!url('/articles')."/".$category_id_international."/"."International"!!}" class="heading-link d-inline color-gray seeall">See All </a>
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
    @endif


</div>

<script src="{!! asset('js/owl.carousel.js') !!}"></script>
<script type="text/javascript">
    $(document).on("click", ".links li a", function(e) {
        e.preventDefault();
        paginationClick(this);
    });
    $(".article-inner .throbber").each(function() {
        $(this).height($(this).width() / 2);
    });

    function videoDetail(thumbnailImage, fileName) {
        var fileName = fileName;
        var poster = thumbnailImage;
        $('.modal-body').find('video').attr('src', fileName)[0].load();

    }
    $("#exampleModal").on("hide.bs.modal", function() {
        $('.modal-body').find('video').removeAttr('src')[0].load();
    });

    $('#canada-carousel,#international-carousel,#community-carousel,#snapshots-carousel,#videos-carousel').owlCarousel({
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
    $(document).on("click", "#next_link,#previous_link", function(e) {
        e.preventDefault();
        paginationClick(this);
    });

    function paginationClick(a_link) {
        $.ajax({
            url: $(a_link).attr("href"),
            success: function(data) {
                if (data != "") {
                    $('.articles').html(data.html);
                    if (data.next_url != "") {
                        $(".next_link_container").html(data.next_url);
                    }
                    if (data.previous_url != "") {
                        $(".previous_link_container").html(data.previous_url);
                    }
                    $(".links").html(data.links);
                    $(".article-inner .throbber").each(function() {
                        $(this).height($(this).width() / 2);
                    });
                    lazyLoadArticles();
                }
            }
        });
    }
</script>
@endsection

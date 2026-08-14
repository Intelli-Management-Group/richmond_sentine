@extends('frontend.layout')
@section('content')
<div class="container">
    <div class="row mx-0">
        <h1 class="page-title"><span>{!! $cat->name !!}</span></h1>
    </div>
    <div class="row mt-4">
        <div class="col-lg-9" id="articledetail">

            <div id="topArticles" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">

                    @foreach($top_slider as $key => $s)
                    @if(count($top_slider) > 1)
                    <li data-target="#topArticles" data-slide-to="{{$key}}" class="active"></li>
                    @endif

                    @endforeach
                </ol>

                <div class="carousel-inner">
                    @foreach($top_slider as $key=> $s)
                    <div class="carousel-item {!!$key==0 ?" active ":" " !!}">
                        @if($s->thumbnailImage=="")
                        <img src='{{ "../../uploads/article/$s->fileName" }}' class="d-block w-100 img-responsive detail_image" alt="{{ $s->heading }}">
                        @elseif($s->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/$s->thumbnailImage" }}' class="d-block w-100 img-responsive detail_image" alt="{{ $s->heading }}">
                        <img src='{{ "../../images/frontend/generic/icon_video_play.png" }}' class="video-top-play-button" data-toggle="modal" data-target="#exampleModal" onclick="javascript:videoDetail('{{ '../../uploads/video_thumbnail/'.$s->thumbnailImage }}','{{ '../../uploads/article/'.$s->fileName }}')">
                        @endif
                        <p class="color-blue mt-3 mb-1 " style="margin-block-start:0px; margin-block-end:0px;">{!! $s->caption !!}</p>
                        <p class="mb-4 small font-italic">{!! $s->credit !!}</p>

                    </div>
                    @endforeach
                    <!-- Modal -->

                </div>
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


            <div id="detail">
                <h1 class="font-weight-light my-4 article-headline">{!! $article->heading !!}</h1>
                <div class="d-inline-block">
                    <div class="d-inline-block" style="vertical-align: top;">
                        <img src="{{ getAuthorImage($author->profileImage) }}">
                    </div>
                    <div class="d-inline-block align-middle ml-3 color-gray">
                        <p class="mb-1">By <a href="" class="color-lightblue">{!! $author->name !!}</a></p>
                        <p class="mb-1">Published {!! Carbon\Carbon::parse($article->publishDate)->format('g:i T, D F j, Y') !!}</p>
                        @if (Carbon\Carbon::parse($article->created_at)->timestamp != Carbon\Carbon::parse($article->updated_at)->timestamp)
                        <p class="mb-1">Last Updated: {!! Carbon\Carbon::parse($article->updated_at)->format('g:i T, D F j, Y') !!}</p>
                        @endif
                        {{-- @if (Carbon\Carbon::parse($article->created_at)->timestamp != Carbon\Carbon::parse($article->updated_at)->timestamp)
                            {!! Carbon\Carbon::parse($article->updated_at)->format('g:i T, D F j, Y') !!}
                            @else
                            {!! Carbon\Carbon::parse($article->publishDate)->format('g:i T, D F j, Y') !!}
                            @endif --}}
                    </div>
                </div>
                <ul class="socialmedia-icons d-inline-block float-right">
                    <li class="socialmedia-list">

                        <a href="{{ route('article-detail',["$article->id",str_slug("$article->heading", "-")]) }} " class="fb"><img src="../../images/frontend/generic/icon_fb.png" alt="Facebook"></a>

                    </li>
                    <li class="socialmedia-list">

                        <a href="{{ route('article-detail',["$article->id",str_slug("$article->heading", "-")]) }}" class="tw"><img src="../../images/frontend/generic/icon_twitter.png" alt="Twitter"></a>

                    </li>
                    <li class="socialmedia-list">
                        <a class="email" href="#">
                            <img src="../../images/frontend/generic/btn_email.png" alt="Email">
                        </a>
                    </li>
                    <li class="socialmedia-list">
                        <a href="#" type="text/css" class="print">
                            <img src="../../images/frontend/generic/btn_print.png" alt="Print">
                        </a>
                    </li>
                </ul>
                <h5 class="font-weight-light font-italic my-4 article-summary">{!! $article->summary !!}</h5>
                <!--Ad Fadeslider -->
                <div class="ad_carousel carousel slide carousel-fade mt-4" data-ride="carousel">
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

                @php
                $description = preg_replace('/<style[\s\S]+?style>/', '', $article->description);
                    $description = preg_replace('/\s*face\s*=\s*[\"\'][^\"|\']*[\"\']/', '$1', $description);
                    @endphp
                    <div class="article_description">
                        <p>{!! preg_replace('/\s*style\s*=\s*[\"\'][^\"|\']*[\"\']/', '$1', $description) !!}</p>
                    </div>
            </div>
        </div>
        <div class="col-lg-3">
            <!--Ad Fadeslider -->
            <div class="carousel slide carousel-fade sidebar" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $ads->link!!}" target="_blank">
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
                        <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}" class="video-trending"><img src='{{ "../../images/frontend/generic/icon_video_play.png" }}' class="video-overlay-play-button"></a>
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
    <!--coupon slider -->

    <div class="row my-4 partition d-none">
        <div class="w-100">
            <div class="col-lg-4 d-inline"><img src="../../images/frontend/home/logo_couponzone_hm.png" alt="Couponzone" class="img-responsive"></div>&nbsp;
            <div class="col-lg-4 d-inline"><img src="../../images/frontend/home/logo_couponzone_shownsafe_hm.png" alt="Couponzone" class="img-responsive"></div>&nbsp;
            <a href="{!!url('/couponzone')!!}" class="heading-link d-inline color-gray">See All Coupons</a>
        </div>
    </div>
    <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3 d-none">
        <div class="owl-carousel owl-theme" id="coupon-carousel">
            @foreach($coupons as $coupon)
            <div class="coupon-inner">
                <a href="{!!url('/coupondetail').'/'.$coupon->id!!}"><img src="{{ '../../'.$coupon->thumbnailImage }}" alt="{!! $coupon->heading !!}"></a>
                <div class="coupon-detail">
                    <div class="head_name">
                        <p class="coupon_head text-left">{!! $coupon->heading !!}</p>
                        <p class="company_name text-left">{!! $coupon->companyName!!}</p>
                    </div>
                    <p class="price"><span class="Original_Price">C${!! $coupon->originalPrice !!}</span> &nbsp;<span class="Discount_Price">C${!! $coupon->discountPrice !!}</span></p>
                    <?php
                    $less =  $coupon->originalPrice - $coupon->discountPrice;
                    $discount_price = (100 * $less) / $coupon->originalPrice;
                    $offerDetail = (empty($coupon->offerDetail) ? (round($discount_price) . "% OFF") : $coupon->offerDetail);
                    ?>
                    <div><span class="discount-box">{!!$offerDetail!!}</span></div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
    <!--Ad Fadeslider 3-->

    <div class="row my-5">
        <div class="col-lg-12">
            <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner bottom-ad">
                    @foreach($ad_bottom as $key => $adb)
                    <div class="carousel-item {!!$key==0?" active ":" "!!}">
                        <a href="{!! $adb->link !!}" target="_blank">
                            <img src="{{ '../../uploads/advertisement/'.$adb->imageName }}" class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Community news slider-->
    @if (!in_array($cat->name,array('Latest News','Arts & Culture','Sports','Business')))
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
                        <img src='{{ "../../uploads/article/".$community->fileName }}' class="img-responsive w-100" alt="{{ $community->heading }}">
                        @elseif($community->thumbnailImage!="")
                        <img src='{{ "../../uploads/video_thumbnail/".$community->thumbnailImage }}' class="img-responsive w-100" alt="{{ $community->heading }}">
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
    @if (!in_array($cat->name,array('Canada','Provinvcial News','National News')))
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

                    <img src='{{ "../../uploads/article/".$canada->fileName }}' class="img-responsive w-100" alt="{{ $canada->heading }}">
                    @elseif($canada->thumbnailImage!="")
                    <img src='{{ "../../uploads/video_thumbnail/".$canada->thumbnailImage }}' class="img-responsive w-100" alt="{{ $canada->heading }}">
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

    @if("$cat->name"!='International')

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
                    <img src='{{ "../../uploads/article/".$international->fileName }}' class="img-responsive w-100" alt="{{ $international->heading }}">
                    @elseif($international->thumbnailImage!="")
                    <img src='{{ "../../uploads/video_thumbnail/".$international->thumbnailImage }}' class="img-responsive w-100" alt="{{ $international->heading }}">
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
<script src="{!! asset('js/sharing.jquery.js') !!}"></script>
<script src="{!! asset('js/jquery.shareemail.js') !!}"></script>
<script src="{!! asset('js/jquery.tmpl.js') !!}"></script>
<script type="text/javascript">
    $(document).on("click", ".links li a", function(e) {
        e.preventDefault();
        paginationClick(this);
    });
    var maxheight = 0;
    var image_width = {!! config('constants.article_image_width') !!};
    var image_height = {!! config('constants.article_image_height') !!};
    var calculated_height = Math.round(($(".detail_image").width() * image_height) / image_width);
    $(".detail_image").css("min-height", calculated_height + "px");

    $("#topArticles .carousel-item").each(function() {
        var outerHeight = $(this).outerHeight(true);
        if (outerHeight > maxheight) {
            maxheight = outerHeight;
        }
    });
    $("#topArticles,#topArticles .carousel-item").height(maxheight);
    $(".detail_image").css("min-height", "");

    function videoDetail(thumbnailImage, fileName) {
        var fileName = fileName;
        var poster = thumbnailImage;
        $('.modal-body').find('video').attr('src', fileName)[0].load();

    }
    $("#exampleModal").on("hide.bs.modal", function() {
        $('.modal-body').find('video').removeAttr('src')[0].load();
    });



    $(".tw").sharing("twitter");
    $(".fb").sharing("facebook");
    $(".email").shareEmail({
        displayText: ''
    });

    $(document).ready(function() {

        var height = $("#detail").height();
        var treheight = $(".trendingnews").outerHeight();
        var totaltrending = Math.floor(height / treheight);
        // console.log(".trendingnews:gt(" + totaltrending + ")");
        $(".trendingnews:gt(" + totaltrending + ")").remove();

        $('.print').click(function() {

            var prtContent = $("#articledetail").clone();
            $(prtContent).find("#topArticles").css("height", "");
            $(prtContent).find(".carousel-indicators,.ad_carousel ,#exampleModal,.socialmedia-icons").remove();
            var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            WinPrint.document.write($(prtContent).html());
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();
        });


    });

    $('#canada-carousel,#international-carousel,#community-carousel,#coupon-carousel').owlCarousel({
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
    })
</script>
@endsection

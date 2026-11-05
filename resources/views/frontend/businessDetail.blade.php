@extends('frontend.layout')
@section('content')
    <div class="container">
        <div class="row mt-5">
            <div class="col-lg-9">
                <div id="detail">
                    @if(count($business_photos) > 0)
                        <div class="row mb-5">
                            <div class="col-lg-12">
                                <div id="bannerimage" class="carousel slide carousel-fade" data-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($business_photos as $key=> $banner)
                                            <div
                                                class="carousel-item {!! (array_keys((array)$business_photos)[0] == $key) ?'active':'' !!}">
                                                <img class="d-block w-100 img-responsive"
                                                     src="{{ asset("uploads/business/".$banner->businessId."/".$banner->fileName)}}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="float-left text-left mr-3">
                                <img src="{!! $business_data->logo !!}"
                                     alt="{!! $business_data->name !!}" height="100"/>
                            </div>
                            <div class="float-left">
                                <h1 class="color-blue font-weight-light my-2">{!! $business_data->name !!}</h1>
                                <div class="d-flex business-category">
                                    @foreach($business_categories as $c)
                                        <div><h6 class="mr-1 temp">{!! $c !!}</h6></div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-lg-12">
                            {!! $business_data->about !!}
                        </div>
                    </div>
                    <div class="row mt-5 mb-5">
                        <div class="col-md-5">
                            <table cellpadding="5" class="w-100 word-break">
                                <tbody>
                                <tr>
                                    <td>
                                        <img src='{!! asset('images/frontend/generic/icon_directions.png') !!}'
                                             alt="Address"
                                             height="25">
                                    </td>
                                    <td>
                                        <h6 class="head_strip text-uppercase font-weight-bold">address :</h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <p class="text-left">{!!$business_data->address!!}</p>
                                    </td>
                                </tr>
                                @if(!empty($business_data->website))
                                    <tr>
                                        <td>
                                            <img src='{!! asset('images/frontend/generic/icon_website.png') !!}'
                                                 alt="Website"
                                                 height="25">
                                        </td>
                                        <td>
                                            <h6 class="head_strip text-uppercase font-weight-bold">website :</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <a href="{!!$business_data->website!!}">{!!$business_data->website!!}</a>
                                        </td>
                                    </tr>
                                @endif
                                @if($business_data->phone != "")
                                    <tr>
                                        <td>
                                            <img src='{!! asset('images/frontend/generic/icon_tel.png') !!}' alt="Phone"
                                                 style="margin-left:0px;margin-top:-10px;">
                                        </td>
                                        <td>
                                            <h6 class="head_strip text-uppercase font-weight-bold d-inline-block float-left mt-2">
                                                call :</h6>
                                            <p class="text-justify pull-left pl-1 d-inline-block float-left mt-2">{!!$business_data->phone!!}</p>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="align-top">
                                        <img src='{!! asset('images/frontend/generic/icon_hours.png') !!}'
                                             alt="Opening hours"/>
                                    </td>
                                    <td>
                                        <?php
                                        $day_name_array = config("constants.day_name");
                                        $today_day = strtoupper(Carbon\Carbon::now()->format("D"));
                                        ?>
                                        <table cellpadding="0" cellspacing="0" class="w-100">
                                            <tbody>
                                            @foreach($business_hours as $key=>$day)
                                                <tr>
                                                    <td class="w-25" valign="top">
                                            <span
                                                class="text-justify text-uppercase {!! $today_day == strtoupper($key) ? 'font-weight-bold' : '' !!}">{!! $key." :"!!}</span>
                                                    </td>
                                                    <td class="w-75">
                                                        @if(count($day) > 0)
                                                            @foreach($day as $d)
                                                                <p class="text-justify {!! $today_day == strtoupper($key) ? 'font-weight-bold' : '' !!}">
                                                                    {!! (Carbon\Carbon::parse($d->start_time)->format("h:i A")) . " to " . (Carbon\Carbon::parse($d->end_time)->format("h:i A")) !!}
                                                                </p>
                                                            @endforeach
                                                        @else
                                                            <p class="text-justify  {!! $today_day == strtoupper($key) ? 'font-weight-bold' : '' !!}">
                                                                Closed</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-7">
                            <iframe width="100%" height="425" frameborder="0" style="border:0"
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDv4daZyOfRCdRf1YtyD6hUHNe5Aeep-BM&q={!! $business_data->address !!}"
                                    allowfullscreen>
                            </iframe>
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
                                <a href="{!! $ads->link!!}" target="_blank">
                                    <img class="d-block w-100 img-responsive"
                                         src="{{ asset('uploads/advertisement/'.$ads->imageName) }}"
                                         alt="Richmond Sentinel Advertisement">
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
                                    <img class="d-block w-100 img-responsive"
                                         src="{{ asset('uploads/advertisement/'.$adsr->imageName) }}"
                                         alt="Richmond Sentinel Advertisement">
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
                                    <img src='{{ asset("uploads/article/$t->fileName") }}' class="img-responsive"
                                         width="80" height="46" alt="{{ $t->heading }}">
                                @elseif($t->thumbnailImage!="")
                                    <img src='{{ asset("uploads/video_thumbnail/$t->thumbnailImage") }}'
                                         class="img-responsive " width="80" height="46" alt="{{ $t->heading }}">
                                    <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}"
                                       class="video-trending"><img
                                            src='{{ "../../images/frontend/generic/icon_video_play.png" }}'
                                            class="video-overlay-play-button"></a>
                                @endif
                            </div>
                            <div class="d-inline-block popular-textbox">
                                <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}"
                                   class="font-weight-light">
                                    {!! $t->heading !!}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row my-4 partition">
            <div class="w-100">
                <div class="col-lg-4 d-inline"><img src="{!! url('images/frontend/home/logo_couponzone_hm.png') !!}"
                                                    alt="Couponzone" class="img-responsive"></div>&nbsp;
                <div class="col-lg-4 d-inline"><img
                        src="{!! url('../../images/frontend/home/logo_couponzone_shownsafe_hm.png') !!}"
                        alt="Couponzone" class="img-responsive"></div>&nbsp;
                <a href="{!!url('/couponzone')!!}" class="heading-link d-inline color-gray">See All Coupons</a>
            </div>
        </div>
        <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
            <div class="owl-carousel owl-theme" id="coupon-carousel">
                @foreach($coupons as $coupon)
                    <div class="coupon-inner">
                        <a href="{!!url('/coupondetail').'/'.$coupon->id!!}"><img
                                src="{{ url($coupon->thumbnailImage) }}" alt="{!! $coupon->heading !!}"></a>
                        <div class="coupon-detail">
                            <div class="head_name">
                                <p class="coupon_head text-left">{!! $coupon->heading !!}</p>
                                <p class="company_name text-left">{!! $coupon->companyName!!}</p>
                            </div>
                            <p class="price"><span class="Original_Price">C${!! $coupon->originalPrice !!}</span> &nbsp;<span
                                    class="Discount_Price">C${!! $coupon->discountPrice !!}</span></p>
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
        <div class="row my-5">
            <div class="col-lg-12">
                <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                    <div class="carousel-inner bottom-ad">
                        @foreach($ad_bottom as $key => $adb)
                            <div class="carousel-item {!!$key==0?" active ":" "!!}">
                                <a href="{!! $adb->link !!}" target="_blank">
                                    <img src="{{ asset('uploads/advertisement/'.$adb->imageName) }}"
                                         class="d-block w-100 img-responsive" alt="Richmond Sentinel Advertisement">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-4 mx-0">
            <div class="w-100 border-bottom">
                <h3 class="text-uppercase d-inline color-blue">Related stories</h3> &nbsp;
            </div>
        </div>
        <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
            <div class="owl-carousel owl-theme" id="related-articles">
                @foreach($related_articles as $article)
                    <div class="related-article-inner">
                        <div class="d-block position-relative">
                            @if($article->thumbnailImage=="" && isset($article->articleFileDetail))
                                <img src='{!! asset("uploads/article")."/".$article->articleFileDetail[0]->fileName !!}'
                                     class="img-responsive w-100" alt="{{ $article->heading }}">
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('article-detail',["$article->id",str_slug("$article->heading", "-")]) }}">
                                <p class="mt-2 text-left">{{ str_limit($article->heading,70) }}</p>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if(count($event_array) > 0)
            <div class="row my-4 mx-0">
                <div class="w-100 border-bottom">
                    <h3 class="text-uppercase d-inline color-blue">Related events</h3> &nbsp;
                </div>
            </div>
            <div class="row ml-xl-3 mr-xl-2 ml-lg-4 mr-lg-3 mx-md-4 mx-sm-3 mx-xs-3">
                <div class="owl-carousel owl-theme" id="related-events">
                    @foreach($event_array as $event)
                        <div class="related-event-inner">
                            <div class="d-block position-relative">
                                @if($event['banner_image'] != "")
                                    <img src='{!! $event['banner_image'] !!}'
                                         class="img-responsive w-100" alt="{{ $event['name'] }}">
                                @endif
                            </div>
                            <div
                                class="event-price">{!! $event["cost"] != "" ? "$".$event["cost"] : $event["price"] !!}</div>
                            <div>
                                <a href="#">
                                    <p class="mt-2 text-left">{{ str_limit($event['name'],70) }}</p>
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="mb-1 font-weight-light color-gray"
                                     style="font-size: 11px;">{!! $event['event_date'] !!}</div>
                                @if($event['link_text'] != "")
                                    <a class="mb-1 event-link" href="{!! $event['booking_link'] !!}"
                                       target="_blank">{!! $event['link_text'] !!}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <script type="text/javascript">
            $(document).ready(function () {
                var height = $("#detail").height();
                var treheight = $(".trendingnews").outerHeight();
                var totaltrending = Math.floor((height - $(".sidebar").height()) / treheight);
                $(".trendingnews:gt(" + totaltrending + ")").remove();
                $('#related-articles,#related-events,#coupon-carousel').owlCarousel({
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
            });
        </script>
@endsection

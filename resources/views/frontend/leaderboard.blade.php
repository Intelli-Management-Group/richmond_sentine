@extends('frontend.layout')
@section('content')
    <div class="container">
        <div class="row mt-5">
            <h1 class="page-title"><span>Steps challenge</span></h1>
        </div>
        <div class="row mt-5">
            <div class="col-lg-9">
                <div id="detail">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">User</th>
                                <th scope="col">Total steps</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data as $d)
                                <tr>
                                    <th scope="row">{!! $d['rank'] !!}</th>
                                    <td><img src="{!! $d['avatar'] !!}" alt="{!! $d['name'] !!}" height="30"
                                             class="mr-3">{!! $d['name'] !!}</td>
                                    <td>{!! number_format($d['totalSteps']) !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No users found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
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
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            var height = $("#detail").height();
            var treheight = $(".trendingnews").outerHeight();
            var totaltrending = Math.floor((height - $(".sidebar").height()) / treheight);
            $(".trendingnews:gt(" + totaltrending + ")").remove();
            $('#coupon-carousel').owlCarousel({
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

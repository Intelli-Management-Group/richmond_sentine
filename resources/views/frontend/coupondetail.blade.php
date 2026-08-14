@extends('frontend.layout')
@section('content')
    <div class="container">
        <!--heading-->
        <div class="row mx-0 my-4 justify-content-center">
            {{-- <div class="page-title-image"> --}}
            <div>
                {{-- <div class="position-absolute coupon-image w-100"> --}}
                <div class=" coupon-image w-100">
                    <img src="{{ '../../uploads/coupon/logo_couponzone.png' }}" class="img-fluid d-inline-block"
                         alt="coupons"><span class="color-blue coupon-title font-weight-bold"> JUST</span><img
                        src="{{ '../../images/frontend/home/logo_couponzone_shownsafe_hm.png' }}"
                        class="img-fluid d-inline-block" alt="coupons">
                </div>
            </div>
        </div>


        <div class="row partition">
            <!--slider-->
            <div class="col-lg-9">
                <div style="padding-right: 25px;">
                    <div id="bannerimage" class="carousel slide carousel-fade" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($banner_images as $key=> $banner)
                                <div class="carousel-item {!! (array_keys($banner_images)[0] == $key) ?'active':'' !!}">
                                    <img class="d-block w-100 img-responsive" src="{{ "../../".($banner)}}">
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div id="detail">
                        <div id="offerdetail" class="mt-4">
                            <h1 class="color-blue font-weight-light my-4">{!!$coupons_detail->heading !!}</h1>
                            <h6 class="head_strip text-uppercase font-weight-bold">your offer</h6>
                            <p>{!! preg_replace('/style[^>]*/', '', htmlspecialchars_decode($coupons_detail->highlights)) !!}</p>
                            <h6 class="head_strip text-uppercase font-weight-bold">the fine print</h6>
                            <p>{!! preg_replace('/style[^>]*/', '', htmlspecialchars_decode($coupons_detail->finePrints)) !!}</p>
                            <h6 class="head_strip text-uppercase font-weight-bold">
                                about&nbsp;{!!$coupons_detail->companyName!!}
                            </h6>
                            <p>{!! preg_replace('/style[^>]*|class[^>]*/', '', htmlspecialchars_decode($coupons_detail->detail)) !!}
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <table cellpadding="5">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img src='../../images/frontend/generic/icon_directions.png' width="30">
                                        </td>
                                        <td>
                                            <h6 class="head_strip text-uppercase font-weight-bold">address:</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <p class="text-left" id="address">{!!$coupons_detail->companyAddress!!}</p>
                                        </td>
                                    </tr>
                                    @if($coupons_detail->companyPhone != "")
                                        <tr>
                                            <td>
                                                <img src='../../images/frontend/generic/icon_tel.png'
                                                     style="margin-left:0px;margin-top:-15px;">
                                            </td>
                                            <td>
                                                <h6 class="head_strip text-uppercase font-weight-bold d-inline-block float-left">
                                                    call:</h6>
                                                <p class="text-justify pull-left pl-1 d-inline-block float-left">{!!$coupons_detail->companyPhone!!}</p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="align-top">
                                            <img src='../../images/frontend/generic/icon_hours.png'>
                                        </td>
                                        <td>
                                            <?php
                                            $day_name_array = array("mon" => "mon", "tue" => "tue", "wed" => "wed", "thr" => "thu", "fri" => "fri", "sat" => "sat", "sun" => "sun");
                                            ?>

                                            @foreach($daywise_data as $key=>$day)
                                                <div>
                                                    @php
                                                        $today_day = strtoupper(Carbon\Carbon::now()->format("D"));
                                                    @endphp
                                                    <p class="text-justify text-uppercase d-inline-block {!! $today_day ==strtoupper($day_name_array[$key]) ? 'font-weight-bold' : '' !!}">
                                                        {!!$day_name_array[$key].":"!!}</p>
                                                    <?php
                                                    if ($day != "") {
                                                        $time_value = explode("-", $day);
                                                        $daywise_data[$key] = (Carbon\Carbon::parse($time_value[0])->format("h:i A")) . " to " . (Carbon\Carbon::parse($time_value[1])->format("h:i A"));
                                                    } else {
                                                        $daywise_data[$key] = "Closed";
                                                    }
                                                    ?>
                                                    <p class="text-justify d-inline-block {!! $today_day == strtoupper($day_name_array[$key]) ? 'font-weight-bold' : '' !!}">
                                                        {!!$daywise_data[$key]!!}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <iframe width="100%" height="370" frameborder="0" style="border:0"
                                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDv4daZyOfRCdRf1YtyD6hUHNe5Aeep-BM&q={!! $coupons_detail->companyAddress !!}"
                                        allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <!--Ad Fadeslider 1-->
                <div id="ad1" class="carousel slide carousel-fade" data-ride="carousel">
                    <div class="ad1-inner">
                        @foreach($ad_sidebar as $key=>$ads)
                            <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                                <a href="{!! $ads->link!!}" target="_blank">
                                    <img class="d-block w-100 img-responsive"
                                         src="{{ '../../uploads/advertisement/'.$ads->imageName }}" alt="Advertisement">
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
                                    <img src='{{ "../../uploads/article/$t->fileName" }}' class="img-responsive"
                                         width="70" height="46" alt="{{ $t->heading }}">
                                @elseif($t->thumbnailImage!="")
                                    <img src='{{ "../../uploads/video_thumbnail/$t->thumbnailImage" }}'
                                         class="img-responsive " width="70" height="46" alt="{{ $t->heading }}">
                                    <a href="{{ route('article-detail',["$t->id",str_slug("$t->heading", "-")]) }}"
                                       class="video-trending"><img
                                            src='../../images/frontend/generic/icon_video_play.png'
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

        <!--Ad Fadeslider 3-->

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="ad_carousel carousel slide carousel-fade" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($ad_bottom as $key => $adb)
                            <div class="carousel-item {!!$key==0?" active ":" "!!}">
                                <a href="{!!$adb->link!!}" target="_blank">
                                    <img src="{{ '../../uploads/advertisement/'.$adb->imageName }}"
                                         class="d-block w-100 img-responsive" alt="Advertisement">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var height = $("#detail").height();
            var treheight = $(".trendingnews").outerHeight();
            var totaltrending = Math.floor(height / treheight);
            $(".trendingnews:gt(" + (totaltrending - 1) + ")").remove();

        });
    </script>

@endsection

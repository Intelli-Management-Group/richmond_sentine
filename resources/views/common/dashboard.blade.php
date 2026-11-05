@extends('common.header')
@extends('common.nav')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="well well-asset-options clearfix" style="padding-bottom:2px;padding-top:2px;background:#fff;">
            <div class="dashboard">
                <div>
                    <div class="col-lg-4" style="cursor: default;">
                        <a class="dashboard-stat dashboard-stat-v2 blue" href="{!! url('article-list') !!} ">
                            <div class="visual"> <i class="fa fa-book" aria-hidden="true"></i> </div>
                            <div class="details">
                                <div class="desc">
                                    <h2>Article</h2>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4" style="cursor: default;">
                        <a class="dashboard-stat dashboard-stat-v2 red" href="{!! url('advertisement-list') !!} ">
                            <div class="visual"> <i class="fa fa-file-text" aria-hidden="true"></i> </div>
                            <div class="details">
                                <div class="desc">
                                    <h2>Advertisement</h2>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4" style="cursor: default;">
                        <a class="dashboard-stat dashboard-stat-v2 purple" href="{!! url('article/Unpublish_list') !!} ">
                            <div class="visual"> <i class="fa fa-tags" aria-hidden="true"></i> </div>
                            <div class="details">
                                <div class="desc">
                                    <h2>Coupon</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-12" style="height:1px; background-color:#999; margin-bottom:25px; margin-top:0px"></div>
                <div>
                    <div class="col-lg-6" style="cursor: default;">
                        <div class="col-lg-12 remove_space">
                            <div class="col-lg-4 art_first art_green_dark"><i class="fa fa-file-o" aria-hidden="true"></i></div>
                            <div class="col-lg-8 remove_space">
                                <div class="heading-inside art_green">
                                    <h2 onclick="location.href='{!! url("add-article/0") !!}'">Create Article</h2>
                                </div>
                            </div>
                        </div>
                        <div class="spacer"></div>
                        <div class="col-lg-12 remove_space">
                            <div class="col-lg-4 art_first art_red_dark"><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
                            <div class="col-lg-8 remove_space">
                                <div class="heading-inside art_red">
                                    <h2 onclick="location.href='{!! url("add-advertisement") !!}'">Create Advertisement</h2>
                                </div>
                            </div>
                        </div>
                        <div class="spacer"></div>
                        <div class="col-lg-12 remove_space">
                            <div class="col-lg-4 art_first art_blue_dark"><i class="fa fa-picture-o" aria-hidden="true"></i></div>
                            <div class="col-lg-8 remove_space">
                                <div class="heading-inside art_blue">
                                    <h2 onclick="location.href='{!! url("category-list") !!}'">Create Categories</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h2 class="feed_head">Latest news articles</h2>
                        @foreach(count($latest_news) > 0 ? $latest_news : array() as $l)
                        <div class="alt_row">
                            <div class="col-lg-1 ico_alert yello">
                                <i class="fa fa-book" aria-hidden="true"></i>
                            </div>
                            <div class="col-lg-11 art_det">
                                <div class="article_heading">{!! $l->heading !!}</div>
                                <div class="article_publish_date">Published at : {!! $l->publishDate !!}</div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@extends('common.footer')
@endsection
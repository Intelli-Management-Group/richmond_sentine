@extends('frontend.layout')


@section('content')
<div class="container">
    <!--heading-->
    <div class="row mx-0">
        <h1 class="page-title"><span>Search Result</span></h1>
    </div>

    <div class="row">
        <div class="col-lg-9 col-md-12">
            @if(!isset($msg))
            @if(count($article_list)>0)
            <p>Displaying results {{$article_list->firstItem()}}-{{$article_list->lastItem()}} out of
                {{$article_list->total()}} for <b>{{$searchvalue}}</b></p>
            @else
            @endif

            @forelse($article_list as $article_l)
            <div class="row">
                <div class="col-md-4 ">
                    <?php
                    $file = $article_l->ArticleFileDetail->where("isMain", 1)->first();
                    //    dd($file);
                    ?>

                    @if($article_l->isVideo==1)
                        @if($article_l->isYoutubeVideo==0)
                        <?php $slug = str_slug($article_l->heading, "-"); ?>
                        <a href="{{ URL('videos',$article_l->id) .'/' .$slug }}">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/video_thumbnail/'.$article_l->videoThumbnail }}">
                            <a href="{{ URL('videos',$article_l->id) .'/' .$slug }}" class="video-search"><img src='../images/frontend/generic/icon_video_play.png' class="video-search-play-button"></a>
                        </a>
                        @elseif($article_l->isYoutubeVideo==1)
                        <?php $slug = str_slug($article_l->heading, "-"); ?>
                        <a href="{{ URL('videos',$article_l->id) .'/' .$slug }}">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/video_thumbnail/'.$article_l->videoThumbnail }}">
                            <a href="{{ URL('videos',$article_l->id) .'/' .$slug }}" class="video-search"><img src='../images/frontend/generic/icon_youtube1.png' class="video-search-play-button"></a>
                        </a>
                        @endif

                    @else
                    <a href="{{ route('article-detail',["$article_l->id",str_slug("$article_l->heading", "-")]) }}">
                        @isset($file)
                            @if($file->thumbnailImage=="")
                            <img src='{{ "../uploads/article/$file->fileName" }}' class="d-block w-100 img-responsive" alt="{{ $article_l->heading }}">
                            @elseif($file->thumbnailImage!="")
                            <img src='{{ "../uploads/video_thumbnail/$file->thumbnailImage" }}' class="d-block w-100 img-responsive" alt="{{ $article_l->heading }}">
                                <a href="{{ route('article-detail',["$article_l->id",str_slug("$article_l->heading", "-")]) }}" class="video-search"><img src='{{ "../images/frontend/generic/icon_video_play.png" }}' class="video-search-play-button"></a>
                            @endif
                        @endisset
                    </a>
                    @endif
                </div>
                <div class="col-md-8 ">
                     @if($article_l->isVideo==1)
                     <?php $slug = str_slug($article_l->heading, "-"); ?>
                    <a href="{{ URL('videos',$article_l->id) .'/' .$slug }}" class="video-search">
                        <h5><b>{!! preg_replace('/style[^>]*/', '', htmlspecialchars_decode($article_l->heading)) !!}</b></h5>
                    </a>
                    @else
                    <a href="{{ route('article-detail',["$article_l->id",str_slug("$article_l->heading", "-")]) }}" class="video-search">
                        <h5><b>{!! preg_replace('/style[^>]*/', '', htmlspecialchars_decode($article_l->heading)) !!} </b></h5>
                    </a>
                    @endif
                    <div class="d-inline-block">
                        <div class="d-inline-block">
                            <img src="{{ getAuthorImage($article_l->authorDetail->profileImage) }}">
                        </div>
                        <div class="d-inline-block align-middle ml-3 color-gray">
                            <p class="mb-1">By <a href="" class="color-lightblue">{!!$article_l->authorDetail->name
                                    !!}</a></p>
                            <p class="mb-1">Published
                                @if (Carbon\Carbon::parse($article_l->created_at)->timestamp != Carbon\Carbon::parse($article_l->updated_at)->timestamp)
                                {!! Carbon\Carbon::parse($article_l->updated_at)->format('g:i T, D F j, Y') !!}
                                @else
                                {!! Carbon\Carbon::parse($article_l->publishDate)->format('g:i T, D F j, Y') !!}
                                @endif
                            </p>
                        </div>
                    </div>
                    <p>{!! preg_replace('/style[^>]*/', '', htmlspecialchars_decode($article_l->summary)) !!}</p>
                </div>
            </div>
            <hr>

            @empty
            <p class="mt-5 h1 text-center">No Data Found</p>
            @endforelse
            @if($article_list!="")
            <div class="row">
                <div class="float-left col-md-2 previous_link_container">
                    @if($article_list->onFirstPage())
                    <p><img src="../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                    @else
                    <a href="{{$article_list->appends(['searchvalue'=>$searchvalue])->previousPageUrl()}}">
                        <p><img src="../images/frontend/generic/btn_arrow_l_0.png" class="mr-2">Previous</p>
                    </a>
                    @endif

                </div>

                <div class="float-left col-md-8 text-center">

                    {{ $article_list->appends(['searchvalue'=>$searchvalue])->links('frontend.paginationlink') }}
                </div>

                <div class="float-right col-md-2 text-right next_link_container">
                    @if($article_list->hasMorePages())
                    <a href="{{$article_list->appends(['searchvalue'=>$searchvalue])->nextPageUrl()}}">
                        <p>Next<img src="{{ "../images/frontend/generic/btn_arrow_r_0.png" }}" class="ml-2"></p>
                    </a>
                    @else
                    <p>Next<img src="{{ "../images/frontend/generic/btn_arrow_r_0.png" }}" class="ml-2"></p>
                    @endif

                </div>
                <div class="clearfix"></div>
            </div>
            @else
            @endif

            @endif
            @isset($msg)
            <p class="mt-5 h1 text-center">{!!$msg!!}</p>
            @endisset



        </div>
        <div class="col-lg-3 col-md-12">

            <div class="carousel slide carousel-fade sidebar mb-5" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $ads->link!!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>

                    @endforeach

                </div>
            </div>
                <!--Ad Sidebar For Responsive -->
            <div class="carousel slide carousel-fade sidebarresponsive mb-5" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ad_sidebar_responsive as $key=>$adsr)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $adsr->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="carousel slide carousel-fade sidebar mb-5" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==1 ?" active ":" "!!}">
                        <a href="{!! $ads->link!!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>

                    @endforeach

                </div>
            </div>
                <!--Ad Sidebar For Responsive -->
            <div class="carousel slide carousel-fade sidebarresponsive mb-5" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ad_sidebar_responsive as $key=>$adsr)
                    <div class="carousel-item {!!$key==1 ?" active ":" "!!}">
                        <a href="{!! $adsr->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>

            <div class="carousel slide carousel-fade sidebar mb-5" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==2 ?" active ":" "!!}">
                        <a href="{!! $ads->link!!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>

                    @endforeach

                </div>
            </div>
                <!--Ad Sidebar For Responsive -->
            <div class="carousel slide carousel-fade sidebarresponsive mb-5" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach($ad_sidebar_responsive as $key=>$adsr)
                    <div class="carousel-item {!!$key==2 ?" active ":" "!!}">
                        <a href="{!! $adsr->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>

        </div>

    </div>
</div>

@endsection

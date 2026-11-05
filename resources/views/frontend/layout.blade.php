<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @isset($article)
        <meta name="description"
              content="{{ (($article->summary==null)? $article->heading :(str_limit(strip_tags($article->summary),100,'...'))) }}"/>
        <meta name="keywords" content="{!! (!empty($article->metaTag)) ? $article->metaTag : '' !!}"/>
    @endisset
    @if(isset($article))
        @php
            $a_title = $article->heading;
            $a_description = (($article->summary == null) ? $article->heading : (str_limit(strip_tags($article->summary),100,'...')));
            $a_image = (($article->thumbnailImage == '') ? (url('uploads/article').'/'. $article->ArticleFileDetail->where("isMain", 1)->first()->fileName) : (url('uploads/video_thumbnail').'/'. $article->thumbnailImage));
            $a_route = route('article-detail',["$article->id",str_slug("$article->heading", "-")]);
        @endphp
        <meta property="og:type" content="article"/>
        <meta property="og:title" content="{!! $a_title !!}"/>
        <meta property="og:description" content="{!! htmlspecialchars($a_description) !!}"/>
        <meta property="og:image" content="{!! $a_image.'?q='.\Carbon\Carbon::now()->timestamp !!}"/>
        <meta property="og:url" content="{!! $a_route  !!}"/>
        <meta property="og:site_name" content="Richmond Sentinel"/>

        <meta name="twitter:title" content="{!! $a_title !!}">
        <meta name="twitter:description" content="{!! htmlspecialchars($a_description) !!}">
        <meta name="twitter:image" content="{!! $a_image !!}">
        <meta name="twitter:site" content="@RmdSentinel">
        <meta name="twitter:creator" content="@RmdSentinel">
        <title>RichmondSentinel | {!! $article->heading !!}</title>
    @else
        <meta name="description"
              content="The Richmond Sentinel provides relevant news and information to the community of Richmond, B.C., Canada. We seek to connect different groups within our municipality through features and human interest stories."/>
        <meta name="keywords"
              content="Richmond Sentinel News, Richmond News, Richmond Local News, Richmond Community News, Richmond Journal, Richmond Public News"/>
        <title>{{ config('app.name', 'Richmond Sentinel') }}</title>
@endif

<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-10237768-18"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-10237768-18');
    </script>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js').'?t='.Carbon\Carbon::now()->timestamp }}" defer></script>
    <script src="{{ asset('js/jquery.min.js') }}" defer></script>
    <script src="{{ asset('js/owl.carousel.js') }}" defer></script>
    <script src="{{ asset('js/jquery-modal-video.min.js') }}" defer></script>

    <link rel="canonical" href="{{ url('/') }}"/>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="icon" href="{{ asset('ui/img/senti.png') }}" type="image/ico" sizes="16x16">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/home.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/couponzone.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/articlelist.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/coupondetail.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/videolist.css').'?t='.Carbon\Carbon::now()->timestamp }}" rel="stylesheet">
    <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.theme.default.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/css/font-awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('ui/css/error.css').'?t='.Carbon\Carbon::now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('css/modal-video.css') }}">
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '244429846723835');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=244429846723835&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
</head>

<body>
<div id="app">
    <div class="wrapper">

        <nav class="navbar navbar-expand-lg bg-blue">
            <div class="container position-relative">
                <a class="navbar-brand" href="{!!url('/')!!}"><img
                        src="{{asset('images/frontend/home/logo_richmondsentinel.png')}}" alt="Richmond Sentinel"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarNavAltMarkup, #navbarNavAltMarkup1" aria-controls="navbarNavAltMarkup"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <?php
                $category_data_array = get_category_data();
                ?>

                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <ul class="navbar-nav font-weight-light">
                        <li class="nav-item">
                            <a class="nav-link active" href="{!!url('/')!!}">Home <span class="sr-only">(current)</span></a>
                        </li>

                        @foreach($category_data_array as $key=>$category_data)

                            <li class="nav-item {!!(count($category_data['child_list'])> 0) ? 'dropdown':'' !!}">
                                <a class="nav-link navbardrop"
                                   href="{!! $category_data['name']=='Couponzone' ? url('/couponzone') : ($category_data['name']=='Videos' ? url('/videos') : ($category_data['name']=='Editions' ? url('/editions'): ($category_data['name']=='Place An Ad' ? url('/place-an-ad') : ($category_data['name']=='Election' ? url('/election') : (url('/articles').'/'.$key.'/'.str_slug(" $category_data[name]", "-" )))))) !!}"
                                   @if(count($category_data['child_list'])> 0)data-toggle="dropdown"@endif>
                                    {!!$category_data["name"] !!}
                                </a>

                                @if(count($category_data["child_list"]) > 0)

                                    <ul class="dropdown-menu bg-lightblue">
                                        @foreach($category_data["child_list"] as $ckey =>$child_category)

                                            <li>
                                                <a class="nav-link"
                                                   href="{!!url('/articles')."/".$ckey."/".str_slug("$child_category[name]", "-" )!!} "
                                                   @if(count($child_category['child_list'])> 0)data-toggle=" dropdown"@endif>{!!$child_category["name"]
                                                    !!}</a></li>

                                        @endforeach
                                    </ul>

                                @endif
                            </li>
                        @endforeach
                        <!--<li class="nav-item">-->
                        <!--    <a class="nav-link active" href="{!!url('/leaderboard')!!}">Leaderboard</a>-->
                        <!--</li>-->
                       {{-- <li class="nav-item">
                            <a class="nav-link active" href="{!!url('/stores')!!}">Stores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{!!url('/events')!!}">Events</a>
                        </li>--}}
                    </ul>
                </div>
            </div>

        </nav>

        <nav class="navbar navbar-expand-lg bg-darkblue">
            <div class="container position-relative">
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup1" style="width:73%">
                        <span class="navbar-text color-lightblue">
                            Get all your news updates in one place!
                        </span>
                    <ul class="socialmedia-icons">
                        <li class="socialmedia-list">
                            <a class="" href="https://www.facebook.com/TheRichmondSentinel/" target="_blank">
                                <img src="{{asset('images/frontend/generic/icon_fb.png')}}" alt="Facebook">
                            </a>
                        </li>
                        <li class="socialmedia-list">
                            <a class="" href="https://twitter.com/rmdsentinel?lang=en" target="_blank">
                                <img src="{{asset('images/frontend/generic/icon_twitter.png')}}" alt="Twitter">
                            </a>
                        </li>
                        <li class="socialmedia-list">
                            <a class="" href="https://instagram.com/therichmondsentinel?igshid=5o2twmxv6hvt"
                               target="_blank">
                                <img src="{{asset('images/frontend/generic/icon_instagram.png')}}" alt="Instagram">
                            </a>
                        </li>
                        <li class="socialmedia-list">
                            <a class="" href="https://www.youtube.com/channel/UC58QGGxMGEYHoJgtMu-2ALQ/"
                               target="_blank">
                                <img src="{{asset('images/frontend/generic/icon_youtube.png')}}" alt="Youtube">
                            </a>
                        </li>
                    </ul>
                    <form class="form-inline ml-auto mr-3" action="{!!url('/list')!!}">
                        <input class="search-bar form-control mr-sm-2 py-0" type="text" placeholder="Enter Keyword"
                               name="searchvalue" required>
                        <button class="search-button" type="submit"><img
                                src="{{asset('images/frontend/generic/btn_search.png')}}" alt="Search"></button>
                    </form>
                </div>
            </div>

        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <div class="container-fluid bg-darkblue mt-7">
        <div class="container">
            <!-- Footer -->
            <div class="row font-weight-light">
                <div class="text-white col-lg-8">
                        <span class="footer-text d-block">© {!! Carbon\Carbon::now()->format('Y') !!} Richmond Sentinel News Inc. All rights reserved. Designed by
                            <a href="http://intellimanagement.com"
                               target="_blank">Intelli Management Group Inc.</a></span>
                </div>
                <div class="col-lg-4">
                    <nav class="footer-nav">
                        <ul class="d-block">
                            <li class="">
                                <a class="" href="{{ route('about-us') }}">About Us</a>
                            </li>
                            <li class="">
                                <a class="" href="{{ route('terms-of-use') }}">Terms of Use</a>
                            </li>
                            <li class="">
                                <a class="" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Footer -->
        </div>

    </div>
</div>
</body>

</html>

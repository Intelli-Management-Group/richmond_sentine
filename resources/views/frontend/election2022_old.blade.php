@extends('frontend.layout')
@section('content')
    <div class="container">
        <!--heading-->
        <div class="row mx-0 mt-4">
            <h1 class="page-title"><span>2022 Richmond City Election</span></h1>
        </div>
        {{-- top --}}
        <div class="row mt-5">
            <!--slider-->
            <div class="col-lg-12">
                <div id="video">
                        <iframe width="100%" height="464" src="https://www.youtube.com/embed/9-A4mvHPugM?autoplay=1" frameborder="0" allowfullscreen></iframe>
{{--                    <iframe width="100%" height="464" src="https://www.youtube.com/embed/9-A4mvHPugM" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>--}}
                    <div id="heading">
{{--                        <p class="color-blue font-weight-light my-4">{!! $video->heading !!}</p>--}}

                        <p class="color-blue font-weight-light my-4">Stay Tune: Richmond Canadidates' Interviews - 2022 Richmond Election</p>
                    </div>
                </div>


                <!--Ad Fadeslider -->
                <div id="detail">
                    <h1 class="font-weight-light my-4" style="font-size: 2rem">Richmond Sentinel News coverage of Richmond's 2022 election.<br/>
                        Get to know the candidates and make an educated decision.<br/>
                        Vote on October 15th.</h1>

                </div>

            </div>

        </div>

    </div>
@endsection

@extends('frontend.layout')
@section('content')
    <div class="container">
        <!--heading-->
        <div class="row mx-0 mt-4">
            <h1 class="page-title election-h1"><span>Election</span></h1>
        </div>


        <div class="row mt-5">
            <div class="col-lg-12 mt-5">
                <nav id="election_main_tabs">
                    <div class="nav nav-tabs" role="tablist">
                        {{--                        <a class="nav-item nav-link{{ $type == "federal" ? ' active' : ''}}" data-toggle="tab"--}}
                        {{--                           href="#nav-federal" role="tab"--}}
                        {{--                           aria-controls="nav-mayor" aria-selected="true"><h4>Federal</h4></a>--}}
                        <a class="nav-item nav-link{{ $type == "provincial" ? ' active' : ''}}" data-toggle="tab"
                           href="#nav-provincial" role="tab"
                           aria-controls="nav-councillor" aria-selected="false"><h4>Provincial</h4></a>
                        <a class="nav-item nav-link{{ $type == "city" ? ' active' : ''}}" data-toggle="tab"
                           href="#nav-city" role="tab"
                           aria-controls="nav-contact" aria-selected="false"><h4>City</h4></a>
                    </div>
                </nav>
                <div class="tab-content" style="background: white">
                    {{--                    <div class="tab-pane fade {{ $type == "federal" ? ' show active' : ''}} mt-5 election_sub_tabs"--}}
                    {{--                         id="nav-federal" role="tabpanel">--}}
                    {{--                        <div class="text-center mt-5 pt-5"><h1>No video(s) available.</h1></div>--}}
                    {{--                    </div>--}}
                    <div class="tab-pane fade mt-5 election_sub_tabs{{ $type == "provincial" ? ' show active' : ''}}"
                         id="nav-provincial" role="tabpanel">
                        <nav>
                            <div class="nav nav-tabs" role="tablist">
                                <a class="nav-item nav-link active" data-toggle="tab" href="#provincial-nav-2020"
                                   role="tab"
                                   aria-controls="nav-mayor" aria-selected="true"><h4>2020</h4></a>
                                <div style="background: #ebf0f9;padding: 30px 50px;">&nbsp;</div>

                            </div>
                        </nav>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="provincial-nav-2020" role="tabpanel">
                                <div class="pt-5 pb-5">
                                    <div class="col-lg-11 pt-2">
                                        @foreach($provincial_tab as $key => $value)
                                            @include('frontend.electionVideo',['key' => $key,'value' => $value])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="tab-pane fade mt-5  election_sub_tabs{{ $type == "city" ? ' show active' : ''}}"
                         id="nav-city" role="tabpanel">
                        <nav>
                            <div class="nav nav-tabs" role="tablist">
                                <a class="nav-item nav-link active" data-toggle="tab" href="#city-nav-2022" role="tab"
                                   aria-controls="nav-mayor" aria-selected="true"><h4>2022</h4></a>
                                <a class="nav-item nav-link" data-toggle="tab" href="#city-nav-2021" role="tab"
                                   aria-controls="nav-councillor" aria-selected="false"><h4>2021</h4></a>
                                <a class="nav-item nav-link" data-toggle="tab" href="#city-nav-2018" role="tab"
                                   aria-controls="nav-councillor" aria-selected="false"><h4>2018</h4></a>
                            </div>
                        </nav>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="city-nav-2022" role="tabpanel">
                                @include('frontend.electionContent',['tab' => 'city2022','mayor_tab' => $city_2022_mayor_tab,'councillor_tab' => $city_2022_councillor_tab,'trustee_tab' =>$city_2022_trustee_tab])
                            </div>
                            <div class="tab-pane fade" id="city-nav-2021" role="tabpanel">
                                <div class="pt-5 pb-5">
                                    <div class="col-lg-11 pt-2">
                                        @foreach($city_2021_tab as $key => $value)
                                            @include('frontend.electionVideo',['key' => $key,'value' => $value])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="city-nav-2018" role="tabpanel">
                                @include('frontend.electionContent',['tab' => 'city2018','mayor_tab' => $city_2018_mayor_tab,'councillor_tab' => $city_2018_councillor_tab,'trustee_tab' =>$city_2018_trustee_tab])
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function () {
            $(".election_video_link").modalVideo({
                'youtube': {
                    autoplay: 0
                }
            });
        });
    </script>
@endsection

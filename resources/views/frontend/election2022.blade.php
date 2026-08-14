@extends('frontend.layout')
@section('content')
    <div class="container">
        <!--heading-->
        <div class="row mx-0 mt-4">
            <h1 class="page-title election-h1"><span>2022 Richmond City Election</span></h1>
        </div>
        {{-- top --}}
        <div class="row mt-5">
            <!--slider-->
            <div class="col-lg-12">
                <div id="video">
                    <iframe id="main_election_video" width="100%" height="464"
                            src="https://www.youtube.com/embed/SUwa3fTVKVc?autoplay=1"
                            frameborder="0" allowfullscreen allow="autoplay"></iframe>
                    <div id="heading">
                        <p class="color-blue font-weight-light my-4">Stay Tune: Richmond Canadidates' Interviews - 2022
                            Richmond Election</p>
                    </div>
                </div>


                <!--Ad Fadeslider -->
                <div id="detail">
                    <h1 class="font-weight-light my-4" style="font-size: 2rem">Richmond Sentinel News coverage of
                        Richmond's 2022 election.<br/>
                        Get to know the candidates and make an educated decision.<br/>
                        Vote on October 15th.</h1>

                </div>

            </div>

        </div>
        <div class="row mt-5">
            <div class="col-lg-12 mt-5">
                <nav>
                    <div id="election_tabs" class="nav nav-tabs" role="tablist">
                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-mayor" role="tab"
                           aria-controls="nav-mayor" aria-selected="true"><h4>Mayor</h4></a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-councillor" role="tab"
                           aria-controls="nav-councillor" aria-selected="false"><h4>Councillor</h4></a>
                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-trustee" role="tab"
                           aria-controls="nav-contact" aria-selected="false"><h4>Trustee</h4></a>
                    </div>
                </nav>
                <div class="tab-content election_tab_content">
                    <div class="tab-pane fade show active mt-5" id="nav-mayor" role="tabpanel">
                        <div class="row">
                            @foreach($mayor_tab as $key => $value)
                                @include('frontend.electionVideo',['key' => $key,'value' => $value])
                            @endforeach

                                <div class="col-md-4">
                                    <div class="w-100" style="cursor: pointer;">
                                        <div class="video-wrapper">
                                            <img src="../../images/frontend/elections/Wei-Ping-Chen.jpg" class="w-100" alt="2022 City of Richmond Election for Mayor - Wei Ping Chen">
                                        </div>
                                        <div class="thumbnail-height video-headinng" style="height: auto;margin-bottom: 40px;">
                                            <p class="mt-2 mb-1 font-weight-light text-left">2022 City of Richmond Election for Mayor - Wei Ping Chen - not available at time of production</p>
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </div>
                    <div class="tab-pane fade mt-5" id="nav-councillor" role="tabpanel">
                        <div class="row">
                            @foreach($councillor_tab as $key => $value)
                                @include('frontend.electionVideo',['key' => $key,'value' => $value])
                            @endforeach
                                <div class="col-md-4">
                                    <div class="w-100" style="cursor: pointer;">
                                        <div class="video-wrapper">
                                            <img src="../../images/frontend/elections/Sunny-Ho.jpg" class="w-100" alt="2022 City of Richmond Election for Councillor - Sunny Ho">
                                        </div>
                                        <div class="thumbnail-height video-headinng" style="height: auto;margin-bottom: 40px;">
                                            <p class="mt-2 mb-1 font-weight-light text-left">2022 City of Richmond Election for Councillor - Sunny Ho - not available at time of production</p>
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </div>
                    <div class="tab-pane fade mt-5" id="nav-trustee" role="tabpanel">
                        <div class="row">
                            @foreach($trustee_tab as $key => $value)
                                @include('frontend.electionVideo',['key' => $key,'value' => $value])
                            @endforeach

                                <div class="col-md-4">
                                    <div class="w-100" style="cursor: pointer;">
                                        <div class="video-wrapper">
                                            <img src="../../images/frontend/elections/Chris-Dinnell.jpg" class="w-100" alt="2022 City of Richmond Election for School Trustee - Chris Dinnell">
                                        </div>
                                        <div class="thumbnail-height video-headinng" style="height: auto;margin-bottom: 40px;">
                                            <p class="mt-2 mb-1 font-weight-light text-left">2022 City of Richmond Election for School Trustee - Chris Dinnell - not available at time of production</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="w-100" style="cursor: pointer;">
                                        <div class="video-wrapper">
                                            <img src="../../images/frontend/elections/Andrew-Scallion.jpg" class="w-100" alt="2022 City of Richmond Election for School Trustee - Andrew Scallionn">
                                        </div>
                                        <div class="thumbnail-height video-headinng" style="height: auto;margin-bottom: 40px;">
                                            <p class="mt-2 mb-1 font-weight-light text-left">2022 City of Richmond Election for School Trustee - Andrew Scallion - not available at time of production</p>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).on("click", ".election_video", function (e) {
            console.log("data :", $(this).data("video"));
            $("#main_election_video").attr("src", "https://www.youtube.com/embed/" + $(this).data("video") + "?autoplay=1");
            $('html, body').animate({
                scrollTop: $("#main_election_video").offset().top
            }, 700);
        });
    </script>

@endsection

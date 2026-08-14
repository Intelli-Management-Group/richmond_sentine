@extends('frontend.layout')
@section('content')
<div class="container edition_list">
    <div class="row MB10">
        <h1 class="page-title"><span>Editions</span></h1>
    </div>
    <div class="row mt-5">
        <div class="col-lg-9">
            @forelse(count($editions) > 0 ? $editions : array() as $key => $value)
            <div id="edition_{!! $key !!}" class="MB10">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                            <a class="edition_link color-blue font-weight-bold" data-toggle="collapse" data-target="#edition_detail_{!! $key !!}" aria-expanded="true" aria-controls="collapseOne">
                                {!! $key !!}
                            </a>
                        </h5>
                    </div>
                    <div id="edition_detail_{!! $key !!}" class="collapse" aria-labelledby="headingOne" data-parent="#edition_{!! $key !!}">
                        <div class="card-body">
                            @forelse($value as $v)
                            <div class="col-sm-3 MB10 float-left">
                                <a class="edition_link" href="{!! $v['pdfFile'] !!}" target="_blank">
                                    <div class="edition_detail">
                                        <div class="edition_name text-center font-weight-bold" title="{!! $v['name'] !!}">{!! $v['name'] !!}</div>
                                        <div class="throbber text-center">
                                            <img data-src="{!! $v['thumbnailImage'] !!}" class="edition_thumbnail" />
                                            <div class="throbber_after"></div>
                                        </div>
                                        <div class="text-center font-weight-bold">{!! "Vol-".$v['volumeEdition']." No-".$v['editionNumber'] !!}</div>
                                    </div>
                                </a>
                            </div>
                            @empty
                            <div class="text-center">No editions found</div>
                            @endforelse
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center">No editions found</div>
            @endforelse
        </div>
        <div class="col-lg-3">
            <div class="carousel slide carousel-fade sidebar" data-ride="carousel">
                <div class="carousel-inner ">
                    @foreach($ad_sidebar as $key=>$ads)
                    <div class="carousel-item {!!$key==0 ?" active ":" "!!}">
                        <a href="{!! $ads->link !!}" target="_blank">
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$ads->imageName }}" alt="Richmond Sentinel Advertisement">
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
                            <img class="d-block w-100 img-responsive" src="{{ '../uploads/advertisement/'.$adsr->imageName }}" alt="Richmond Sentinel Advertisement">
                        </a>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('css/edition.css').'?t='.Carbon\Carbon::now()->timestamp }}">
<script type="text/javascript">
    $('.edition_list .collapse').on('show.bs.collapse', function() {
        $(this).find(".throbber").find("img").each(function() {
            if ($(this).next(".throbber_after").length > 0) {
                $(this).attr("src", $(this).data("src"));
                $(this).on('load', function() {
                    $(this).next(".throbber_after").remove();
                    $(this).removeAttr("data-src");
                    $(this).off('load');
                });

            }
        });
    });
    $('.edition_link:first').click();
</script>
@endsection

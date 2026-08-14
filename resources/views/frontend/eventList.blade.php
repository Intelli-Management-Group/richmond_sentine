@extends('frontend.layout')
@section('content')
    <div class="container">
        <div class="row mt-3">
            <div class="col-md-6 mb-4 pt-5">
                <div id="event_calendar" data-action="{!! url('events-by-date') !!}">
                    <header>
                        <div class="title-content">
                            <button id="last"><img src="{!! asset("images/frontend/generic/btn_arrow_l_0.png") !!}">
                            </button>
                            <div class="month-name">
                                <h1 id="month"></h1>
                                <h2 id="year"></h2>
                            </div>
                            <button id="next"><img src="{!! asset("images/frontend/generic/btn_arrow_r_0.png") !!}">
                            </button>
                        </div>
                    </header>
                </div>
                <div class="ci-cal-container" id="calendar"></div>
            </div>
            <div class="col-md-6 mb-2">
                <h5 class="text-center color-666 text-uppercase">upcoming events</h5>
                <div id="upcoming_events">
                </div>
            </div>
        </div>
    </div>
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" defer></script>
    <script src="{{ asset('js/calendar.js') }}" defer></script>
@endsection

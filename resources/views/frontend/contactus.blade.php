@extends('frontend.layout')

@section('content')

    <div class="container">
        <div class="row">
            <h1 class="page-title"><span>Contact Us</span></h1>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <h3> We welcome your questions and feedback so that we can serve you better</h3>
            </div>

            <div class="col-md-4">
                <div class="w-100 d-block my-4">
                    <div class="d-inline-block align-top">
                        <img src="{{ asset('images/frontend/generic/icon_email.png') }}">
                    </div>
                    <div class="d-inline-block pl-2 align-middle">
                        <h6>Email</h6>
                        <p class="m-0">{{ $contact->email_id }}</p>
                    </div>
                </div>
                <div class="w-100 d-block my-4">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_tel.png') }}">
                    </div>
                    <div class="d-inline-block pl-2 align-middle">
                        <h6>Phone</h6>
                        <p class="m-0">{{ $contact->phone }}</p>
                    </div>
                </div>
                <div class="w-100 d-block my-4">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_hours.png') }}">
                    </div>
                    <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                        <h6>Business hours</h6>
                        <p class="m-0">{{ $contact->office_days }} - {{ $contact->office_hours }}</p>
                    </div>
                </div>
                <div class="w-100 d-block my-4">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_mail.png') }}">
                    </div>
                    <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                        <h6>Address</h6>
                        <p class="m-0">{{ $contact->mailing_address }}</p>
                    </div>
                </div>
                <div class="d-block">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_advertising.png') }}">
                    </div>
                    <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                        <h6>Advertise with us?</h6>
                    </div>
                </div>

            </div>
            <div class="col-md-8 text-center">
                <div class="d-inline-block mb-4 float-left">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_distribution_g.png') }}" width="30px">
                    </div>
                    <div class="d-inline-block pl-2">
                        <p>Newspaper box</p>
                    </div>
                </div>
                <div class="d-inline-block ml-md-3 mx-auto">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_distribution_r.png') }}" width="30px">
                    </div>
                    <div class="d-inline-block pl-2">
                        <p>High-traffic location</p>
                    </div>
                </div>
                <div class="d-inline-block ml-md-3 float-md-right">
                    <div class="d-inline-block ">
                        <img src="{{ asset('images/frontend/generic/icon_distribution_b.png') }}" width="30px">
                    </div>
                    <div class="d-inline-block pl-2">
                        <p>Wire racks</p>
                    </div>
                </div>

                <iframe src="https://www.google.com/maps/d/embed?mid=1D2Xlmy-e_rmvtnVQEgjdwaceA_-tUGP0" width="100%" height="400"></iframe>
            </div>
        </div>
    </div>
@endsection

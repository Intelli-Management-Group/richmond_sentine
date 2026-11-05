@extends('frontend.layout')

@section('content')
<div class="container">
    <div class="row mx-0">
        <h1 class="page-title"><span>About Us</span></h1>
    </div>
    <div class="row mt-4">
        <div class="col-lg-8">
            <img src="images/frontend/generic/banner_placead.jpg" class="img-responsive w-100 mb-5">
            @php
            $data = json_decode($aboutus->content,true);
            echo $data['description'];
            @endphp
        </div>
        <div class="col-lg-4">
            <div class="w-100 d-block mb-4">
                <div class="d-inline-block align-top ">
                    <img src="images/frontend/generic/icon_tel.png">
                </div>
                <div class="d-inline-block pl-2 align-middle">
                    <p class="mb-1">Call</p>
                    @php
                        $contact_no = explode(",",$contact->place_an_ad_phone);
                        echo '<h3 class="m-0 color-blue">'.$contact_no[0].'</h3><p class="m-0">Editorial</p>';
                    @endphp
                </div>
            </div>
            <div class="w-100 d-block my-4">
                <div class="d-inline-block align-top">
                    <img src="images/frontend/generic/icon_email.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle">
                    <p class="mb-1">Email</p>
                    <p class="m-0">{{ $contact->about_us_email }}</p>
                </div>
            </div>
            <div class="w-100 d-block my-4">
                <div class="d-inline align-top">
                    <img src="images/frontend/generic/icon_mail.png" class="align-top">
                </div>
                <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                    <p class="mb-1">Mailing Address</p>
                    <p class="m-0">{{ $contact->mailing_address }}</p>
                </div>
            </div>
            <div class="w-100 d-block my-4">
                <div class="d-inline-block ">
                    <img src="images/frontend/generic/icon_hours.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                    <p class="mb-1">Office Hours</p>
                    <p class="m-0">{{ $contact->office_days }} - {{ $contact->office_hours }}</p>
                </div>
            </div>

            <div class="d-block">
                <div class="d-inline-block ">
                    <img src="images/frontend/generic/icon_advertising.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                    <h6 class="color-blue">Advertise with us?</h6>
                </div>
            </div>
            <div class="center distribution-icon mt-4">
                <div class="float-left">
                    <div class="d-inline-block ">
                        <img src="images/frontend/generic/icon_distribution_g.png" width="20px">
                    </div>
                    <div class="d-inline-block">
                        <p>Newspaper box</p>
                    </div>
                </div>

                <div class="float-right">
                    <div class="d-inline-block ">
                        <img src="images/frontend/generic/icon_distribution_b.png" width="20px">
                    </div>
                    <div class="d-inline-block ">
                        <p>Wire racks</p>
                    </div>
                </div>
                <div class="center">
                    <div class="d-inline-block ">
                        <img src="images/frontend/generic/icon_distribution_r.png" width="20px">
                    </div>
                    <div class="d-inline-block ">
                        <p>High-traffic location</p>
                    </div>
                </div>
            </div>


            <iframe src="https://www.google.com/maps/d/embed?mid=1D2Xlmy-e_rmvtnVQEgjdwaceA_-tUGP0" width="100%" height="300"></iframe>
        </div>
    </div>

</div>


@endsection

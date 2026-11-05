@extends('frontend.layout')

@section('content')

<div class="container">
    <div class="row mx-0">
        <h1 class="page-title"><span>Place an Ad</span></h1>
    </div>
    <div class="row mt-4">
        <div class="col-lg-8">
            <img src="../images/frontend/generic/banner_placead.jpg" class="img-responsive w-100 mb-5">
            <p>
                With a full menu of print and online options, we can design an advertising package that uses the mediums that reach your target audiencex the best, fine tuning the distribution to make the most of your advertising dollar.
            </p>
            <p>
                <b>Print Marketing - Newspapers</b>
                In a tech-savvy world, print advertising still has a significant impact due to reader memorability, tangibility and extended lifespan. Our print newspaper works synergistically with our website, our mobile app and our social media presence.
            </p>
            <p>
                The Richmond Sentinel publications had reached a combined readership of over 31,500! We offer content writing by our professional copywriters and top-grade design professionals to enhance your advertisement. Advertise your business with us now!
            </p>
            <p>
                <b>Richmond Sentinel Digital Marketing Tools</b>
                Digital Marketing is fast, flexible, and trackable. Investing in a varying marketing mix amplifies your business’s success in getting front and center with potential customers. Advertising online allows you to connect with your target market no matter where they are physically. Online advertising has proven its success to thousands of marketers and small business owners! It’s one of the most effective ways for businesses of all sizes to expand their reach, find new customers, and diversify their revenue streams.
            </p>
            <p>
                Planning to promote your business? Act now before your competitors do so. Richmond Sentinel friendly and helpful staff will assist you every step of the way from content creation to ad placement.
            </p>
        </div>
        <div class="col-lg-4">
            <div class="w-100 d-block mb-4">
                <div class="d-inline-block align-top">
                    <img src="../images/frontend/generic/icon_tel.png" class="align-top">
                </div>
                <div class="d-inline-block pl-2 align-middle">
                    <p class="mb-1">Call</p>
                    <h3 class="m-0 color-blue">
                        @php
                            $contact_no = explode(",",$contact->place_an_ad_phone);
                            foreach(count($contact_no) > 0 ? $contact_no : array() as $c){
                                echo $c."<br/>";
                            }
                        @endphp
                    </h3>
                </div>
            </div>
            <div class="w-100 d-block my-4">
                <div class="d-inline-block">
                    <img src="../images/frontend/generic/icon_email.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle">
                    <p class="mb-1">Email</p>
                    <p class="m-0">{{ $contact->email_id }}</p>
                </div>
            </div>
            <div class="w-100 d-block my-4">
                <div class="d-inline-block ">
                    <img src="../images/frontend/generic/icon_hours.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                    <p class="mb-1">Office Hours</p>
                    <p class="m-0">{{ $contact->office_days }} - {{ $contact->office_hours }}</p>
                </div>
            </div>

            <div class="d-block">
                <div class="d-inline-block align-top">
                    <img src="../images/frontend/generic/icon_advertising.png" class="align-baseline">
                </div>
                <div class="d-inline-block pl-2 align-middle" style="width: calc(100% - 60px);">
                    <p>Distribution Location</p>
                </div>
            </div>
            <div class="center distribution-icon mt-4">
                <div class="float-left">
                    <div class="d-inline-block ">
                        <img src="../images/frontend/generic/icon_distribution_g.png" width="20px">
                    </div>
                    <div class="d-inline-block">
                        <p>Newspaper box</p>
                    </div>
                </div>

                <div class="float-right">
                    <div class="d-inline-block ">
                        <img src="../images/frontend/generic/icon_distribution_b.png" width="20px">
                    </div>
                    <div class="d-inline-block ">
                        <p>Wire racks</p>
                    </div>
                </div>
                <div class="center">
                    <div class="d-inline-block ">
                        <img src="../images/frontend/generic/icon_distribution_r.png" width="20px">
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

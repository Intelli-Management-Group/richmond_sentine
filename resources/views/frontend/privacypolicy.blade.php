@extends('frontend.layout')

@section('content')
    <div class="container">
        <div class="row mb-5">
            <h1 class="page-title"><span>Privacy Policy</span></h1>
        </div>
        @php
            $data = json_decode($policy->content,true);
            echo $data['description'];
        @endphp
    </div>


@endsection

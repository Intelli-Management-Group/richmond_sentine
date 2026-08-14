@extends('frontend.layout')

@section('content')
    <div class="container">
        <div class="row mb-5">
            <h1 class="page-title"><span>Terms of Use</span></h1>
        </div>
        @php
            $data = json_decode($tou->content,true);
            echo $data['description'];
        @endphp
    </div>
@endsection
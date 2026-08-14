<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('ui/img/senti.png') }}" type="image/ico" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('ui/css/quirk.css') }}">
    <title>{{ config('app.name') }}</title>
</head>
@yield('content')

</html>

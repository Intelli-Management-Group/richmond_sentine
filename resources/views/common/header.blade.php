<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('ui/img/senti.png') }}" type="image/ico" sizes="16x16">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('ui/css/font-awesome.css') }}">
    <!--WYSIWYG Editor css included-->

    <link rel="stylesheet" href="{{ asset('ui/lib/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/jquery-ui/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('ui/css/quirk.css').'?t='.Carbon\Carbon::now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/summernote/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/Hover/hover.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/weather-icons/css/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/ionicons/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/jquery-toggles/toggles-full.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/lib/morrisjs/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('ui/css/custom.css').'?t='.Carbon\Carbon::now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css').'?t='.Carbon\Carbon::now()->timestamp }}">
    <link rel="stylesheet" href="{{ asset('ui/css/error.css').'?t='.Carbon\Carbon::now()->timestamp }}">
</head>

<body>
    
    @yield('navsection') @yield('footer')
</body>

</html>
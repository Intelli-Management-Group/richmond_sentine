@extends(Auth::user() ? 'common.header' : 'frontend.layout')

@if(Auth::user())
@section('navsection')
@include('common.topbar')
<div class="mainpanel" style="margin-left:0px;height:100%">
  <div class="contentpanel">
    <div class="row">
      <div class="col-md-12">
        <div class="notfoundpanel">
          <h1>404!</h1>
          <h3>Sorry, the page you are looking for could not be found</h3>
          <div class="text-center">
            <a class="btn btn-success btn-quirk btn-wide" href="{!! url('dashboard') !!}" class="m-t">Go back</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@extends('common.footer')
@endsection
@else
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="notfoundpanel">
        <h1>404!</h1>
        <h3>Sorry, the page you are looking for could not be found</h3>
        <div class="text-center">
          <a class="btn btn-primary text-uppercase btn-quirk" href="{!! url('/') !!}" class="m-t">Go back</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@endif
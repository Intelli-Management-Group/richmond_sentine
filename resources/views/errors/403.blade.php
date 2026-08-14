@extends('common.header')
@section('navsection')
@include('common.topbar')
<div class="mainpanel" style="margin-left:0px;height:100%">
  <div class="contentpanel">
    <div class="row">
      <div class="col-md-12">
        <div class="notfoundpanel">
          <h1>403!</h1>
          <h3>You don't have permission to access</h3>
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
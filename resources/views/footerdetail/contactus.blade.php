@extends('common.header')
@extends('common.nav')

@section('content')
    <ol class="breadcrumb breadcrumb-quirk">
        <li><a href="{{ url('dashboard') }}"><i class="fa fa-home mr5"></i> Home</a></li>
        <li class="active">Contact Us</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success" id="successMessage">
                    {!! session('success') !!}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" id="errorMessage">
                    {!! session('error') !!}
                </div>
            @endif

            <div class="panel footerdetail_container">
                <div class="panel-heading nopaddingbottom">
                    <h4>Contact Us</h4>
                </div>
                <div class="panel-body">
                    <hr>
                    <form method="POST" class="form-horizontal">
                        @csrf

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="email_id" value="{{ old('email_id', $content->email_id ?? '') }}"
                                    class="form-control" id="mail" placeholder="Enter email...">
                                @error('email_id')
                                    <label class="error" for="email_id">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email for about us <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="about_us_email" value="{{ old('about_us_email', $content->about_us_email ?? '') }}"
                                    class="form-control" placeholder="Enter email for about us...">
                                @error('about_us_email')
                                    <label class="error" for="about_us_email">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="phone" value="{{ old('phone', $content->phone ?? '') }}"
                                    class="form-control" id="phone" placeholder="Enter phone...">
                                @error('phone')
                                    <label class="error" for="phone">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone for Place an Ad <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="place_an_ad_phone" value="{{ old('place_an_ad_phone', $content->place_an_ad_phone ?? '') }}"
                                    class="form-control" placeholder="Enter phone for place ad...">
                                @error('place_an_ad_phone')
                                    <label class="error" for="place_an_ad_phone">{{ $message }}</label>
                                @enderror
                                <label class="image_format_label">(Enter comma separated phone number)</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Office Days <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="office_days" value="{{ old('office_days', $content->office_days ?? '') }}"
                                    class="form-control" id="days" placeholder="Enter working days...">
                                @error('office_days')
                                    <label class="error" for="office_days">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Office Hours <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="office_hours" value="{{ old('office_hours', $content->office_hours ?? '') }}"
                                    class="form-control" id="picker" placeholder="Enter working hours...">
                                @error('office_hours')
                                    <label class="error" for="office_hours">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Mailing Address <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="mailing_address" value="{{ old('mailing_address', $content->mailing_address ?? '') }}"
                                    class="form-control" id="address" placeholder="Enter mailing address...">
                                @error('mailing_address')
                                    <label class="error" for="mailing_address">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <div class="row submit">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button class="btn btn-success btn-quirk btn-wide mr5" name="submit" id="save" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @extends('common.footer')

        @section('jquerysection')
            <link rel="stylesheet" href="{{ asset('ui/css/daterangepicker.css') }}">
            <link rel="stylesheet" href="{{ asset('ui/css/footerdetail.css') . '?t=' . now()->timestamp }}">
            <script src="{{ asset('ui/js/popper.min.js') }}"></script>
            <script src="{{ asset('ui/js/moment.js') }}"></script>
            <script src="{{ asset('ui/js/daterangepicker.js') }}"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#picker').daterangepicker({
                        timePicker: true,
                        locale: {
                            format: 'hh:mm a'
                        }
                    });
                });
            </script>
        @endsection
    </div>
@endsection
@extends('auth.header')

@section('content')

<body class="signwrapper">
    <div class="panel signin">
        <div class="panel-body">
            <p style="text-align: center;margin-left: 78px;"><img src="{!! asset('ui/img/logo.png') !!}" alt="" class="media-object" style="width: 170px;"></p>
            <div class="or">Welcome</div>
            @if (Session::has('error'))<div id="error">{!! Session::get('error') !!}</div> @endif
            <form method="POST" action="{!! url('admin-login') !!}">
                <div class="form-group @if ($errors->has('user_name')) has-error @endif">
                    <input type="hidden" value="{!! csrf_token()  !!}" name="_token" />
                    <div class="input-group"> <span class="input-group-addon"><i
                                class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" placeholder="Enter Email" name="user_name">
                    </div>
                    @if ($errors->has('user_name'))
                    <div class="error" for="user_name">{{ $errors->first('user_name') }}</div>
                    @endif
                </div>
                <div class="form-group @if ($errors->has('password')) has-error @endif">
                    <div class="input-group"> <span class="input-group-addon"><i
                                class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" placeholder="Enter Password" name="password">
                    </div>
                    @if ($errors->has('password'))
                    <div class="error" for="password">{{ $errors->first('password') }}</div>
                    @endif
                </div>
                <div class="form-group">
                    <input class="btn btn-success btn-quirk btn-block" type="submit" value="Sign In">
                </div>
            </form>
            <hr class="invisible">
        </div>
    </div>
</body>

@endsection

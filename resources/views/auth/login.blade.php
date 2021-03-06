<? $info=DB::table('company_info')->first(); ?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <meta content="{{$info->company_name}}" name="description" />
    <meta content="{{$info->company_name}}" name="author" />
    <link rel="shortcut icon" type="image/png" href='{{asset("images/company/ico/$info->company_icon")}}'>
    <title>Smart Account | {{$info->company_name}}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/app.css') }}">

    <!-- Styles -->

    <style type="text/css">
        body{background:#d2f5ff}
        .login-section{padding-top: 50px;overflow: hidden;}
        .login-content{background: linear-gradient(#fff, rgba(1, 200, 255, 1));padding: 20px 0;padding-bottom: 30px;}
        .brand img{width: 150px;height: auto;margin-bottom: 30px;margin-top: 10px;}
        a.btn-link,p,span{color: #fff;}
    </style>
</head>
<body>
    <div id="app">
        <div class="login-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <div class="panel panel-default login-content">

                            <div class="panel-body">
                                <div class="brand text-center">
                                    <img src='{{asset("images/logo.png")}}' alt="Smart Account">
                                </div>
                                <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                        <div class="col-md-12">
                                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                        <div class="col-md-12">
                                            <input id="password" type="password" class="form-control" name="password" required>

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                           <a class="btn btn-link" href="{{ route('password.request') }}">
                                                Forgot Your Password?
                                            </a>
                                            <button type="submit" class="btn btn-primary pull-right">
                                                Login
                                            </button>

                                            
                                        </div>
                                    </div>
                                </form>
                                <div class="col-md-12 text-center">
                                    <hr>
                                    <p class="text">Helpline : 01844047000</p>
                                    <div class="powered">
                                        <span>Powered By: </span>
                                        <img src="{{asset('images/Smart-Soft-Inc-logo.png')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}"></script>
</body>
</html>



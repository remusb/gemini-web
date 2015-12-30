@extends('auth.base')

@section('page-title', 'Login')
@section('page-class', 'login-page')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <b>Gemini</b>
    </div><!-- /.login-logo -->

    @if ($errors->has())
      @foreach ($errors->all() as $error)
      <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Alert!</h4>
        {{ $error }}
      </div>
      @endforeach
    @endif

    <div class="login-box-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="/auth/login" method="post">
        {!! csrf_field() !!}

        <div class="form-group has-feedback">
          <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>

        <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>

        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input type="checkbox" name="remember"> Remember Me
              </label>
            </div>
          </div><!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div><!-- /.col -->
        </div>
      </form>

      <!--
      <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
        <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
      </div>--><!-- /.social-auth-links -->

      <!--<a href="#">I forgot my password</a><br>-->
      <a href="/auth/register" class="text-center">Register a new account</a>

    </div><!-- /.login-box-body -->
  </div>
@endsection

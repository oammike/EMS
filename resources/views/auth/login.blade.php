@extends('layouts.single')

@section('metatags')
<title>Login to OAMPI Employee Management System</title>
<style type="text/css">
    input#email, input#password{ background: none; border: none; border-bottom: solid 2px #666 }
    /* Change Autocomplete styles in Chrome*/
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover
textarea:-webkit-autofill:focus,
select:-webkit-autofill,
select:-webkit-autofill:hover,
select:-webkit-autofill:focus {
  border:none;
  -webkit-text-fill-color: #333;
  -webkit-box-shadow: 0 0 0px 1000px #f2fcff inset;
  transition: background-color #f2fcff ease-in-out 0s;
}

</style>
@endsection

@section('bodyClasses')
 skin-green login-page
@stop



@section('content')<br/><br/>
<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="login-box-body" style="box-shadow: 0 0 10px #0003"><div class="login-logo"><a href="{{ action('HomeController@index')}} ">
    <img src="{{ asset('public/img/eval-login-logo.png')}}" width="290" style="margin: 0 auto;" /><br/></a>
  </div><br/><br/><br/>
    <p class="login-box-msg" style="color:#333">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sign in to your <strong> OAMPI</strong> account <br/>
   </p>
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label" style="color:#666">Zimbra E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" placeholder="your_handle@openaccessbpo.net" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label" style="color:#666">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label style="color:#666">
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" style="width: 245px">
                                    <i class="fa fa-btn fa-sign-in"></i> LOG IN
                                </button>
                                
                                <div class="clearfix"><br/><br/></div>

                                <small class="text-center"><a  style="color:#333" href="{{ url('/password/reset') }}">Forgot Your Password?</a> &nbsp;|&nbsp; 
                                <!--<a style="color:#333" href="{{ url('/register') }}"><i class="fa fa-pencil "></i> Sign Up Now</a>--></small>
                            </div>
                        </div>
                    </form>

     
    

    

  </div>
  <!-- /.login-box-body -->
 
</div>



@endsection

@extends('layouts.single')

@section('metatags')
<title>Reset Password | OAMPI System</title>
@endsection

@section('bodyClasses')
 skin-green login-page
@stop


<!-- Main Content -->
@section('content')
<br/><br/>
<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="login-box-body"><div class="login-logo"><a href="{{ action('HomeController@index')}} ">
    <img src="{{ asset('public/img/eval-login-logo.png')}}" width="280" style="margin: 0 auto;" /><br/></a>
  </div><br/><br/><br/>
    <p class="login-box-msg text-primary"><strong> Reset Password</strong>  <br/>
   </p>
   @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
   <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label text-gray">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" placeholder="your Zimbra e-mail" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>

     
    

    

  </div>
  <!-- /.login-box-body -->
 
</div>




@endsection

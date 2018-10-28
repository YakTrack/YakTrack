@extends('layouts.blank') 
@section('page')
    <div class="w-full max-w-md mt-10 mx-auto px-6">
        <div class="max-w-xl">
            <div class="text-center">
                <a href="{{ URL::route('home') }}" class="text-4xl no-underline text-grey-darkest">
                    <b>Y</b>ak<b>T</b>rack
                </a>
            </div>
            <div class="text-center mt-4 text-grey-darker">Sign in to start your session</div>
            <div class="card mt-8 bg-grey-lightest">
                <div class="card-body">

                    <form role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="email"> Email </label> 
                            <input type="email" class="form-control" placeholder="Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password"> Password </label>
                            <input type="password" class="form-control" placeholder="Password" name="password">
                        </div>
                        <div class="form-group">
                            <label class="">
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                        <div class="flex">
                            <div class="flex-1 pt-3">
                                <a class="text-grey-light" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                            </div>
                            <div class="flex-1 text-right">
                                <button type="submit" class="btn btn-primary flex-1">Sign In</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.login-card-body -->
        </div>
        <!-- /.login-box -->
    </div>
@endsection
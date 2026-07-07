@extends('layouts.app')

@section('content')
<div class="auth-card glass">
    <h1>Welcome Back</h1>
    <p>Login to read and write news.</p>

    <!-- Social Logins -->
    <button class="btn btn-social">
        <i class="fa-brands fa-google" style="color: #DB4437;"></i>
        Sign in with Google
    </button>
    <button class="btn btn-social">
        <i class="fa-brands fa-facebook" style="color: #4267B2;"></i>
        Sign in with Facebook
    </button>

    <div class="divider">OR</div>

    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="form-group" style="text-align: left;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>
        <div class="form-group" style="text-align: left;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Login</button>
    </form>
    
    <p style="margin-top: 20px; font-size: 0.9rem;">
        Don't have an account? <a href="{{ url('/register') }}">Sign up here</a>
    </p>
</div>
@endsection

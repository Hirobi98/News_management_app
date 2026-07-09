@extends('layouts.app')

@section('content')
<div class="auth-card glass">
    <h1>Account Access</h1>
    <p>Sign in to read and publish dispatches.</p>

    <!-- Social Logins -->
    <button class="btn btn-social">
        <i class="fa-brands fa-google" style="color: var(--text-primary);"></i>
        Continue with Google
    </button>
    <button class="btn btn-social">
        <i class="fa-brands fa-facebook-f" style="color: var(--text-primary);"></i>
        Continue with Facebook
    </button>

    <div class="divider">OR USE REGISTRY</div>

    <form action="{{ url('/login') }}" method="POST">
        @csrf
        <div class="form-group" style="text-align: left;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>
        <div class="form-group" style="text-align: left;">
            <label for="password">Security Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Authenticate</button>
    </form>
    
    <p style="margin-top: 25px; font-size: 0.9rem; color: var(--text-secondary);">
        Not registered yet? <a href="{{ url('/register') }}" style="font-weight: 700;">Subscribe here</a>
    </p>
</div>
@endsection

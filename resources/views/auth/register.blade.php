@extends('layouts.app')

@section('content')
<div class="auth-card glass">
    <h1>Reader Subscription</h1>
    <p>Join the Gazette community as a reader or author.</p>

    <!-- Social Logins -->
    <button class="btn btn-social">
        <i class="fa-brands fa-google" style="color: var(--text-primary);"></i>
        Sign up with Google
    </button>
    <button class="btn btn-social">
        <i class="fa-brands fa-facebook-f" style="color: var(--text-primary);"></i>
        Sign up with Facebook
    </button>

    <div class="divider">OR USE REGISTRY</div>

    <form action="{{ url('/register') }}" method="POST">
        @csrf
        <div class="form-group" style="text-align: left;">
            <label for="name">Full Signature / Name</label>
            <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe">
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="role">Select Registry Role</label>
            <select id="role" name="role" class="form-control" required>
                <option value="reader">Reader Only</option>
                <option value="author">Author Only</option>
                <option value="both">Both (Reader & Author)</option>
            </select>
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="password">Security Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Create Contributor Profile</button>
    </form>
    
    <p style="margin-top: 25px; font-size: 0.9rem; color: var(--text-secondary);">
        Already registered? <a href="{{ url('/login') }}" style="font-weight: 700;">Login here</a>
    </p>
</div>
@endsection

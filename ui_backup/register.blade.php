@extends('layouts.app')

@section('content')
<div class="auth-card glass">
    <h1>Create Account</h1>
    <p>Join the community as a reader or author.</p>

    <!-- Social Logins -->
    <button class="btn btn-social">
        <i class="fa-brands fa-google" style="color: #DB4437;"></i>
        Sign up with Google
    </button>
    <button class="btn btn-social">
        <i class="fa-brands fa-facebook" style="color: #4267B2;"></i>
        Sign up with Facebook
    </button>

    <div class="divider">OR</div>

    @if ($errors->any())
        <div class="alert alert-error" style="background: rgba(169, 68, 56, 0.1); border-left: 3px solid var(--secondary-color); color: var(--secondary-color); text-align: left; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px; font-family: var(--font-sans); font-size: 0.9rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/register') }}" method="POST">
        @csrf
        <div class="form-group" style="text-align: left;">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe">
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="role">Select Role</label>
            <select id="role" name="role" class="form-control" required style="appearance: none;">
                <option value="reader">Reader Only</option>
                <option value="author">Author Only</option>
                <option value="both">Both (Reader & Author)</option>
                <option value="channel">News Channel Admin</option>
            </select>
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            <small style="color: var(--text-secondary); font-size: 0.8rem; display: block; margin-top: 5px;">Must be at least 8 chars, 1 uppercase, 1 lowercase, 1 number, and 1 symbol.</small>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Sign Up</button>
    </form>
    
    <p style="margin-top: 20px; font-size: 0.9rem;">
        Already have an account? <a href="{{ url('/login') }}">Login here</a>
    </p>
</div>
@endsection

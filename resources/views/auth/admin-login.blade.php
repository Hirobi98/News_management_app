@extends('layouts.app')

@section('content')
<div class="auth-card" style="border-top: 4px solid var(--secondary-color);">
    <h1 class="auth-header" style="color: var(--secondary-color);">SUPER ADMIN PORTAL</h1>
    
    <div style="text-align: center; margin-bottom: 20px;">
        <i class="fa-solid fa-user-shield" style="font-size: 3rem; color: var(--secondary-color);"></i>
    </div>
    
    <p style="text-align: center; font-style: italic; color: var(--text-secondary); margin-bottom: 20px;">Secure access restricted to the Supreme Editor.</p>

    @if($errors->any())
        <div class="alert alert-error" style="background: rgba(169,68,56,0.1); border-color: var(--secondary-color);">
            @foreach($errors->all() as $error)
                <p style="margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ url('/admin/superadmin') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Admin Signature (Email)</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Security Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--secondary-color); border-color: var(--secondary-color);">Access Terminal</button>
    </form>
</div>
@endsection

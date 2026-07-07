@extends('layouts.app')

@section('content')
<div class="glass" style="max-width: 600px; margin: 40px auto; padding: 40px; text-align: center;">
    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
        <i class="fa-solid fa-user"></i>
    </div>
    
    <h1 style="margin-bottom: 5px;">{{ $user['name'] }}</h1>
    <p style="color: var(--text-secondary); margin-top: 0; font-size: 1.1rem;">{{ $user['email'] }}</p>

    <div style="display: inline-block; background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 20px; margin-top: 10px; font-weight: 600; text-transform: capitalize;">
        Role: {{ $user['role'] }}
    </div>

    <div class="divider" style="margin: 30px 0;"></div>

    <div style="display: flex; justify-content: space-around; text-align: center;">
        <div>
            <h3 style="margin-bottom: 5px; font-size: 1.5rem;">{{ $user['saved_articles'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">Saved Articles</p>
        </div>
        
        @if($user['role'] === 'author' || $user['role'] === 'both')
        <div>
            <h3 style="margin-bottom: 5px; font-size: 1.5rem; color: var(--secondary-color);">{{ $user['published_articles'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">Published News</p>
        </div>
        @endif

        <div>
            <h3 style="margin-bottom: 5px; font-size: 1.1rem; padding-top: 5px;">{{ $user['joined'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">Joined Date</p>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="profile-badge">
    <div class="profile-avatar">
        <i class="fa-solid fa-user-tie"></i>
    </div>
    
    <h1 style="font-family: var(--font-serif); font-size: 2.2rem; margin: 0 0 5px; font-weight: 700;">{{ $user['name'] }}</h1>
    <p style="color: var(--text-secondary); margin-top: 0; font-size: 1rem; font-family: var(--font-serif); font-style: italic;">{{ $user['email'] }}</p>

    <div class="profile-role">
        Registry: {{ $user['role'] }} Contributor
    </div>

    <div style="display: flex; justify-content: space-around; text-align: center; margin-top: 40px; border-top: 1px solid var(--border-color); padding-top: 30px;">
        <div style="flex: 1; border-right: 1px solid var(--border-color); padding: 0 10px;">
            <h3 style="margin-bottom: 5px; font-size: 1.8rem; font-family: var(--font-serif); font-weight: 700; color: var(--primary-color);">{{ $user['saved_articles'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Saved Dispatches</p>
        </div>
        
        @if($user['role'] === 'author' || $user['role'] === 'both')
        <div style="flex: 1; border-right: 1px solid var(--border-color); padding: 0 10px;">
            <h3 style="margin-bottom: 5px; font-size: 1.8rem; font-family: var(--font-serif); font-weight: 700; color: var(--primary-color);">{{ $user['published_articles'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Published Stories</p>
        </div>
        @endif

        <div style="flex: 1; padding: 0 10px;">
            <h3 style="margin-bottom: 5px; font-size: 1.25rem; font-family: var(--font-serif); font-weight: 700; padding-top: 6px; color: var(--text-primary);">{{ $user['joined'] }}</h3>
            <p style="color: var(--text-secondary); margin: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Registry Date</p>
        </div>
    </div>
</div>
@endsection

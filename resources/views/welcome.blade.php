@extends('layouts.app')

@section('content')
<div class="text-center" style="margin-top: 60px; margin-bottom: 80px;">
    <h1 style="font-family: var(--font-serif); font-size: 3.8rem; font-weight: 900; margin-bottom: 20px; color: var(--text-primary); text-transform: uppercase; letter-spacing: -1px; line-height: 1.1;">
        The Art of Independent Journalism.
    </h1>
    <p style="font-family: var(--font-serif); font-style: italic; font-size: 1.4rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto 40px; line-height: 1.5;">
        Join a distinguished circle of readers and authors. Discover daily dispatches, publish your stories, and share thoughtful perspectives with the world.
    </p>
    
    <div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 80px;">
        @if(session('user_logged_in'))
            <a href="{{ url('/home') }}" class="btn btn-primary" style="font-size: 1rem; padding: 14px 28px;">Go to News Feed</a>
            <a href="{{ url('/profile') }}" class="btn btn-secondary" style="font-size: 1rem; padding: 14px 28px;">View Profile</a>
        @else
            <a href="{{ url('/register') }}" class="btn btn-primary" style="font-size: 1rem; padding: 14px 28px;">Subscribe & Register</a>
            <a href="{{ url('/login') }}" class="btn btn-secondary" style="font-size: 1rem; padding: 14px 28px;">Sign In to Account</a>
        @endif
    </div>

    <div class="glass" style="text-align: left; max-width: 900px; width: 100%; margin: 0 auto; padding: 40px; box-sizing: border-box;">
        <h2 class="text-center" style="font-family: var(--font-serif); font-size: 2rem; margin-top: 0; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 0.5px;">Why Read The Gazette?</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 40px;">
            <div style="border-right: 1px solid var(--border-color); padding-right: 20px;">
                <i class="fa-solid fa-book-open" style="font-size: 1.8rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin: 10px 0;">Curated Channels</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">Access handpicked news from specialized channels designed to keep you highly informed without noise.</p>
            </div>
            <div style="border-right: 1px solid var(--border-color); padding-right: 20px;">
                <i class="fa-solid fa-pen-nib" style="font-size: 1.8rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin: 10px 0;">Independent Authors</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">Become an authorized contributor. Publish articles with clean, professional layouts that honor your writing.</p>
            </div>
            <div>
                <i class="fa-solid fa-comments" style="font-size: 1.8rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin: 10px 0;">Civil Discussions</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">Engage with other members via letters and comments. Save relevant articles to build your personal news archive.</p>
            </div>
        </div>
    </div>
</div>
@endsection

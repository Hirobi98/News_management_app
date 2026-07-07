@extends('layouts.app')

@section('content')
<div class="text-center" style="margin-top: 100px; margin-bottom: 100px;">
    <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        The Future of News Reading.
    </h1>
    <p style="font-size: 1.25rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto 40px;">
        Join thousands of readers and authors. Discover breaking news, write your own stories, and share your perspective with the world.
    </p>
    
    <div style="display: flex; gap: 20px; justify-content: center;">
        <a href="{{ url('/register') }}" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;">Get Started Today</a>
        <a href="{{ url('/login') }}" class="btn btn-secondary" style="font-size: 1.1rem; padding: 15px 30px;">Login to Account</a>
    </div>

    <div class="glass" style="margin-top: 80px; padding: 40px; display: inline-block; text-align: left; max-width: 800px; width: 100%;">
        <h2 class="text-center" style="margin-bottom: 30px;">Why Choose NewsApp?</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px;">
            <div>
                <i class="fa-solid fa-book-open" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3>Read the Best</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Access handpicked news from top channels and stay informed.</p>
            </div>
            <div>
                <i class="fa-solid fa-pen-nib" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 15px;"></i>
                <h3>Write & Publish</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Sign up as an author and start publishing your own news articles.</p>
            </div>
            <div>
                <i class="fa-solid fa-comments" style="font-size: 2rem; color: #10B981; margin-bottom: 15px;"></i>
                <h3>Engage & Share</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Comment on stories, save them for later, and share to social media.</p>
            </div>
        </div>
    </div>
</div>
@endsection

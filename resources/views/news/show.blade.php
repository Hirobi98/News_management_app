@extends('layouts.app')

@section('content')
<style>
    .article-title {
        font-family: var(--font-serif);
        font-size: 3.5rem;
        line-height: 1.15;
        margin-bottom: 25px;
        color: var(--text-main);
        text-align: center;
        text-transform: capitalize;
    }
    .article-content {
        font-family: var(--font-serif);
        font-size: 1.25rem;
        line-height: 1.9;
        color: var(--text-main);
        margin-bottom: 50px;
        text-align: justify;
    }
    .article-content::first-letter {
        font-size: 5rem;
        float: left;
        margin-right: 15px;
        margin-bottom: -15px;
        margin-top: 5px;
        font-family: var(--font-serif);
        font-weight: bold;
        color: var(--primary-color);
        line-height: 0.8;
    }
    .vintage-image-frame {
        margin: 40px auto;
        padding: 12px;
        background: #fdfbf7;
        border: 1px solid #d3c4a1;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.03), 0 4px 15px rgba(0,0,0,0.05);
        max-width: 100%;
    }
    .vintage-image-frame img {
        width: 100%;
        height: auto;
        display: block;
        border: 1px solid #e5dcc3;
    }
    .meta-bar {
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-top: 3px double var(--primary-color); 
        border-bottom: 3px double var(--primary-color); 
        padding: 15px 0; 
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 15px;
    }
</style>

<div style="max-width: 850px; margin: 0 auto; padding: 20px;">
    
    <a href="{{ url('/home') }}" style="display: inline-block; margin-bottom: 30px; font-family: var(--font-sans); text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; color: var(--text-secondary); text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Return to Front Page</a>

    <h1 class="article-title">
        {{ $news['title'] }}
    </h1>
    
    <div class="meta-bar">
        <div style="font-family: var(--font-sans); font-size: 0.95rem; letter-spacing: 0.5px;">
            <strong style="color: var(--primary-color);">{{ strtoupper($news['target_channel_name']) }}</strong> &nbsp;|&nbsp; 
            <span style="color: var(--text-secondary);"><i class="fa-regular fa-clock"></i> {{ $news['date'] }}</span> &nbsp;|&nbsp; 
            <span style="color: var(--text-secondary);"><i class="fa-solid fa-pen-nib"></i> {{ strtoupper($news['author']) }}</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary" onclick="alert('News saved to your profile!')" style="padding: 6px 12px; font-size: 0.85rem;"><i class="fa-regular fa-bookmark"></i> Save</button>
            <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn" style="background: #4267B2; color: white; padding: 6px 12px; font-size: 0.85rem;"><i class="fa-brands fa-facebook"></i> Share</a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($news['title']) }}&url={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn" style="background: #1DA1F2; color: white; padding: 6px 12px; font-size: 0.85rem;"><i class="fa-brands fa-twitter"></i> Tweet</a>
        </div>
    </div>

    @if(!empty($news['image']))
        <div class="vintage-image-frame">
            <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}">
        </div>
    @endif

    <div class="article-content">
        {!! nl2br(e($news['content'])) !!}
    </div>

    <!-- Channel Profile Section -->
    <div class="channel-profile" style="background: transparent; border-top: 1px dashed var(--border-color); border-bottom: 1px dashed var(--border-color); padding: 30px 0; display: flex; align-items: center; gap: 20px; margin-bottom: 50px;">
        <div class="profile-pic" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: #f0f0f0; flex-shrink: 0; border: 2px solid var(--border-color);">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($news['target_channel_name']) }}&background=random&size=80&color=fff" alt="{{ $news['target_channel_name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="profile-info">
            <h3 style="margin: 0 0 5px 0; font-family: var(--font-serif); font-size: 1.4rem;">{{ $news['target_channel_name'] }}</h3>
            <p style="margin: 0 0 10px 0; color: var(--text-secondary); font-family: var(--font-sans); display: flex; align-items: center; gap: 8px;">
                <i class="fa-regular fa-envelope"></i> {{ strtolower($news['target_channel_email'] ?? 'contact@news.com') }}
            </p>
            <p style="margin: 0; font-family: var(--font-sans); font-size: 0.95rem; color: var(--text-main);">
                Official news channel bringing you the latest updates. Follow us for more trusted reports.
            </p>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="comments-section" style="border-top: 2px solid var(--primary-color); padding-top: 30px;">
        <h2 style="font-family: var(--font-serif); text-transform: uppercase; margin-bottom: 20px;">Letters to the Editor ({{ count($news['comments']) }})</h2>

        <div style="margin-bottom: 40px;">
            <form action="{{ url('/news/' . $news['id'] . '/comment') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 10px;">
                    <textarea name="comment" rows="3" class="form-control" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        </div>

        @forelse($news['comments'] as $comment)
            <div class="comment">
                <div style="margin-bottom: 5px;"><strong><i class="fa-solid fa-user-circle"></i> {{ $comment['user'] }}</strong></div>
                <p style="margin: 0; color: var(--text-secondary);">{{ $comment['text'] }}</p>
            </div>
        @empty
            <p style="color: var(--text-secondary); font-style: italic;">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>
</div>
@endsection

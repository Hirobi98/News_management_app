@extends('layouts.app')

@section('content')
<div style="margin-top: 30px;">
    
    <a href="{{ url('/home') }}" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 30px; text-decoration: none;">
        <i class="fa-solid fa-arrow-left-long"></i> Back to Dispatches
    </a>

    <article class="article-detail">
        <h1 class="article-headline">{{ $news['title'] }}</h1>
        
        <div class="article-lead-meta">
            <div style="display: flex; gap: 20px; align-items: center; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; color: var(--text-secondary);">
                <span><i class="fa-solid fa-user-nib" style="color: var(--primary-color);"></i> By {{ $news['author'] }}</span>
                <span class="channel">{{ $news['channel'] }}</span>
                <span><i class="fa-regular fa-calendar"></i> {{ $news['date'] }}</span>
            </div>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem;" onclick="alert('Dispatch bookmarked to your profile archives!')">
                    <i class="fa-regular fa-bookmark"></i> Save
                </button>
                <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem;">
                    <i class="fa-brands fa-facebook-f" style="color: var(--secondary-color);"></i> Share
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($news['title']) }}&url={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem;">
                    <i class="fa-brands fa-twitter" style="color: var(--secondary-color);"></i> Tweet
                </a>
            </div>
        </div>

        <div class="article-content">
            <p class="drop-cap">{{ $news['content'] }}</p>
            <p>Furthermore, this development marks a significant milestone in modern journalism, prompting deeper dialogue across our readership base. As we continue to witness these shifts in the cultural and socio-economic landscapes, our correspondents will deliver dedicated updates directly to your daily dispatches.</p>
            <p style="font-family: var(--font-sans); font-size: 0.9rem; color: var(--text-secondary); border-top: 1px dashed var(--border-color); padding-top: 20px; margin-top: 40px; font-style: italic;">
                The Gazette Editorial Board. London Branch. All rights reserved.
            </p>
        </div>
    </article>

    <!-- Comments Section (Letters to the Editor) -->
    <div class="comments-section">
        <h2>Letters to the Editor ({{ count($news['comments']) }})</h2>

        <div style="margin-bottom: 40px; background: var(--card-bg); padding: 30px; border: 1px solid var(--border-color); position: relative;">
            <div style="position: absolute; top: 4px; left: 4px; right: 4px; bottom: 4px; border: 1px solid var(--border-light); pointer-events: none;"></div>
            <h3 style="font-family: var(--font-serif); font-size: 1.2rem; margin-top: 0; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Submit a Letter</h3>
            <form action="{{ url('/news/' . $news['id'] . '/comment') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 15px;">
                    <textarea name="comment" rows="4" class="form-control" placeholder="Write your letter regarding this dispatch..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Letter</button>
            </form>
        </div>

        @forelse($news['comments'] as $comment)
            <div class="comment">
                <div style="margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <strong><i class="fa-solid fa-signature"></i> {{ $comment['user'] }}</strong>
                    <span style="font-size: 0.7rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.5px;">Reader Response</span>
                </div>
                <p style="margin: 0; color: var(--text-secondary); font-family: var(--font-serif); font-size: 1.05rem; line-height: 1.5; font-style: italic;">
                    "{{ $comment['text'] }}"
                </p>
            </div>
        @empty
            <p style="color: var(--text-secondary); font-style: italic; text-align: center; margin: 40px 0;">No letters have been received for this dispatch yet. Be the first to write.</p>
        @endforelse
    </div>
</div>
@endsection

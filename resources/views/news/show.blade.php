@extends('layouts.app')

@section('content')
<div class="glass" style="padding: 40px; margin-top: 20px;">
    
    <a href="{{ url('/home') }}" style="display: inline-block; margin-bottom: 20px;"><i class="fa-solid fa-arrow-left"></i> Back to Feed</a>

    <h1 style="font-size: 2.5rem; margin-bottom: 10px; background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        {{ $news['title'] }}
    </h1>
    
    <div class="news-meta" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 20px; margin-bottom: 20px;">
        <div>
            <span style="margin-right: 15px;"><i class="fa-solid fa-user"></i> {{ $news['author'] }}</span>
            <span class="channel">{{ $news['channel'] }}</span>
            <span style="margin-left: 15px;"><i class="fa-regular fa-calendar"></i> {{ $news['date'] }}</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary" onclick="alert('News saved to your profile!')"><i class="fa-regular fa-bookmark"></i> Save</button>
            <a href="https://facebook.com/sharer/sharer.php?u={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn" style="background: #4267B2; color: white;"><i class="fa-brands fa-facebook"></i> Share</a>
            <a href="https://twitter.com/intent/tweet?text={{ urlencode($news['title']) }}&url={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" class="btn" style="background: #1DA1F2; color: white;"><i class="fa-brands fa-twitter"></i> Tweet</a>
        </div>
    </div>

    <div style="font-size: 1.1rem; line-height: 1.8; color: var(--text-primary); margin-bottom: 40px;">
        <p>{{ $news['content'] }}</p>
        <p><em>(More content would go here in a real application...)</em></p>
    </div>

    <!-- Comments Section -->
    <div class="comments-section">
        <h2>Comments ({{ count($news['comments']) }})</h2>

        <div style="margin-bottom: 30px; background: rgba(15, 23, 42, 0.4); padding: 20px; border-radius: 8px; border: 1px solid var(--glass-border);">
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

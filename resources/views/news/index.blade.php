@extends('layouts.app')

@section('content')
<div>
    <div class="flex justify-between items-center" style="margin-bottom: 20px;">
        <h1>Latest News Feed</h1>
        @if(session('user_role') === 'author' || session('user_role') === 'both')
            <a href="{{ url('/news/create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Write News</a>
        @endif
    </div>

    @if(session('error'))
        <div class="alert" style="background: rgba(239, 68, 68, 0.2); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.5);">
            {{ session('error') }}
        </div>
    @endif

    <div class="news-grid">
        @forelse($newsList as $news)
            <div class="news-card glass">
                <h3><a href="{{ url('/news/' . $news['id']) }}">{{ $news['title'] }}</a></h3>
                <p>{{ Str::limit($news['content'], 120) }}</p>
                <div class="news-meta">
                    <span><i class="fa-solid fa-user"></i> {{ $news['author'] }}</span>
                    <span class="channel">{{ $news['channel'] }}</span>
                </div>
                <div class="news-meta" style="margin-top: 10px;">
                    <span><i class="fa-regular fa-calendar"></i> {{ $news['date'] }}</span>
                    <a href="{{ url('/news/' . $news['id']) }}" style="font-weight: 600;">Read More <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        @empty
            <div class="glass" style="grid-column: 1 / -1; padding: 40px; text-align: center;">
                <h2>No news articles available.</h2>
            </div>
        @endforelse
    </div>
</div>
@endsection

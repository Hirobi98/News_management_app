@extends('layouts.app')

@section('content')
<div>
    <!-- Author Post Composer -->
    @if(strtolower(session('user_role')) === 'author' || strtolower(session('user_role')) === 'both')
        <div class="post-composer">
            <h3><i class="fa-solid fa-pen-nib"></i> Draft a New Dispatch</h3>
            <form action="{{ url('/news') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="text" name="title" class="form-control" placeholder="Headline..." required style="font-family: var(--font-serif); font-size: 1.5rem; font-weight: 700; border: none; border-bottom: 2px solid var(--primary-color); border-radius: 0; padding-left: 0; background: transparent;">
                </div>
                
                <div class="form-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select name="category" class="form-control" required style="width: auto; font-family: var(--font-sans); text-transform: uppercase;">
                        <option value="">Select Category</option>
                        <option value="World" {{ (isset($currentCategory) && strtolower($currentCategory) === 'world') ? 'selected' : '' }}>World</option>
                        <option value="Opinion" {{ (isset($currentCategory) && strtolower($currentCategory) === 'opinion') ? 'selected' : '' }}>Opinion</option>
                        <option value="Culture" {{ (isset($currentCategory) && strtolower($currentCategory) === 'culture') ? 'selected' : '' }}>Culture</option>
                        <option value="Sports" {{ (isset($currentCategory) && strtolower($currentCategory) === 'sports') ? 'selected' : '' }}>Sports</option>
                    </select>
                    <select name="target_channel" class="form-control" required style="width: auto; font-family: var(--font-sans); text-transform: uppercase;">
                        <option value="">Select News Channel</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel->id ?? $channel->ID }}">{{ $channel->name ?? $channel->NAME }} ({{ $channel->email ?? $channel->EMAIL }})</option>
                        @endforeach
                    </select>
                    <input type="file" name="image" class="form-control" accept="image/*" style="width: auto; font-family: var(--font-sans);">
                </div>
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="What is the story?" required style="font-style: italic;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publish Dispatch</button>
            </form>
        </div>
    @endif

    <div class="flex justify-between items-center" style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
        <h1 style="font-family: var(--font-serif); font-size: 2rem; margin: 0; text-transform: uppercase;">{{ isset($currentCategory) ? $currentCategory . ' Section' : 'Latest Dispatches' }}</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="news-grid">
        @forelse($newsList as $news)
            <div class="news-card">
                <h3><a href="{{ url('/news/' . $news['id']) }}">{{ $news['title'] }}</a></h3>
                <div class="news-meta" style="border: none; padding-top: 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px;">
                    <span class="channel" style="color: var(--primary-color);">PUBLISHED VIA {{ strtoupper($news['target_channel_name']) }}</span>
                    <span class="author">WRITTEN BY {{ strtoupper($news['author']) }}</span>
                </div>
                
                <p>{{ Str::limit($news['content'], 150) }}</p>
                
                <div class="news-meta" style="margin-top: 10px;">
                    <span style="font-family: var(--font-serif); font-style: italic; font-weight: normal; text-transform: none;"><i class="fa-regular fa-clock"></i> {{ $news['date'] }}</span>
                    
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <button class="btn-icon" title="Share Dispatch" onclick="alert('Link copied to clipboard!')">
                            <i class="fa-solid fa-share-nodes"></i> Share
                        </button>
                        <a href="{{ url('/news/' . $news['id']) }}" style="font-family: var(--font-sans); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Read Full <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; border: 1px dashed var(--border-color);">
                <h2 style="font-family: var(--font-serif); color: var(--text-secondary);">No dispatches available at the moment.</h2>
            </div>
        @endforelse
    </div>
</div>
@endsection

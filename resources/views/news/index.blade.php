@extends('layouts.app')

@section('content')
<div>
    <!-- Author Post Composer -->
    @if(session('user_role') === 'author' || session('user_role') === 'both')
        <div class="post-composer">
            <h3><i class="fa-solid fa-pen-nib"></i> Draft a New Dispatch</h3>
            <form action="{{ url('/news') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="title" class="form-control" placeholder="Headline..." required style="font-weight: 700; font-size: 1.2rem; margin-bottom: 10px;">
                </div>
                <div class="form-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select name="category" class="form-control" required style="width: auto; font-family: var(--font-sans); text-transform: uppercase;">
                        <option value="">Select Category</option>
                        <option value="World">World</option>
                        <option value="Opinion">Opinion</option>
                        <option value="Culture">Culture</option>
                        <option value="Sports">Sports</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="What is the story?" required style="font-style: italic;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publish Dispatch</button>
            </form>
        </div>
    @endif

    <div class="flex justify-between items-center" style="margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">
        <h1 style="font-family: var(--font-serif); font-size: 2rem; margin: 0; text-transform: uppercase;">Latest Dispatches</h1>
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
                    <span class="author">BY {{ $news['author'] }}</span>
                    <span class="channel">IN {{ $news['channel'] }}</span>
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

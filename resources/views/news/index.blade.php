@extends('layouts.app')

@section('content')
<div>
    <div class="flex justify-between items-center" style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-family: var(--font-serif); font-size: 2rem; margin: 0; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-primary);">Today's Feed</h2>
        @if(session('user_role') === 'author' || session('user_role') === 'both')
            <a href="{{ url('/news/create') }}" class="btn btn-primary"><i class="fa-solid fa-pen-fancy"></i> Submit Dispatch</a>
        @endif
    </div>

    @if(session('error'))
        <div class="alert" style="background-color: #F8D7DA; color: #721C24; border: 1px solid #F5C6CB;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Category filter bar inspired by vintage dispatches design -->
    <div style="display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; overflow-x: auto; white-space: nowrap; align-items: center;">
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; color: var(--text-secondary); margin-right: 10px;">Sections:</span>
        <a href="#" class="btn btn-primary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">All Dispatches</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Technology</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Finance</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Science</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Politics</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Sports</a>
        <a href="#" class="btn btn-secondary" style="padding: 5px 14px; font-size: 0.75rem; border-radius: 20px; text-decoration: none;">Entertainment</a>
    </div>

    <div class="news-grid">
        @forelse($newsList as $news)
            <div class="news-card {{ $loop->first ? 'featured' : '' }}" onclick="window.location='{{ url('/news/' . $news['id']) }}'">
                @if($loop->first)
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; color: var(--primary-color); font-weight: 800; margin-bottom: 8px;">
                        &bigstar; Editorial Pick
                    </div>
                @endif
                <h3><a href="{{ url('/news/' . $news['id']) }}">{{ $news['title'] }}</a></h3>
                <p>{{ Str::limit($news['content'], $loop->first ? 260 : 120) }}</p>
                <div class="news-meta">
                    <span><i class="fa-solid fa-user-nib"></i> {{ $news['author'] }}</span>
                    <span class="channel">{{ $news['channel'] }}</span>
                </div>
                <div class="news-meta" style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed var(--border-light);">
                    <span><i class="fa-regular fa-calendar"></i> {{ $news['date'] }}</span>
                    <a href="{{ url('/news/' . $news['id']) }}" style="font-weight: 700; color: var(--primary-color); text-decoration: none;">Read Dispatch <i class="fa-solid fa-arrow-right-long" style="font-size: 0.8rem; margin-left: 2px;"></i></a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 60px 40px; text-align: center; border: 1px dashed var(--border-color); background: var(--card-bg);">
                <h2 style="font-family: var(--font-serif); margin: 0 0 10px; color: var(--text-primary);">No news dispatches available.</h2>
                <p style="color: var(--text-secondary); margin: 0;">Check back later for fresh updates from our editorial team.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

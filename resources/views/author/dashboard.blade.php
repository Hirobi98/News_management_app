@extends('layouts.app')

@section('content')
<div class="auth-card" style="max-width: 1000px; padding: 40px; margin: 40px auto; text-align: left;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; text-transform: uppercase; border-bottom: 2px solid var(--primary-color); padding-bottom: 15px; margin-bottom: 30px;">
        Author Dashboard
    </h1>

    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Inbox Notifications</h2>
    <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); margin-bottom: 40px; max-height: 300px; overflow-y: auto;">
        @php $hasMessages = false; @endphp
        
        {{-- Display actionable modification requests directly in the inbox --}}
        @foreach($newsList as $news)
            @if($news['status'] === 'Modification_Required')
                @php $hasMessages = true; @endphp
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 15px;">
                    <p style="margin: 0; font-family: var(--font-serif); font-weight: bold; font-size: 1.1rem; color: var(--primary-color);">Modification Request from {{ $news['channel'] ?? 'News Channel' }}</p>
                    <p style="margin: 5px 0; font-family: var(--font-sans);"><strong>Article:</strong> {{ $news['title'] }}</p>
                    <div style="background: rgba(169, 68, 56, 0.1); padding: 10px; margin-top: 10px; border-left: 3px solid var(--secondary-color); font-style: italic;">
                        {{ $news['admin_feedback'] ?? 'No specific feedback provided.' }}
                    </div>
                    <div style="margin-top: 15px;">
                        <a href="{{ url('/news/' . $news['id'] . '/edit') }}" class="btn btn-secondary" style="padding: 5px 15px; font-size: 0.85rem;">Modify & Resubmit</a>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Display standard text messages --}}
        @foreach($inboxMessages ?? [] as $msg)
            @php $hasMessages = true; @endphp
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 10px;">
                <p style="margin: 0; font-family: var(--font-sans);"><i class="fa-solid fa-envelope" style="color: var(--secondary-color); margin-right: 10px;"></i> {{ $msg->message ?? $msg->MESSAGE }}</p>
                <span style="font-size: 0.8rem; color: var(--text-secondary); font-style: italic;">{{ date('F j, Y, g:i a', strtotime($msg->created_at ?? $msg->CREATED_AT)) }}</span>
            </div>
        @endforeach

        @if(!$hasMessages)
            <p style="color: var(--text-secondary); font-style: italic;">You have no new messages.</p>
        @endif
    </div>

    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Your Dispatches</h2>

    <div class="news-grid">
        @forelse($newsList as $news)
            <div class="news-card">
                <h3>{{ $news['title'] }}</h3>
                <div class="news-meta" style="border: none; padding-top: 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px;">
                    <span class="channel">STATUS: {{ str_replace('_', ' ', strtoupper($news['status'])) }}</span>
                </div>
                <p>{{ Str::limit($news['content'], 100) }}</p>
                
                @if($news['status'] === 'Modification_Required')
                    <div style="background: rgba(169, 68, 56, 0.1); padding: 15px; margin-bottom: 15px; border-left: 3px solid var(--secondary-color);">
                        <strong>Status:</strong> <span style="font-style: italic;">Requires Modification (Check your Inbox)</span>
                    </div>
                @elseif($news['status'] === 'Rejected_Permanent')
                    <div style="background: rgba(169, 68, 56, 0.1); padding: 15px; margin-bottom: 15px; border-left: 3px solid var(--secondary-color);">
                        <strong>Status:</strong> <span style="font-style: italic;">This article was permanently rejected.</span>
                    </div>
                @else
                    <div class="news-meta" style="margin-top: auto;">
                        <span style="font-style: italic;"><i class="fa-regular fa-clock"></i> {{ $news['date'] }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; border: 1px dashed var(--border-color);">
                <p style="font-family: var(--font-serif); color: var(--text-secondary); font-size: 1.2rem;">You haven't written any dispatches yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

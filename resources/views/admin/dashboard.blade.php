@extends('layouts.app')

@section('content')
<div class="auth-card" style="max-width: 1000px; padding: 40px; margin: 40px auto; text-align: left;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; text-transform: uppercase; border-bottom: 2px solid var(--primary-color); padding-bottom: 15px; margin-bottom: 30px;">
        Super Admin Dashboard
    </h1>

    <div style="display: flex; gap: 20px; margin-bottom: 40px;">
        <div style="flex: 1; padding: 20px; background: #fff; border: 1px solid var(--border-color); text-align: center;">
            <h3 style="margin: 0; font-size: 2rem; color: var(--primary-color);">{{ $totalArticles }}</h3>
            <p style="margin: 0; font-family: var(--font-sans); font-size: 0.8rem; text-transform: uppercase;">Total Articles</p>
        </div>
        <div style="flex: 1; padding: 20px; background: #fff; border: 1px solid var(--border-color); text-align: center;">
            <h3 style="margin: 0; font-size: 2rem; color: var(--primary-color);">{{ $totalUsers }}</h3>
            <p style="margin: 0; font-family: var(--font-sans); font-size: 0.8rem; text-transform: uppercase;">Total Users</p>
        </div>
        <div style="flex: 1; padding: 20px; background: #fff; border: 1px solid var(--border-color); text-align: center;">
            <h3 style="margin: 0; font-size: 2rem; color: var(--primary-color);">{{ $totalComments }}</h3>
            <p style="margin: 0; font-family: var(--font-sans); font-size: 0.8rem; text-transform: uppercase;">Total Comments</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Final Review (Pending_Admin)</h2>

    <div class="news-grid">
        @php
            $pendingAdminNews = array_filter($newsList, function($n) {
                return $n['status'] === 'Pending_Admin';
            });
        @endphp

        @forelse($pendingAdminNews as $news)
            <div class="news-card">
                <h3>{{ $news['title'] }}</h3>
                <div class="news-meta" style="border: none; padding-top: 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px;">
                    <span class="author">BY {{ $news['author'] }}</span>
                    <span class="channel" style="color: var(--primary-color);">VIA {{ $news['target_channel_name'] }}</span>
                </div>
                <p style="font-style: italic;">{{ $news['content'] }}</p>
                
                <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <form action="{{ url('/admin/review/' . $news['id']) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Feedback (Required if Rejecting)</label>
                            <textarea name="feedback" class="form-control" rows="2" placeholder="Explain why it was rejected..."></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" name="action" value="approve" class="btn btn-primary" style="flex: 1; background: #2E7D32; border-color: #2E7D32;">Publish Article</button>
                            <button type="submit" name="action" value="reject" class="btn btn-secondary" style="flex: 1; border-color: var(--secondary-color); color: var(--secondary-color);">Reject & Send Back</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; border: 1px dashed var(--border-color);">
                <p style="font-family: var(--font-serif); color: var(--text-secondary); font-size: 1.2rem;">No pending articles waiting for final review.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection

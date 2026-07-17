@extends('layouts.app')

@section('content')
<div class="auth-card" style="max-width: 1000px; padding: 40px; margin: 40px auto; text-align: left;">
    <h1 style="font-family: var(--font-serif); font-size: 2.5rem; text-transform: uppercase; border-bottom: 2px solid var(--primary-color); padding-bottom: 15px; margin-bottom: 30px;">
        Channel Moderation Dashboard
    </h1>

    <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); margin-bottom: 30px;">
        <h2 style="font-family: var(--font-serif); margin-top: 0; margin-bottom: 5px;">{{ session('user_name') }}</h2>
        <p style="color: var(--text-secondary); margin-bottom: 0;">{{ session('user_email') }}</p>
    </div>

    <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); margin-bottom: 40px;">
        <h3 style="font-family: var(--font-serif); margin-top: 0; text-transform: uppercase; font-size: 1.2rem;">Hire Author to Roster</h3>
        <form action="{{ url('/channel/add-author') }}" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
            @csrf
            <input type="email" name="author_email" class="form-control" placeholder="Enter author's email address..." required style="flex: 1;">
            <button type="submit" class="btn btn-primary">Add Author</button>
        </form>

        <button id="toggleRosterBtn" class="btn btn-secondary">Show All Hired Authors</button>
        
        <div id="rosterList" style="display: none; margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
            <ul style="list-style: none; padding: 0; margin: 0; font-family: var(--font-sans);">
                @forelse($roster ?? [] as $author)
                    <li style="padding: 10px 0; border-bottom: 1px dashed var(--border-color); display: flex; justify-content: space-between;">
                        <span style="font-weight: 600;">{{ $author->name ?? $author->NAME }}</span>
                        <span style="color: var(--text-secondary);">{{ $author->email ?? $author->EMAIL }}</span>
                    </li>
                @empty
                    <li style="padding: 10px 0; color: var(--text-secondary); font-style: italic;">No authors hired yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Pending Dispatches for Review</h2>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="news-grid">
        @forelse($newsList as $news)
            <div class="news-card">
                <h3>{{ $news['title'] }}</h3>
                <div class="news-meta" style="border: none; padding-top: 0; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px;">
                    <span class="author">BY {{ $news['author'] }}</span>
                    <span class="channel">CAT: {{ $news['channel'] }}</span>
                </div>
                <p style="font-style: italic;">{{ $news['content'] }}</p>
                
                <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <form action="{{ url('/channel/review/' . $news['id']) }}" method="POST">
                        @csrf
                        <div id="feedback-group-{{ $news['id'] }}" style="display: none; margin-bottom: 15px;">
                            <div class="form-group">
                                <label>Feedback Message</label>
                                <textarea name="feedback" class="form-control" rows="2" placeholder="Explain what needs to be changed..."></textarea>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="action" value="modify" class="btn btn-primary" style="flex: 1;">Send Modification Request</button>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;" id="action-buttons-{{ $news['id'] }}">
                            <button type="submit" name="action" value="approve" class="btn btn-primary" style="flex: 1; background: #2E7D32; border-color: #2E7D32;">Approve</button>
                            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="document.getElementById('feedback-group-{{ $news['id'] }}').style.display='block';">Modify</button>
                            <button type="submit" name="action" value="reject" class="btn btn-secondary" style="flex: 1; border-color: var(--secondary-color); color: var(--secondary-color);" onclick="return confirm('Are you sure you want to permanently reject this article?');">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; border: 1px dashed var(--border-color);">
                <p style="font-family: var(--font-serif); color: var(--text-secondary); font-size: 1.2rem;">No pending articles for your channel right now.</p>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('toggleRosterBtn').addEventListener('click', function() {
        var rosterDiv = document.getElementById('rosterList');
        if (rosterDiv.style.display === 'none') {
            rosterDiv.style.display = 'block';
            this.textContent = 'Hide Authors';
        } else {
            rosterDiv.style.display = 'none';
            this.textContent = 'Show All Hired Authors';
        }
    });
</script>
@endpush

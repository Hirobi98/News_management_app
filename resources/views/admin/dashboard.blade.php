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

    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Final Review Queue</h2>

    <div class="news-grid" style="margin-bottom: 60px;">
        @php
            $pendingAdminNews = array_filter($newsList, function($n) {
                return $n['status'] === 'Pending_Admin';
            });
        @endphp

        @forelse($pendingAdminNews as $news)
            @php
                $newsChannel = null;
                $newsAuthor = null;
                foreach($channels as $c) {
                    $cName = $c->name ?? $c->NAME ?? null;
                    if ($cName === $news['target_channel_name']) {
                        $newsChannel = $c;
                        break;
                    }
                }
                foreach($authors as $a) {
                    $aName = $a->name ?? $a->NAME ?? null;
                    if ($aName === $news['author']) {
                        $newsAuthor = $a;
                        break;
                    }
                }
            @endphp
            <div class="news-card" style="border: 2px solid var(--primary-color);">
                <div style="background: rgba(0,0,0,0.03); padding: 15px; margin: -20px -20px 20px -20px; border-bottom: 1px solid var(--border-color);">
                    <p style="margin: 0; font-family: var(--font-sans); font-size: 0.8rem; text-transform: uppercase;">
                        <strong>Channel:</strong> {{ $newsChannel->name ?? $newsChannel->NAME ?? $news['target_channel_name'] }} ({{ $newsChannel->email ?? $newsChannel->EMAIL ?? 'No Email' }})<br>
                        <strong>Author:</strong> {{ $newsAuthor->name ?? $newsAuthor->NAME ?? $news['author'] }} ({{ $newsAuthor->email ?? $newsAuthor->EMAIL ?? 'No Email' }})
                    </p>
                </div>
                
                <h3>{{ $news['title'] }}</h3>
                <p style="font-style: italic;">{{ $news['content'] }}</p>
                
                <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <form action="{{ url('/admin/review/' . $news['id']) }}" method="POST" id="admin-review-form-{{ $news['id'] }}">
                        @csrf
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>Feedback (Required if Rejecting)</label>
                            <textarea name="feedback" class="form-control" rows="2" placeholder="Explain rejection..."></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" name="action" value="approve" class="btn btn-primary" style="flex: 1; background: #2E7D32; border-color: #2E7D32;">Publish Now</button>
                            <button type="submit" name="action" value="reject" class="btn btn-secondary" style="flex: 1; border-color: var(--secondary-color); color: var(--secondary-color);">Reject</button>
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

    <!-- AUDIT LOGS -->
    <h2 style="font-family: var(--font-serif); font-size: 1.5rem; text-transform: uppercase; margin-bottom: 20px;">Database Audit Logs</h2>
    <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); margin-bottom: 40px; font-family: var(--font-sans); font-size: 0.9rem;">
        <ul style="list-style: none; padding: 0;">
        @forelse($auditLogs as $log)
            <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                <strong style="color: var(--primary-color);">[{{ $log->action ?? $log->ACTION }}]</strong> 
                <span style="color: #555; font-size: 0.8rem; margin-left: 10px;">{{ $log->created_at ?? $log->CREATED_AT }}</span><br>
                {{ $log->details ?? $log->DETAILS }}
            </li>
        @empty
            <li style="color: #888; font-style: italic;">No audit logs generated yet. Perform an action to see logs.</li>
        @endforelse
        </ul>
    </div>

    <!-- UNIVERSAL DIRECTORY -->
    <h2 style="font-family: var(--font-serif); font-size: 2rem; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Universal Directory</h2>

    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn btn-primary" onclick="showTab('tab-channels')" id="btn-channels">Show News Channels</button>
        <button class="btn btn-secondary" onclick="showTab('tab-authors')" id="btn-authors">Show Authors</button>
        <button class="btn btn-secondary" onclick="showTab('tab-readers')" id="btn-readers">Show Readers</button>
    </div>

    <!-- Channels Tab -->
    <div id="tab-channels" class="directory-tab" style="display: block;">
        <h3 style="font-family: var(--font-serif);">News Channels & Rosters ({{ count($channels) }})</h3>
        <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color);">
            @foreach($channels as $channel)
                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-color);">
                    <strong>{{ $channel->name ?? $channel->NAME }}</strong> ({{ $channel->email ?? $channel->EMAIL }})<br>
                    @php $cPwd = isset($channel->PASSWORD) ? $channel->PASSWORD : (isset($channel->password) ? $channel->password : 'N/A'); @endphp
                    <span style="font-family: monospace; font-size: 0.8rem; color: #666; background: #eee; padding: 2px 5px; border-radius: 3px;">Password Hash: {{ Str::limit((string)$cPwd, 40) }}</span>
                    <div style="margin-top: 10px; padding-left: 20px;">
                        <em>Hired Authors:</em>
                        @php $hasAuthors = false; @endphp
                        <ul style="margin-top: 5px; font-family: var(--font-sans); font-size: 0.9rem;">
                        @if(isset($channelRosters[$channel->id ?? $channel->ID]))
                            @foreach($channelRosters[$channel->id ?? $channel->ID] as $ra)
                                @php $hasAuthors = true; @endphp
                                <li>{{ $ra->author_name ?? $ra->AUTHOR_NAME }} ({{ $ra->author_email ?? $ra->AUTHOR_EMAIL }})</li>
                            @endforeach
                        @endif
                        @if(!$hasAuthors)
                            <li style="color: var(--text-secondary); font-style: italic;">No authors hired yet.</li>
                        @endif
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Authors Tab -->
    <div id="tab-authors" class="directory-tab" style="display: none;">
        <h3 style="font-family: var(--font-serif);">All Authors ({{ count($authors) }})</h3>
        <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); font-family: var(--font-sans); font-size: 0.9rem;">
            @foreach($authors as $author)
                <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-color);">
                    <strong>{{ $author->name ?? $author->NAME }}</strong> ({{ $author->email ?? $author->EMAIL }})<br>
                    @php $aPwd = isset($author->PASSWORD) ? $author->PASSWORD : (isset($author->password) ? $author->password : 'N/A'); @endphp
                    <span style="font-family: monospace; font-size: 0.8rem; color: #666; background: #eee; padding: 2px 5px; border-radius: 3px;">Password Hash: {{ Str::limit((string)$aPwd, 40) }}</span>
                    <div style="margin-top: 10px; padding-left: 20px;">
                        <em>Connected News Channels:</em>
                        @php $hasChannels = false; @endphp
                        <ul style="margin-top: 5px; font-family: var(--font-sans); font-size: 0.9rem;">
                        @if(isset($authorRosters[$author->id ?? $author->ID]))
                            @foreach($authorRosters[$author->id ?? $author->ID] as $rc)
                                @php $hasChannels = true; @endphp
                                <li>{{ $rc->channel_name ?? $rc->CHANNEL_NAME }}</li>
                            @endforeach
                        @endif
                        @if(!$hasChannels)
                            <li style="color: var(--text-secondary); font-style: italic;">Not connected to any news channel yet.</li>
                        @endif
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Readers Tab -->
    <div id="tab-readers" class="directory-tab" style="display: none;">
        <h3 style="font-family: var(--font-serif);">All Readers ({{ count($readers) }})</h3>
        <div style="background: var(--card-bg); padding: 20px; border: 1px solid var(--border-color); font-family: var(--font-sans); font-size: 0.9rem;">
            <ul style="list-style: none; padding: 0;">
            @foreach($readers as $reader)
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                    <strong>{{ $reader->name ?? $reader->NAME }}</strong> ({{ $reader->email ?? $reader->EMAIL }})<br>
                    @php $rPwd = isset($reader->PASSWORD) ? $reader->PASSWORD : (isset($reader->password) ? $reader->password : 'N/A'); @endphp
                    <span style="font-family: monospace; font-size: 0.8rem; color: #666; background: #eee; padding: 2px 5px; border-radius: 3px;">Password Hash: {{ Str::limit((string)$rPwd, 40) }}</span>
                </li>
            @endforeach
            </ul>
        </div>
    </div>

</div>

<script>
function showTab(tabId) {
    document.querySelectorAll('.directory-tab').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.btn').forEach(el => {
        el.classList.remove('btn-primary');
        el.classList.add('btn-secondary');
    });
    
    document.getElementById(tabId).style.display = 'block';
    
    if(tabId === 'tab-channels') {
        document.getElementById('btn-channels').classList.replace('btn-secondary', 'btn-primary');
    } else if(tabId === 'tab-authors') {
        document.getElementById('btn-authors').classList.replace('btn-secondary', 'btn-primary');
    } else if(tabId === 'tab-readers') {
        document.getElementById('btn-readers').classList.replace('btn-secondary', 'btn-primary');
    }
}
</script>
@endsection

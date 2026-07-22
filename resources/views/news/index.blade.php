@extends('layouts.app')

@section('content')
<div>
    <!-- Composer moved to Author Dashboard -->

    @if(isset($currentCategory) && strtolower($currentCategory) === 'world')
        <!-- International News Section -->
        <div style="margin-bottom: 40px; padding: 25px; background: linear-gradient(135deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.05);">
            <h2 style="font-family: var(--font-serif); font-size: 1.8rem; text-transform: uppercase; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; color: var(--text-main);">Live International News</h2>
            <div id="intl-news-container" style="display: flex; flex-direction: column; gap: 12px;">
                <p style="color: var(--text-secondary); font-style: italic;">Fetching latest global headlines...</p>
            </div>
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

    <div class="news-feed" style="max-width: 680px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">
        @forelse($newsList as $news)
            <div class="news-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; display: flex; flex-direction: {{ !empty($news['image']) ? 'row' : 'column' }}; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.3s ease, box-shadow 0.3s ease; padding: 0;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.04)';">
                
                @if(!empty($news['image']))
                    <div style="width: 130px; min-width: 130px; height: 130px; margin: 15px 0 15px 15px; border-radius: 16px; overflow: hidden; flex-shrink: 0; background: #e5e5e5;">
                        <a href="{{ url('/news/' . $news['id']) }}" style="display: block; width: 100%; height: 100%;">
                            <img src="{{ asset($news['image']) }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </a>
                    </div>
                @endif
                
                <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; width: 100%;">
                    <div style="font-family: var(--font-sans); color: var(--secondary-color); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                        {{ $news['target_channel_name'] }}
                    </div>
                    
                    <h3 style="font-family: var(--font-serif); font-size: 1.35rem; margin: 0 0 8px 0; line-height: 1.3;">
                        <a href="{{ url('/news/' . $news['id']) }}" style="color: var(--text-main); text-decoration: none;">{{ $news['title'] }}</a>
                    </h3>
                    
                    <p style="font-family: var(--font-sans); color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem; margin: 0 0 15px 0;">
                        {{ Str::limit($news['content'], 90) }}
                    </p>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 10px;">
                        <div style="font-family: var(--font-sans); font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 15px;">
                            <span style="display: flex; align-items: center; gap: 5px;">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($news['author']) }}&background=random&size=24&rounded=true" style="width: 20px; height: 20px; border-radius: 50%;"> 
                                {{ $news['author'] }}
                            </span>
                            <span style="display: flex; align-items: center; gap: 5px;"><i class="fa-regular fa-clock"></i> {{ explode(',', $news['date'])[0] ?? $news['date'] }}</span>
                        </div>
                        <div style="display: flex; gap: 12px;">
                            <a href="{{ url('/news/' . $news['id']) }}" style="color: var(--text-secondary); font-size: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-secondary)'"><i class="fa-regular fa-bookmark"></i></a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($news['title']) }}&url={{ urlencode(url('/news/' . $news['id'])) }}" target="_blank" style="color: var(--text-secondary); font-size: 1rem; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-secondary)'"><i class="fa-solid fa-share-nodes"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding: 40px; text-align: center; border: 1px dashed var(--border-color); border-radius: 8px;">
                <h2 style="font-family: var(--font-serif); color: var(--text-secondary);">No dispatches available at the moment.</h2>
            </div>
        @endforelse
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const container = document.getElementById('intl-news-container');
        if (!container) return; // Only run if the container exists (i.e., we are in the World section)
        
        try {
            const response = await fetch('/api/live-news');
            const data = await response.json();
            
            if (data.status === 'ok' && data.articles.length > 0) {
                container.innerHTML = ''; 
                data.articles.forEach(article => {
                    const articleDiv = document.createElement('div');
                    articleDiv.style = "padding: 12px 15px; border-left: 3px solid var(--secondary-color); background: rgba(255,255,255,0.3); border-radius: 0 8px 8px 0; transition: background 0.3s;";
                    articleDiv.onmouseover = function() { this.style.background = 'rgba(255,255,255,0.6)'; };
                    articleDiv.onmouseout = function() { this.style.background = 'rgba(255,255,255,0.3)'; };
                    articleDiv.innerHTML = `
                        <h4 style="margin: 0 0 5px 0; font-family: var(--font-sans); font-size: 1.1rem;">
                            <a href="${article.url}" target="_blank" style="color: var(--text-main); text-decoration: none;">${article.title}</a>
                        </h4>
                        <small style="color: var(--text-secondary); font-weight: bold; text-transform: uppercase; font-size: 0.75rem;">Source: ${article.source}</small>
                    `;
                    container.appendChild(articleDiv);
                });
            } else {
                container.innerHTML = '<p style="color: var(--text-secondary);">No international news available at the moment.</p>';
            }
        } catch (error) {
            container.innerHTML = '<p style="color: #A94438;">Network failure while connecting to Live News API.</p>';
        }
    });
</script>
@endsection

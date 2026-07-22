<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'The Gazette - Vintage News Reader')</title>
    <meta name="description" content="Stay updated with the latest news on The Gazette.">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    @stack('styles')
</head>
<body>

    <div class="container">
        <nav>
            <div style="font-family: var(--font-sans); font-size: 0.8rem; letter-spacing: 2px; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase;">
                Vol. CXXIV — {{ date('l, F j, Y') }}
            </div>
            <a href="{{ url('/') }}" class="nav-brand">The Gazette</a>
            <div class="nav-links">
                <a href="{{ url('/home') }}">Front Page</a>
                <a href="{{ url('/category/world') }}">World</a>
                <a href="{{ url('/category/opinion') }}">Opinion</a>
                <a href="{{ url('/category/culture') }}">Culture</a>
                
                <form id="categorySearchForm" class="flashy-search-form" onsubmit="event.preventDefault(); const cat = document.getElementById('catSearch').value.trim(); if(cat) window.location.href='{{ url('/category') }}/'+encodeURIComponent(cat.toLowerCase());">
                    <input type="text" id="catSearch" class="flashy-search-input" placeholder="Search category...">
                    <button type="submit" class="flashy-search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                @if(Auth::check())
                    @if(strtolower(Auth::user()->role) === 'author' || strtolower(Auth::user()->role) === 'both')
                        @php
                            $unreadMsgCount = \Illuminate\Support\Facades\DB::table('inbox_messages')
                                ->where('user_id', Auth::id())
                                ->where('is_read', 0)
                                ->count();
                        @endphp
                        <a href="{{ url('/author/dashboard') }}">
                            Inbox
                            @if($unreadMsgCount > 0)
                                <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; vertical-align: super; font-family: sans-serif; font-weight: bold; margin-left: 2px;">{{ $unreadMsgCount }}</span>
                            @endif
                        </a>
                    @elseif(strtolower(Auth::user()->role) === 'channel')
                        <a href="{{ url('/channel/dashboard') }}">Dashboard</a>
                    @elseif(strtolower(Auth::user()->role) === 'admin')
                        <a href="{{ url('/admin/dashboard') }}">Dashboard</a>
                    @endif
                    <a href="{{ url('/profile') }}">Account</a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                    </form>
                @else
                    <a href="{{ url('/login') }}">Login</a>
                    <a href="{{ url('/register') }}">Subscribe</a>
                @endif
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <main>
            @yield('content')
        </main>
        
        <footer class="text-center mt-4 mb-4" style="color: var(--text-secondary); padding-top: 40px; border-top: 1px solid var(--border-color); font-family: var(--font-sans); font-size: 0.85rem; text-transform: uppercase;">
            <p>&copy; {{ date('Y') }} The Gazette. All rights reserved.</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NewsApp - Premium News Reader')</title>
    <meta name="description" content="Stay updated with the latest news on NewsApp. Read, write and share.">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

    <div class="container">
        <header class="masthead">
            <h1 class="masthead-title"><a href="{{ url('/') }}" style="color: inherit; text-decoration: none;">THE <span>GAZETTE</span></a></h1>
            <p style="font-family: var(--font-serif); font-style: italic; font-size: 0.95rem; letter-spacing: 2px; text-transform: uppercase; margin: 8px 0 0; color: var(--text-secondary);">Delivering Diligent News & Dispatches</p>
            <div class="masthead-meta">
                <span>Vol. CCLVI &bull; No. 12</span>
                <span>{{ date('F j, Y') }}</span>
                <span>Khulna &bull; Bangladesh</span>
            </div>
        </header>

        <nav>
            <div style="display: flex; width: 100%; justify-content: space-between; align-items: center;">
                <a href="{{ url('/') }}" class="nav-brand" style="text-decoration: none;">THE <span>GAZETTE</span></a>
                <div class="nav-links">
                    @if(session('user_logged_in'))
                        <a href="{{ url('/home') }}">News Feed</a>
                        @if(session('user_role') === 'author' || session('user_role') === 'both')
                            <a href="{{ url('/news/create') }}">Write News</a>
                        @endif
                        <a href="{{ url('/profile') }}">Profile</a>
                        <a href="{{ url('/logout') }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.75rem;">Logout</a>
                    @else
                        <a href="{{ url('/') }}">Front Page</a>
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.75rem;">Sign Up</a>
                    @endif
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success glass">
                {{ session('success') }}
            </div>
        @endif

        <main>
            @yield('content')
        </main>
        
        <footer class="text-center mt-4 mb-4" style="color: var(--text-secondary); padding-top: 40px; border-top: 1px solid var(--glass-border);">
            <p>&copy; {{ date('Y') }} NewsApp. All rights reserved.</p>
        </footer>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>

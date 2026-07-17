<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NewsApp - Premium News Reader')</title>
    <meta name="description" content="Stay updated with the latest news on NewsApp. Read, write and share.">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    @stack('styles')
</head>
<body>

    <div class="container">
        <nav class="glass" style="margin-top: 20px; padding: 15px 30px;">
            <a href="{{ url('/') }}" class="nav-brand">NewsApp</a>
            <div class="nav-links">
                @if(session('user_logged_in'))
                    <a href="{{ url('/home') }}">News Feed</a>
                    @if(session('user_role') === 'author' || session('user_role') === 'both')
                        <a href="{{ url('/news/create') }}">Write News</a>
                    @endif
                    <a href="{{ url('/profile') }}">Profile</a>
                    <a href="{{ url('/logout') }}" class="btn btn-secondary">Logout</a>
                @else
                    <a href="{{ url('/login') }}">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary">Sign Up</a>
                @endif
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
</body>
</html>

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view your profile.');
        }

        // Mock profile data
        $user = [
            'name' => session('user_name', 'Demo User'),
            'email' => session('user_email', 'demo@example.com'),
            'role' => session('user_role', 'reader'),
            'joined' => 'July 2026',
            'saved_articles' => 3,
            'published_articles' => (session('user_role') === 'author' || session('user_role') === 'both') ? 5 : 0,
        ];

        return view('profile.index', compact('user'));
    }
}

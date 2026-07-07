<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Mock authentication
        session([
            'user_logged_in' => true,
            'user_name' => 'Demo User',
            'user_email' => $request->input('email', 'demo@example.com'),
            'user_role' => 'reader' // Default for mock login
        ]);

        return redirect('/home')->with('success', 'Successfully logged in!');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Mock registration
        session([
            'user_logged_in' => true,
            'user_name' => $request->input('name', 'New User'),
            'user_email' => $request->input('email', 'new@example.com'),
            'user_role' => $request->input('role', 'reader') // reader, author, or both
        ]);

        return redirect('/home')->with('success', 'Account created successfully!');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}

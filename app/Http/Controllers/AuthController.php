<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function showLogin()
    {
        // Admin auto-seed shoriyeneoa hoyeche jeno default login page-e crash na kore
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Oracle USERS table theke uppercase identity dhore user fetch kora hochhe
        $user = DB::table('USERS')
            ->where(DB::raw('LOWER("EMAIL")'), strtolower($request->email))
            ->get()
            ->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email signature or security password.'])->withInput();
        }

        // Session variable establish kora hochhe UPPERCASE object property diye
        session([
            'user_id' => $user->id,
            'user_logged_in' => true,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        $roleLower = strtolower($user->role);
        if ($roleLower === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Successfully authenticated as Administrator.');
        } elseif ($roleLower === 'channel') {
            return redirect('/channel/dashboard')->with('success', 'Successfully logged in to Channel Dashboard.');
        }
        
        return redirect('/home')->with('success', 'Successfully logged in. Welcome to the News Feed.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        // Email description check korar jonno query
        $existing = DB::table('USERS')
            ->where(DB::raw('LOWER("EMAIL")'), strtolower($request->email))
            ->get()
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'This email signature is already registered in our archives.'])->withInput();
        }

        // Registration form er role mapping
        $roleMap = [
            'reader' => 'Reader',
            'author' => 'Author',
            'both' => 'Both',
            'channel' => 'Channel',
        ];
        $role = $roleMap[$request->role] ?? 'Reader';

        // Oracle case-mismatch rodh korte UPPERCASE key-te input kora hochhe
        DB::statement(
            'INSERT INTO USERS
    (NAME, EMAIL, PASSWORD, ROLE, BIO, INTEREST, PROFILE_PICTURE)
    VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $request->name,
                $request->email,
                Hash::make($request->password),
                $role,
                null,
                null,
                null,
            ]
        );

        return redirect('/login')->with('success', 'Registration successful. Please login with your new account.');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Safely logged out of archives.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        dd($user);

        if (!$user || !password_verify($request->password, $user->PASSWORD)) {
            return back()->withErrors(['email' => 'Invalid email signature or security password.'])->withInput();
        }

        // Session variable establish kora hochhe UPPERCASE object property diye
        session([
            'user_id' => $user->ID,
            'user_logged_in' => true,
            'user_name' => $user->NAME,
            'user_email' => $user->EMAIL,
            'user_role' => $user->ROLE
        ]);

        // Role-er upor vitti kore dashboard-e redirect kora hochhe
        $role = strtolower($user->ROLE);
        if ($role === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Successfully authenticated as Administrator.');
        } elseif ($role === 'author' || $role === 'both') {
            return redirect('/author/dashboard')->with('success', 'Successfully logged in to Author Dashboard.');
        } else {
            return redirect('/home')->with('success', 'Welcome back to the Gazette News Feed.');
        }
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
            'both' => 'Author',
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
                password_hash($request->password, PASSWORD_BCRYPT),
                $role,
                null,
                null,
                null,
            ]
        );


        // Shaddho-shristi (Newly created) user session set korar jonno database checking
        $user = DB::table('USERS')
            ->where(DB::raw('LOWER("EMAIL")'), strtolower($request->email))
            ->get()
            ->first();
        dd($user);

        session([
            'user_id' => $user->ID,
            'user_logged_in' => true,
            'user_name' => $user->NAME,
            'user_email' => $user->EMAIL,
            'user_role' => $user->ROLE
        ]);

        // Reader ebong Author dashboard er redirect check
        $roleLower = strtolower($user->ROLE);
        if ($roleLower === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Admin profile generated successfully.');
        } elseif ($roleLower === 'author' || $roleLower === 'both') {
            return redirect('/author/dashboard')->with('success', 'Author portfolio created successfully.');
        } else {
            return redirect('/home')->with('success', 'Contributor profile created successfully.');
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Safely logged out of archives.');
    }
}
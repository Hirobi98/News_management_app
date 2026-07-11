<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Auto-seed default Admin if none exists to facilitate testing
        $adminExists = DB::table('USERS')
            ->where(DB::raw('LOWER(role)'), 'admin')
            ->exists();

        if (!$adminExists) {
            DB::table('USERS')->insert([
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'Admin',
                'bio' => 'Lead administrator of The Gazette.',
                'interest' => 'Operations',
                'profile_picture' => null
            ]);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Fetch user from Oracle USERS table
        $user = DB::table('USERS')
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
            ->get()
            ->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email signature or security password.'])->withInput();
        }

        // Establish session variables
        session([
            'user_id' => $user->id,
            'user_logged_in' => true,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        // Redirect based on role
        $role = strtolower($user->role);
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

        // Check if email signature already exists in Oracle USERS table
        $existing = DB::table('USERS')
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
            ->get()
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'This email signature is already registered in our archives.'])->withInput();
        }

        // Map registration role option to database string
        $roleMap = [
            'reader' => 'Reader',
            'author' => 'Author',
            'both' => 'Author',
        ];
        $role = $roleMap[$request->role] ?? 'Reader';

        // Insert using sequence/trigger auto-increment (AUTHORS_TRG)
        DB::table('USERS')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'role' => $role,
            'bio' => null,
            'interest' => null,
            'profile_picture' => null
        ]);

        // Retrieve newly created user to verify session ID
        $user = DB::table('USERS')
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
            ->get()
            ->first();

        session([
            'user_id' => $user->id,
            'user_logged_in' => true,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        // Redirect appropriately
        $roleLower = strtolower($user->role);
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

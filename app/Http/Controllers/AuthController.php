<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function socialRedirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function socialCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName() ?? 'Social User',
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'Reader'
                ]
            );

            Auth::login($user);
            return redirect('/home');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Unable to login via ' . ucfirst($provider)]);
        }
    }

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
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
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
        
        // Prevent Super Admin from using standard login
        if ($roleLower === 'admin') {
            return redirect('/login')->with('error', 'Admins must use the secure portal.');
        }

        if ($roleLower === 'channel') {
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
            'password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'role' => 'required|string'
        ]);

        // Email description check korar jonno query
        $existing = DB::table('USERS')
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
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
        $maxId = DB::table('users')->max('id') ?? 0;
        DB::table('users')->insert([
            'id' => $maxId + 1,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'bio' => null,
            'interest' => null,
            'profile_picture' => null
        ]);

        return redirect('/login')->with('success', 'Registration successful. Please login with your new account.');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Safely logged out of archives.');
    }

    public function showSuperAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function superAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = DB::table('USERS')
            ->where(DB::raw('LOWER(email)'), strtolower($request->email))
            ->get()
            ->first();

        if (!$user || !Hash::check($request->password, $user->password) || strtolower($user->role) !== 'admin') {
            return back()->withErrors(['email' => 'Invalid admin credentials.'])->withInput();
        }

        session([
            'user_id' => $user->id,
            'user_logged_in' => true,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        return redirect('/admin/dashboard')->with('success', 'Successfully authenticated as Super Administrator.');
    }
}
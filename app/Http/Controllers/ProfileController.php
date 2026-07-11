<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session('user_logged_in')) {
            return redirect('/login')->with('error', 'Please login to view your profile.');
        }

        // Fetch user information from Oracle USERS table
        $userRow = DB::table('USERS')
            ->where('id', session('user_id'))
            ->first();

        if (!$userRow) {
            return redirect('/home')->with('error', 'User profile not found in archives.');
        }

        // Map user profile fields
        $user = [
            'id' => $userRow->id,
            'name' => $userRow->name,
            'email' => $userRow->email,
            'role' => $userRow->role,
            'bio' => $userRow->bio ?? 'No biography details provided.',
            'interest' => $userRow->interest ?? 'None specified',
            'profile_picture' => $userRow->profile_picture ?? null,
            'joined' => 'Gazette Archivist'
        ];

        // Call Oracle stored function count_category_articles to display live database stats on the profile page
        $stats = [
            'Technology' => DB::selectOne("SELECT count_category_articles('Technology') AS cnt FROM dual")->cnt ?? 0,
            'Finance' => DB::selectOne("SELECT count_category_articles('Finance') AS cnt FROM dual")->cnt ?? 0,
            'Politics' => DB::selectOne("SELECT count_category_articles('Politics') AS cnt FROM dual")->cnt ?? 0,
            'Sports' => DB::selectOne("SELECT count_category_articles('Sports') AS cnt FROM dual")->cnt ?? 0,
            'Entertainment' => DB::selectOne("SELECT count_category_articles('Entertainment') AS cnt FROM dual")->cnt ?? 0,
        ];

        return view('profile.index', compact('user', 'stats'));
    }
}

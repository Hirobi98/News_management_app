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

        // Fetch user information from Oracle USERS table without LIMIT to avoid ORA-00933
        $userRows = DB::select("SELECT * FROM USERS WHERE ID = ?", [session('user_id')]);
        $userRow = $userRows[0] ?? null;

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

    public function update(Request $request)
    {
        if (!session('user_logged_in')) {
            return redirect('/login');
        }

        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'interest' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = session('user_id');
        $bio = $request->input('bio');
        $interest = $request->input('interest');
        
        $profilePictureName = null;

        // Check if an existing picture exists to keep it if no new one is uploaded
        $userRows = DB::select("SELECT PROFILE_PICTURE FROM USERS WHERE ID = ?", [$userId]);
        $existingPicture = $userRows[0]->profile_picture ?? ($userRows[0]->PROFILE_PICTURE ?? null);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profilePictureName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $profilePictureName);
        } else {
            $profilePictureName = $existingPicture;
        }

        DB::statement(
            "UPDATE USERS SET BIO = ?, INTEREST = ?, PROFILE_PICTURE = ? WHERE ID = ?",
            [$bio, $interest, $profilePictureName, $userId]
        );

        return redirect('/profile')->with('success', 'Profile updated successfully.');
    }
}

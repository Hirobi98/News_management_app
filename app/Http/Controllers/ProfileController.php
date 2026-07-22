<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $userRow = Auth::user();

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

        // Standard Laravel count logic
        $stats = [
            'Technology' => DB::table('news_items')->where('category', 'Technology')->count(),
            'Finance' => DB::table('news_items')->where('category', 'Finance')->count(),
            'Politics' => DB::table('news_items')->where('category', 'Politics')->count(),
            'Sports' => DB::table('news_items')->where('category', 'Sports')->count(),
            'Entertainment' => DB::table('news_items')->where('category', 'Entertainment')->count(),
        ];

        return view('profile.index', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'interest' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $user->bio = $request->input('bio');
        $user->interest = $request->input('interest');
        
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profilePictureName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $profilePictureName);
            $user->profile_picture = $profilePictureName;
        }

        $user->save();

        return redirect('/profile')->with('success', 'Profile updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = $request->user()->profile ?: Profile::create(['user_id' => (string) $request->user()->getKey()]);

        return view('profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:600'],
            'location' => ['nullable', 'string', 'max:120'],
            'website' => ['nullable', 'url', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            $request->user()->update(['avatar' => $data['avatar']]);
        }

        Profile::updateOrCreate(['user_id' => (string) $request->user()->getKey()], $data);
        $request->user()->update(['profile_complete' => true, 'phone' => $data['phone'] ?? $request->user()->phone]);

        return back()->with('success', 'Profile updated.');
    }
}

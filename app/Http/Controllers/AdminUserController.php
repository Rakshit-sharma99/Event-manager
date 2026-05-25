<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($nested) => $nested
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone_number', 'like', "%{$q}%"));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $roleCounts = [
            'all' => User::count(),
            'planner' => User::where('role', 'planner')->count(),
            'vendor' => User::where('role', 'vendor')->count(),
            'guest' => User::where('role', 'guest')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function suspend(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        abort_if($user->role === 'admin', 403, 'Cannot suspend admin users.');

        $user->update(['is_suspended' => true]);

        AuditLog::log('user_suspended', 'user', $id, [
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return back()->with('success', "User '{$user->name}' has been suspended.");
    }

    public function ban(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        abort_if($user->role === 'admin', 403, 'Cannot ban admin users.');

        $user->update(['is_banned' => true]);

        AuditLog::log('user_banned', 'user', $id, [
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return back()->with('success', "User '{$user->name}' has been banned.");
    }

    public function activate(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => false, 'is_banned' => false]);

        AuditLog::log('user_activated', 'user', $id, [
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return back()->with('success', "User '{$user->name}' has been activated.");
    }
}

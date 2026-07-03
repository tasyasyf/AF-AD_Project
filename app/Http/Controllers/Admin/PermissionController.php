<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ViewPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', '!=', 'admin')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.permissions.index', compact('users'));
    }

    public function edit(User $user): View
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts already have full access.');

        $catalog = ViewPermissions::catalog();
        $granted = $user->grantedPermissions();

        return view('admin.permissions.edit', compact('user', 'catalog', 'granted'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $validated = $request->validate([
            'additional_access_enabled' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', ViewPermissions::keys())],
        ]);

        // Keep only valid, de-duplicated keys.
        $permissions = array_values(array_intersect(
            ViewPermissions::keys(),
            $validated['permissions'] ?? []
        ));

        $user->update([
            'permissions' => $permissions,
            'additional_access_enabled' => $request->boolean('additional_access_enabled'),
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Additional access updated for {$user->name}.");
    }
}

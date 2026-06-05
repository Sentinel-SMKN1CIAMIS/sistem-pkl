<?php

namespace App\Http\Controllers\Admin;

use App\Models\PokjaGroup;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PokjaGroupController extends Controller
{
    /**
     * Display a listing of Pokja groups.
     */
    public function index()
    {
        $groups = PokjaGroup::with('users')->paginate(15);
        return view('admin.pokja-groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new Pokja group.
     */
    public function create()
    {
        $pokjaUsers = User::where('role', 'pokja')
            ->where('is_active', true)
            ->get();
        return view('admin.pokja-groups.create', compact('pokjaUsers'));
    }

    /**
     * Store a newly created Pokja group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:pokja_groups|max:255',
            'description' => 'nullable|string|max:1000',
            'members' => 'required|array|min:2|max:4',
            'members.*' => 'required|integer|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Verify all members are pokja users
        $memberIds = $validated['members'];
        $pokjaUsers = User::where('role', 'pokja')
            ->whereIn('id', $memberIds)
            ->count();

        if ($pokjaUsers !== count($memberIds)) {
            return back()->withErrors(['members' => 'Semua anggota harus memiliki role Pokja.']);
        }

        // Check if any user is already in another active group
        $existingMembers = User::whereIn('id', $memberIds)
            ->with('pokjaGroups')
            ->get();

        foreach ($existingMembers as $user) {
            if ($user->pokjaGroups()->where('is_active', true)->exists()) {
                return back()->withErrors(['members' => "User {$user->name} sudah menjadi bagian dari grup Pokja lain yang aktif."]);
            }
        }

        // Create the group
        $group = PokjaGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Attach members
        $group->users()->attach($memberIds);

        return redirect()->route('admin.pokja-groups.show', $group)
            ->with('success', "Grup Pokja '{$group->name}' berhasil dibuat dengan {$group->users()->count()} anggota.");
    }

    /**
     * Display the specified Pokja group.
     */
    public function show(PokjaGroup $pokjaGroup)
    {
        $group = $pokjaGroup->load('users');
        return view('admin.pokja-groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified Pokja group.
     */
    public function edit(PokjaGroup $pokjaGroup)
    {
        $group = $pokjaGroup->load('users');
        $pokjaUsers = User::where('role', 'pokja')
            ->where('is_active', true)
            ->get();
        return view('admin.pokja-groups.edit', compact('group', 'pokjaUsers'));
    }

    /**
     * Update the specified Pokja group in storage.
     */
    public function update(Request $request, PokjaGroup $pokjaGroup)
    {
        $validated = $request->validate([
            'name' => "required|string|unique:pokja_groups,name,{$pokjaGroup->id}|max:255",
            'description' => 'nullable|string|max:1000',
            'members' => 'required|array|min:2|max:4',
            'members.*' => 'required|integer|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $memberIds = $validated['members'];
        
        // Verify all members are pokja users
        $pokjaUsers = User::where('role', 'pokja')
            ->whereIn('id', $memberIds)
            ->count();

        if ($pokjaUsers !== count($memberIds)) {
            return back()->withErrors(['members' => 'Semua anggota harus memiliki role Pokja.']);
        }

        // Check if any NEW user is already in another active group
        $newMembers = array_diff($memberIds, $pokjaGroup->users()->pluck('id')->toArray());
        if (!empty($newMembers)) {
            $existingMembers = User::whereIn('id', $newMembers)
                ->with('pokjaGroups')
                ->get();

            foreach ($existingMembers as $user) {
                if ($user->pokjaGroups()->where('is_active', true)->exists()) {
                    return back()->withErrors(['members' => "User {$user->name} sudah menjadi bagian dari grup Pokja lain yang aktif."]);
                }
            }
        }

        // Update the group
        $pokjaGroup->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync members
        $pokjaGroup->users()->sync($memberIds);

        return redirect()->route('admin.pokja-groups.show', $pokjaGroup)
            ->with('success', "Grup Pokja '{$pokjaGroup->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified Pokja group from storage.
     */
    public function destroy(PokjaGroup $pokjaGroup)
    {
        $groupName = $pokjaGroup->name;
        $pokjaGroup->delete();

        return redirect()->route('admin.pokja-groups.index')
            ->with('success', "Grup Pokja '{$groupName}' berhasil dihapus.");
    }

    /**
     * Add a member to an existing group
     */
    public function addMember(Request $request, PokjaGroup $pokjaGroup)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Verify user is pokja
        if ($user->role !== 'pokja') {
            return back()->withErrors(['user_id' => 'User harus memiliki role Pokja.']);
        }

        // Check user is not in another active group
        if ($user->pokjaGroups()->where('is_active', true)->exists()) {
            return back()->withErrors(['user_id' => "User {$user->name} sudah menjadi bagian dari grup Pokja lain yang aktif."]);
        }

        // Check group size (max 4)
        if ($pokjaGroup->users()->count() >= 4) {
            return back()->withErrors(['user_id' => 'Grup Pokja sudah penuh (maksimal 4 anggota).']);
        }

        // Check user not already in group
        if ($pokjaGroup->hasMemberId($validated['user_id'])) {
            return back()->withErrors(['user_id' => 'User sudah menjadi anggota grup ini.']);
        }

        $pokjaGroup->users()->attach($validated['user_id']);

        return back()->with('success', "User {$user->name} berhasil ditambahkan ke grup.");
    }

    /**
     * Remove a member from group
     */
    public function removeMember(Request $request, PokjaGroup $pokjaGroup)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (!$pokjaGroup->hasMemberId($validated['user_id'])) {
            return back()->withErrors(['user_id' => 'User bukan anggota grup ini.']);
        }

        // Don't allow removing member if group would become smaller than 2
        if ($pokjaGroup->users()->count() <= 2) {
            return back()->withErrors(['user_id' => 'Grup Pokja minimal harus memiliki 2 anggota.']);
        }

        $pokjaGroup->users()->detach($validated['user_id']);

        return back()->with('success', "User {$user->name} berhasil dihapus dari grup.");
    }
}

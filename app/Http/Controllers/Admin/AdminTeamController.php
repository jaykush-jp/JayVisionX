<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTeamController extends Controller
{
    private const MESSAGES = [
        'photo.max'      => 'Photo ka size bahut bada hai (max 3 MB). Chhoti image use karein.',
        'photo.image'    => 'File ek valid image honi chahiye (jpg, png, webp).',
        'photo.uploaded' => 'Photo upload nahi ho payi — size 3 MB se bada hai.',
    ];

    public function index()
    {
        return view('admin.team.index', [
            'members' => TeamMember::orderBy('sort_order')->orderByDesc('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }
        TeamMember::create($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member added.');
    }

    public function edit(TeamMember $team)
    {
        return view('admin.team.edit', ['member' => $team]);
    }

    public function update(Request $request, TeamMember $team)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('photo')) {
            if ($team->photo) {
                Storage::disk('public')->delete($team->photo);
            }
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }
        $team->update($data);

        return redirect()->route('admin.team.index')->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $team)
    {
        if ($team->photo) {
            Storage::disk('public')->delete($team->photo);
        }
        $team->delete();

        return redirect()->route('admin.team.index')->with('success', 'Team member deleted.');
    }

    public function toggleStatus($id)
    {
        $member = TeamMember::findOrFail($id);
        $member->update(['is_active' => ! $member->is_active]);

        return response()->json(['status' => $member->is_active]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name'          => 'required|string|max:120',
            'designation'   => 'nullable|string|max:120',
            'role'          => 'nullable|string|max:255',
            'linkedin_url'  => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'sort_order'    => 'nullable|integer|min:0',
            'photo'         => 'nullable|image|max:3072',
        ], self::MESSAGES);

        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['photo']); // handled separately

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberGroup;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('group')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($groupId = $request->input('group_id')) {
            $query->where('group_id', $groupId);
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $members = $query->paginate(20)->withQueryString();
        $groups = MemberGroup::all();

        $stats = [
            'total' => Member::count(),
            'active' => Member::where('is_active', true)->count(),
            'new_this_month' => Member::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.members.index', compact('members', 'groups', 'stats'));
    }

    public function create()
    {
        $groups = MemberGroup::where('is_active', true)->get();

        return view('admin.members.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'group_id' => 'nullable|exists:member_groups,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Member::create($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Üye oluşturuldu.');
    }

    public function edit(Member $member)
    {
        $groups = MemberGroup::where('is_active', true)->get();

        return view('admin.members.edit', compact('member', 'groups'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'group_id' => 'nullable|exists:member_groups,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $member->update($validated);

        return redirect()->route('admin.members.index')
            ->with('success', 'Üye güncellendi.');
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Üye silindi.');
    }

    public function toggleActive(Member $member)
    {
        $member->update(['is_active' => ! $member->is_active]);

        return back()->with('success', 'Üye durumu güncellendi.');
    }
}

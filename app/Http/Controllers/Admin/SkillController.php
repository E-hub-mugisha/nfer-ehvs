<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(Request $request): View
    {
        $query = Skill::withCount('employees');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $skills = $query->orderBy('name')->paginate(30)->appends($request->query());

        $categories = Skill::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.skills.index', compact('skills', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100|unique:skills,name',
            'category' => 'nullable|string|max:80',
        ]);

        Skill::create($data);

        return back()->with('success', "Skill '{$data['name']}' added successfully.");
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100|unique:skills,name,' . $skill->id,
            'category' => 'nullable|string|max:80',
        ]);

        $skill->update($data);

        return back()->with('success', "Skill updated successfully.");
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        // Detach from employees first
        $skill->employees()->detach();
        $skill->delete();

        return back()->with('success', "Skill '{$skill->name}' deleted.");
    }
}
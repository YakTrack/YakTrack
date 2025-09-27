<?php

namespace App\Http\Controllers;

use App\Models\SessionCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SessionCategoryController extends Controller
{
    public function index(): Response
    {
        $sessionCategories = SessionCategory::withCount('sessions')
            ->orderBy('name')
            ->get();

        return Inertia::render('SessionCategory/Index', [
            'sessionCategories' => $sessionCategories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SessionCategory/Edit');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:session_categories,name',
            'description' => 'nullable|string',
        ]);

        $sessionCategory = SessionCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('session-category.index')
            ->with('success', "Session category '{$sessionCategory->name}' created successfully");
    }

    public function show(SessionCategory $sessionCategory): Response
    {
        $sessionCategory->loadCount('sessions');

        return Inertia::render('SessionCategory/Show', [
            'sessionCategory' => $sessionCategory,
        ]);
    }

    public function edit(SessionCategory $sessionCategory): Response
    {
        return Inertia::render('SessionCategory/Edit', [
            'sessionCategory' => $sessionCategory,
        ]);
    }

    public function update(Request $request, SessionCategory $sessionCategory): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:session_categories,name,'.$sessionCategory->id,
            'description' => 'nullable|string',
        ]);

        $sessionCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('session-category.index')
            ->with('success', "Session category '{$sessionCategory->name}' updated successfully");
    }

    public function destroy(SessionCategory $sessionCategory): RedirectResponse
    {
        $sessionCount = $sessionCategory->sessions()->count();

        if ($sessionCount > 0) {
            return redirect()
                ->route('session-category.index')
                ->with('error', "Cannot delete session category '{$sessionCategory->name}' because it has {$sessionCount} associated session(s). Please reassign or remove the sessions first.");
        }

        $name = $sessionCategory->name;
        $sessionCategory->delete();

        return redirect()
            ->route('session-category.index')
            ->with('success', "Session category '{$name}' deleted successfully");
    }
}

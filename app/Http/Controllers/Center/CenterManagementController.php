<?php

namespace App\Http\Controllers\Center;

use App\ExaminationHub\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CenterManagementController — CRUD for examination centers on central.
 *
 * Only available on central instances (APP_ROLE=central).
 */
class CenterManagementController
{
    /**
     * List all centers with search and filtering.
     */
    public function index(Request $request)
    {
        $query = Center::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $centers = $query->orderBy('name')->paginate(20);

        return view('center-sync.centers.index', compact('centers'));
    }

    /**
     * Show the create center form.
     */
    public function create()
    {
        return view('center-sync.centers.create');
    }

    /**
     * Store a new center.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:general_exam_centers,code',
            'is_active' => 'boolean',
        ]);

        $token = Center::generateToken();

        $center = Center::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'token_hash' => Center::hashToken($token),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Store the plain token for one-time display
        session()->flash('center_token', $token);
        session()->flash('center_id', $center->id);

        return redirect()
            ->route('center-sync.centers.show', $center)
            ->with('success', 'Center created successfully. Copy the sync token before navigating away.');
    }

    /**
     * Show a single center's detail page.
     */
    public function show(Center $center)
    {
        return view('center-sync.centers.show', compact('center'));
    }

    /**
     * Show the edit center form.
     */
    public function edit(Center $center)
    {
        return view('center-sync.centers.edit', compact('center'));
    }

    /**
     * Update an existing center.
     */
    public function update(Request $request, Center $center)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:general_exam_centers,code,' . $center->id,
        ]);

        $center->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);

        return redirect()
            ->route('center-sync.centers.show', $center)
            ->with('success', 'Center updated successfully.');
    }

    /**
     * Toggle center active/inactive status.
     */
    public function toggleActive(Center $center)
    {
        $center->update([
            'is_active' => !$center->is_active,
        ]);

        return redirect()
            ->route('center-sync.centers.show', $center)
            ->with('success', $center->is_active ? 'Center activated.' : 'Center deactivated.');
    }

    /**
     * Regenerate the center's sync token.
     */
    public function regenerateToken(Center $center)
    {
        $token = Center::generateToken();

        $center->update([
            'token_hash' => Center::hashToken($token),
        ]);

        // Store the plain token for one-time display
        session()->flash('center_token', $token);
        session()->flash('center_id', $center->id);

        return redirect()
            ->route('center-sync.centers.show', $center)
            ->with('success', 'Token regenerated. Copy the new sync token before navigating away.');
    }
}

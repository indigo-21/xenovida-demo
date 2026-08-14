<?php

namespace App\Http\Controllers;

use App\Models\UserLevel;
use App\Services\UserLevelServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLevelController extends Controller
{
    public function __construct(UserLevelServices $userLevelServices) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Shared table data
    // ─────────────────────────────────────────────────────────────────────────

    private function getTableData(): array
    {
        return [
            'userLevels' => UserLevel::withCount('users')->latest()->get(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Index
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        return view('pages.user-level.index', $this->getTableData());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('pages.user-level.form');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Store
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request, UserLevelServices $userLevelServices)
    {
        $userLevelServices->upsert($request);

        return redirect()
            ->route('user-level.index')
            ->with('success', 'User Level created successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show (redirect to edit — no separate show page needed)
    // ─────────────────────────────────────────────────────────────────────────

    public function show(UserLevel $userLevel)
    {
        return redirect()->route('user-level.edit', $userLevel);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit
    // ─────────────────────────────────────────────────────────────────────────

    public function edit(UserLevel $userLevel)
    {
        return view('pages.user-level.form', compact('userLevel'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Update
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, UserLevel $userLevel, UserLevelServices $userLevelServices)
    {
        $userLevelServices->upsert($request, $userLevel->id);

        return redirect()
            ->route('user-level.index')
            ->with('success', "User Level \"{$userLevel->description}\" updated successfully.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destroy
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(UserLevel $userLevel)
    {
        // Prevent deletion if users are still assigned to this level
        if ($userLevel->users()->exists()) {
            return response()->json([
                'error' => true,
                'message' => "Cannot delete \"{$userLevel->description}\" — it still has assigned users.",
            ], 422);
        }

        $description = $userLevel->description;
        $userLevel->deleted_by = Auth::id();
        $userLevel->save();
        $userLevel->delete();

        // If the request expects JSON (AJAX delete from index table), return JSON.
        // Otherwise redirect (delete from form page).
        if (request()->expectsJson()) {
            return response()->json([
                'error' => false,
                'message' => "User Level \"{$description}\" deleted successfully.",
            ]);
        }

        return redirect()
            ->route('user-level.index')
            ->with('success', "User Level \"{$description}\" deleted successfully.");
    }
}

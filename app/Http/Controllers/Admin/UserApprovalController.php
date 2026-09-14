<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserApprovalController extends Controller
{
    /**
     * Display the list of users pending approval.
     */
    public function index(): View
    {
        $pendingUsers = User::where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.approvals', compact('pendingUsers'));
    }

    /**
     * Approve a user account so they can log in.
     */
    public function approve(User $user): RedirectResponse
    {
        $user->update(['is_approved' => true]);

        return redirect()->route('admin.approvals')
            ->with('status', "Akun {$user->name} ({$user->email}) berhasil disetujui.");
    }
}

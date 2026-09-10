<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PendingChangeController extends Controller
{
    public function index(): View
    {
        $pending = PendingChange::with('submitter')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $reviewed = PendingChange::with(['submitter', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at')
            ->limit(20)
            ->get();

        return view('approvals.index', compact('pending', 'reviewed'));
    }

    public function approve(PendingChange $pendingChange): RedirectResponse
    {
        abort_unless($pendingChange->status === 'pending', 404);

        $pendingChange->approve(Auth::user());

        return redirect()->route('approvals.index')->with('success', 'Change approved and applied.');
    }

    public function reject(Request $request, PendingChange $pendingChange): RedirectResponse
    {
        abort_unless($pendingChange->status === 'pending', 404);

        $request->validate(['review_notes' => ['nullable', 'string', 'max:500']]);

        $pendingChange->reject(Auth::user(), $request->input('review_notes'));

        return redirect()->route('approvals.index')->with('success', 'Change rejected.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dispute::with([
            'raisedBy',
            'employmentRecord.employee.user',
            'employmentRecord.employer',
            'resolvedBy',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['open', 'under_review']);
        }

        if ($request->filled('type')) {
            $query->where('dispute_type', $request->type);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('raisedBy', fn($sub) =>
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
            );
        }

        $disputes = $query->latest()->paginate(20)->appends($request->query());

        $counts = [
            'open'         => Dispute::where('status', 'open')->count(),
            'under_review' => Dispute::where('status', 'under_review')->count(),
            'resolved'     => Dispute::where('status', 'resolved')->count(),
            'dismissed'    => Dispute::where('status', 'dismissed')->count(),
        ];

        return view('admin.disputes.index', compact('disputes', 'counts'));
    }

    public function show(Dispute $dispute): View
    {
        $dispute->load([
            'raisedBy',
            'resolvedBy',
            'employmentRecord.employee.user',
            'employmentRecord.employer',
            'employmentRecord.feedback',
        ]);

        return view('admin.disputes.show', compact('dispute'));
    }

    public function markUnderReview(Dispute $dispute): RedirectResponse
    {
        abort_unless($dispute->status === 'open', 422, 'Dispute is not in open status.');

        $dispute->update(['status' => 'under_review']);

        return back()->with('success', 'Dispute marked as under review.');
    }

    public function resolve(Request $request, Dispute $dispute): RedirectResponse
    {
        abort_unless(in_array($dispute->status, ['open', 'under_review']), 422, 'Dispute cannot be resolved from its current state.');

        $request->validate([
            'admin_resolution' => 'required|string|min:20|max:2000',
        ]);

        $dispute->update([
            'status'           => 'resolved',
            'admin_resolution' => $request->admin_resolution,
            'resolved_by'      => auth()->id(),
            'resolved_at'      => now(),
        ]);

        return redirect()->route('admin.disputes.index')
                         ->with('success', 'Dispute resolved successfully.');
    }

    public function dismiss(Request $request, Dispute $dispute): RedirectResponse
    {
        abort_unless(in_array($dispute->status, ['open', 'under_review']), 422, 'Dispute cannot be dismissed from its current state.');

        $request->validate([
            'admin_resolution' => 'required|string|min:10|max:1000',
        ]);

        $dispute->update([
            'status'           => 'dismissed',
            'admin_resolution' => $request->admin_resolution,
            'resolved_by'      => auth()->id(),
            'resolved_at'      => now(),
        ]);

        return redirect()->route('admin.disputes.index')
                         ->with('success', 'Dispute dismissed.');
    }
}
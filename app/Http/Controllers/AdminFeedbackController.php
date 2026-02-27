<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class AdminFeedbackController extends Controller
{
    /**
     * Show feedback list
     */
    public function index()
    {
        $feedbacks = Feedback::latest()->get();

        // ✅ Counts for stat cards
        $pendingCount   = Feedback::where('is_reviewed', 0)->count();
        $reviewedCount  = Feedback::where('is_reviewed', 1)->count();
        $totalCount     = Feedback::count();
        $averageRating  = round(Feedback::avg('rating') ?? 0, 1);

        return view('admin.feedback-management', compact(
            'feedbacks',
            'pendingCount',
            'reviewedCount',
            'totalCount',
            'averageRating'
        ));
    }

    /**
     * Mark feedback as reviewed
     */
    public function markReviewed($id)
    {
        $fb = Feedback::findOrFail($id);
        $fb->is_reviewed = 1;
        $fb->save();

        return back()->with('success', 'Feedback marked as reviewed.');
    }
}
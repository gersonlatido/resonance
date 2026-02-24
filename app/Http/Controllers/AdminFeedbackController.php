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

        return view('admin.feedback-management', compact('feedbacks'));
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

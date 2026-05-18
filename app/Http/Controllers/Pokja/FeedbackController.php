<?php

namespace App\Http\Controllers\Pokja;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['pembimbingDudi.dudi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pembimbingDudi', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhereHas('dudi', function($sub) use ($search) {
                      $sub->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $feedbacks = $query->latest()->paginate(10)->withQueryString();

        return view('pokja.feedback.index', compact('feedbacks'));
    }
}

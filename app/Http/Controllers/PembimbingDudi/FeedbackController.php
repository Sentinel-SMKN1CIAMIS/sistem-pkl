<?php

namespace App\Http\Controllers\PembimbingDudi;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $pembimbingDudi = auth()->user()->pembimbingDudi;
        $feedbacks = Feedback::where('pembimbing_dudi_id', $pembimbingDudi->id)
            ->latest()
            ->paginate(10);

        return view('pembimbing-dudi.feedback.index', compact('feedbacks'));
    }

    public function create()
    {
        return view('pembimbing-dudi.feedback.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required|string|max:100',
            'isi_feedback' => 'required|string',
            'saran' => 'nullable|string',
        ]);

        $pembimbingDudi = auth()->user()->pembimbingDudi;

        Feedback::create([
            'pembimbing_dudi_id' => $pembimbingDudi->id,
            'periode' => $request->periode,
            'isi_feedback' => $request->isi_feedback,
            'saran' => $request->saran,
        ]);

        return redirect()->route('pembimbing_dudi.feedback.index')
            ->with('success', 'Feedback berhasil dikirim ke sekolah.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('reviewable')->latest();

        if ($status = $request->input('status')) {
            match ($status) {
                'pending' => $query->where('is_approved', false),
                'approved' => $query->where('is_approved', true),
                default => null,
            };
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('author_name', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
            'avg_rating' => round(Review::where('is_approved', true)->avg('rating') ?? 0, 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Yorum onaylandı.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Yorum reddedildi.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Yorum silindi.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        Review::whereIn('id', $request->input('ids'))->update(['is_approved' => true]);

        return back()->with('success', count($request->input('ids')) . ' yorum onaylandı.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        Review::whereIn('id', $request->input('ids'))->delete();

        return back()->with('success', count($request->input('ids')) . ' yorum silindi.');
    }
}

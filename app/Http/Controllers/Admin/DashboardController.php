<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\FormSubmission;
use App\Models\Page;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_pages' => Page::count(),
            'total_articles' => Article::count(),
            'published_pages' => Page::where('status', 'published')->count(),
            'new_submissions' => FormSubmission::where('status', 'new')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

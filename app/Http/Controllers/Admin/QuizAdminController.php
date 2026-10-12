<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatLog;
use App\Models\Quiz;
use Illuminate\View\View;

class QuizAdminController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::with('questions')->latest()->get();
        $cheatLogs = AntiCheatLog::with('attempt.user')->latest('recorded_at')->paginate(20);

        return view('admin.quizzes.index', compact('quizzes', 'cheatLogs'));
    }
}

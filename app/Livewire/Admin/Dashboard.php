<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_quizzes' => Quiz::count(),
            'published_quizzes' => Quiz::where('is_published', true)->count(),
            'total_questions' => Question::count(),
            'total_attempts' => QuizAttempt::count(),
            'completed_attempts' => QuizAttempt::whereNotNull('completed_at')->count(),
        ];

        $recentAttempts = QuizAttempt::with('quiz')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take(10)
            ->get();

        $popularQuizzes = Quiz::withCount('attempts')
            ->where('is_published', true)
            ->orderBy('attempts_count', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recentAttempts' => $recentAttempts,
            'popularQuizzes' => $popularQuizzes,
        ]);
    }
}
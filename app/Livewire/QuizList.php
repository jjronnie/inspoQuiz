<?php

namespace App\Livewire;

use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithPagination;

class QuizList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $quizzes = Quiz::query()
            ->where('is_published', true)
            ->withCount('questions', 'attempts')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(9);

        return view('livewire.quiz-list', [
            'quizzes' => $quizzes,
        ]);
    }
}
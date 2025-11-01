<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithPagination;

class QuizIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingDeletion = false;
    public $quizToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($quizId)
    {
        $this->quizToDelete = $quizId;
        $this->confirmingDeletion = true;
    }

    public function deleteQuiz()
    {
        $quiz = Quiz::find($this->quizToDelete);
        if ($quiz) {
            $quiz->delete();
            session()->flash('success', 'Quiz deleted successfully.');
        }
        
        $this->confirmingDeletion = false;
        $this->quizToDelete = null;
    }

    public function togglePublish($quizId)
    {
        $quiz = Quiz::find($quizId);
        if ($quiz) {
            $quiz->is_published = !$quiz->is_published;
            $quiz->save();
            
            $status = $quiz->is_published ? 'published' : 'unpublished';
            session()->flash('success', "Quiz {$status} successfully.");
        }
    }

    public function render()
    {
        $quizzes = Quiz::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('questions', 'attempts')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.quiz-index', [
            'quizzes' => $quizzes,
        ]);
    }
}
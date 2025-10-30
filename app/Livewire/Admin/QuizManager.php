<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;

class QuizManager extends Component
{
    use WithPagination;

    // Form properties
    #[Rule('required|string|min:3')]
    public string $title = '';

    #[Rule('nullable|integer|min:1')]
    public ?int $time_limit_minutes = null;

    #[Rule('boolean')]
    public bool $is_published = false;

    public ?Quiz $editing = null;

    protected $queryString = ['editing' => ['except' => null]];

    public function mount()
    {
        // Ensure user is authenticated, though typically handled by route middleware
        if (!Auth::check()) {
            return redirect('/login');
        }
    }

    /**
     * Display the quiz creation modal.
     */
    public function create()
    {
        $this->reset(['title', 'time_limit_minutes', 'is_published', 'editing']);
        $this->dispatch('open-modal', name: 'quiz-form');
    }

    /**
     * Load existing quiz for editing.
     */
    public function edit(Quiz $quiz)
    {
        $this->editing = $quiz;
        $this->title = $quiz->title;
        $this->time_limit_minutes = $quiz->time_limit_minutes;
        $this->is_published = $quiz->is_published;
        $this->dispatch('open-modal', name: 'quiz-form');
    }

    /**
     * Save/Update the quiz record.
     */
    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . time(), // Simple unique slug generation
            'time_limit_minutes' => $this->time_limit_minutes,
            'is_published' => $this->is_published,
            'user_id' => Auth::id(),
        ];

        if ($this->editing) {
            // Update mode: preserve original slug if title didn't change, otherwise generate new one
            $data['slug'] = $this->editing->title === $this->title
                ? $this->editing->slug
                : Str::slug($this->title) . '-' . time();

            $this->editing->update($data);
            $message = 'Quiz updated successfully!';
        } else {
            Quiz::create($data);
            $message = 'Quiz created successfully!';
        }

        $this->reset(['title', 'time_limit_minutes', 'is_published', 'editing']);
        $this->dispatch('close-modal', name: 'quiz-form');
        $this->dispatch('notify', message: $message);
    }

    /**
     * Delete a quiz record.
     */
    public function delete(Quiz $quiz)
    {
        $quiz->delete();
        $this->dispatch('notify', message: 'Quiz deleted successfully!');
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.admin.quiz-manager', [
            'quizzes' => Quiz::where('user_id', Auth::id())
                ->latest()
                ->paginate(10),
        ]);
    }
}

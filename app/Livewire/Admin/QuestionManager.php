<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Models\Question;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class QuestionManager extends Component
{
    public Quiz $quiz;

    public string $questionText = '';
    public array $answers = [];

    public ?Question $editingQuestion = null;

    /**
     * Initialize component.
     */
    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;
        $this->resetForm();
    }

    /**
     * Reset the form for new question creation.
     */
    public function resetForm()
    {
        $this->questionText = '';
        $this->answers = [
            ['text' => '', 'is_correct' => true],
            ['text' => '', 'is_correct' => false],
        ];
        $this->editingQuestion = null;
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        return [
            'questionText' => 'required|string|min:5',
            'answers' => [
                'required',
                'array',
                'min:2',
                'max:6',
                function ($attribute, $value, $fail) {
                    $correctCount = collect($value)->where('is_correct', true)->count();
                    if ($correctCount !== 1) {
                        $fail('You must select exactly one correct answer.');
                    }
                }
            ],
            'answers.*.text' => 'required|string|min:1',
            'answers.*.is_correct' => 'boolean',
        ];
    }

    /**
     * Add a new answer option.
     */
    public function addAnswer()
    {
        if (count($this->answers) >= 6) {
            $this->dispatch('notify', message: 'Maximum of 6 answers allowed.');
            return;
        }

        $this->answers[] = ['text' => '', 'is_correct' => false];
    }

    /**
     * Remove an answer option.
     */
    public function removeAnswer(int $index)
    {
        if (count($this->answers) <= 2) {
            $this->dispatch('notify', message: 'A question must have at least 2 answers.');
            return;
        }

        // Remove the answer
        array_splice($this->answers, $index, 1);

        // Ensure at least one correct answer remains
        if (!collect($this->answers)->contains('is_correct', true)) {
            $this->answers[0]['is_correct'] = true;
        }
    }

    /**
     * Load a question for editing.
     */
    public function edit(Question $question)
    {
        $this->editingQuestion = $question;
        $this->questionText = $question->text;
        $this->answers = $question->answers->map(fn($a) => [
            'id' => $a->id,
            'text' => $a->text,
            'is_correct' => $a->is_correct,
        ])->toArray();

        $this->dispatch('open-modal', name: 'question-form');
    }

    /**
     * Save or update question and answers.
     */
    public function saveQuestion()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->editingQuestion) {
                    $question = $this->editingQuestion;
                    $question->update(['text' => $this->questionText]);
                    // Delete old answers
                    $question->answers()->delete();
                    $message = 'Question updated successfully.';
                } else {
                    $nextOrder = ($this->quiz->questions()->max('order') ?? 0) + 1;
                    $question = $this->quiz->questions()->create([
                        'text' => $this->questionText,
                        'order' => $nextOrder,
                    ]);
                    $message = 'Question created successfully.';
                }

                // Save answers
                foreach ($this->answers as $answer) {
                    $question->answers()->create([
                        'text' => $answer['text'],
                        'is_correct' => $answer['is_correct'],
                    ]);
                }

                $this->resetForm();
                $this->dispatch('close-modal', name: 'question-form');
                $this->dispatch('notify', message: $message);
            });
        } catch (\Exception $e) {
            \Log::error($e);
            $this->dispatch('notify', message: 'An error occurred while saving the question.');
        }
    }

    /**
     * Delete a question.
     */
    public function delete(Question $question)
    {
        $question->delete();
        $this->dispatch('notify', message: 'Question and answers deleted successfully!');
    }

    public function render()
    {
        return view('livewire.admin.question-manager', [
            'questions' => $this->quiz->questions()->with('answers')->orderBy('order')->get(),
        ]);
    }
}

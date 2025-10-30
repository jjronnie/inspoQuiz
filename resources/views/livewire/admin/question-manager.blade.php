<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Managing Questions for: **{{ $quiz->title }}**
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <x-secondary-button wire:navigate href="{{ route('admin.quizzes.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Quizzes
                    </x-secondary-button>
                    <x-primary-button wire:click="create">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add New Question
                    </x-primary-button>
                </div>

                <!-- Questions List -->
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Questions ({{ $questions->count() }})</h3>
                <div class="space-y-4">
                    @forelse ($questions as $question)
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700/50" wire:key="{{ $question->id }}">
                            <div class="flex justify-between items-start">
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $question->order }}. {{ $question->text }}
                                </p>
                                <div class="flex space-x-2">
                                    <button wire:click="edit({{ $question->id }})" class="text-blue-500 hover:text-blue-700">Edit</button>
                                    <button wire:click="delete({{ $question->id }})" wire:confirm="Delete this question and all its answers?" class="text-red-500 hover:text-red-700">Delete</button>
                                </div>
                            </div>
                            <!-- Answers List -->
                            <ul class="mt-2 space-y-1">
                                @foreach ($question->answers as $answer)
                                    <li class="text-sm {{ $answer->is_correct ? 'font-medium text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300' }}">
                                        {!! $answer->is_correct ? '&#10003;' : '&#8226;' !!} {{ $answer->text }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">This quiz has no questions yet. Click "Add New Question" to begin.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <!-- Create/Edit Question Modal -->
    <x-modal name="question-form" :show="$editingQuestion || $errors->any()" focusable>
        <form wire:submit="saveQuestion" class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                {{ $editingQuestion ? 'Edit Question' : 'Add New Question' }}
            </h2>

            <!-- Question Text -->
            <div class="mb-4">
                <x-input-label for="questionText" value="Question Text" />
                <textarea id="questionText" wire:model="questionText" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('questionText')" />
            </div>

            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Answers (Select exactly one correct answer)</h3>

            <!-- Dynamic Answers List -->
            <div class="space-y-3">
                @foreach ($answers as $index => $answer)
                    <div class="flex items-center space-x-2 p-2 border rounded-md dark:border-gray-700 bg-white dark:bg-gray-800">
                        <!-- Correct/Incorrect Radio Button -->
                        <input
                            type="radio"
                            name="correct_answer"
                            wire:model="answers.{{ $index }}.is_correct"
                            value="1"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            title="Mark as correct answer"
                            onclick="event.preventDefault(); $wire.set('answers.{{ $index }}.is_correct', true); @foreach (range(0, count($answers) - 1) as $i) @if ($i !== $index) $wire.set('answers.{{ $i }}.is_correct', false); @endif @endforeach"
                        >

                        <!-- Answer Text Input -->
                        <input
                            type="text"
                            wire:model="answers.{{ $index }}.text"
                            placeholder="Answer option {{ $index + 1 }}"
                            class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                        >

                        <!-- Remove Button -->
                        @if (count($answers) > 2)
                            <button type="button" wire:click="removeAnswer({{ $index }})" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        @endif
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('answers.' . $index . '.text')" />
                @endforeach
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('answers')" />

            <!-- Add Answer Button -->
            <div class="mt-4">
                <x-secondary-button type="button" wire:click="addAnswer" class="w-full justify-center" :disabled="count($answers) >= 6">
                    + Add Another Answer (Max 6)
                </x-secondary-button>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close'); $wire.resetForm()">
                    Cancel
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ $editingQuestion ? 'Save Changes' : 'Create Question' }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Notification (Assuming the parent view handles the 'notify' event) -->

</div>

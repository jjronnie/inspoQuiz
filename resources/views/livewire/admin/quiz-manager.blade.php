<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Quiz Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Your Quizzes</h3>
                    <x-primary-button wire:click="create">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Create New Quiz
                    </x-primary-button>
                </div>

                <!-- Quiz Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time Limit (min)</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Published</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($quizzes as $quiz)
                                <tr wire:key="{{ $quiz->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $quiz->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $quiz->time_limit_minutes ?? 'No Limit' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $quiz->is_published ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.questions.manage', $quiz) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-4">
                                            Questions
                                        </a>
                                        <button wire:click="edit({{ $quiz->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-4">
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $quiz->id }})" wire:confirm="Are you sure you want to delete this quiz and all its questions/answers?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No quizzes created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Quiz Modal (Assuming you have a standard x-modal component) -->
    <x-modal name="quiz-form" :show="$editing || $errors->any()" focusable>
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $editing ? 'Edit Quiz' : 'Create New Quiz' }}
            </h2>

            <div class="mt-4">
                <x-input-label for="title" value="Quiz Title" />
                <x-text-input id="title" wire:model="title" type="text" class="mt-1 block w-full" autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div class="mt-4">
                <x-input-label for="time_limit_minutes" value="Time Limit (Minutes)" />
                <x-text-input id="time_limit_minutes" wire:model="time_limit_minutes" type="number" min="1" class="mt-1 block w-full" placeholder="e.g., 10" />
                <x-input-error class="mt-2" :messages="$errors->get('time_limit_minutes')" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank for no time limit.</p>
            </div>

            <div class="mt-4">
                <label for="is_published" class="flex items-center">
                    <x-checkbox id="is_published" wire:model="is_published" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Publish Quiz (Allow users to attempt)</span>
                </label>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ $editing ? 'Save Changes' : 'Create Quiz' }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Simple Notification (Assuming Livewire/Starter Kit has a simple way to handle 'notify' events) -->
    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed bottom-5 right-5 z-50 p-4 bg-green-500 text-white rounded-lg shadow-xl"
        style="display: none;"
    >
        <span x-text="message"></span>
    </div>

</div>

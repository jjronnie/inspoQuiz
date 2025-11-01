<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                Quiz Challenge
            </h1>
            <p class="text-xl text-gray-600">
                Test your knowledge with our exciting quizzes!
            </p>
        </div>

        <!-- Search -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="relative">
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Search quizzes..."
                       class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent shadow-sm">
                <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Quizzes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($quizzes as $quiz)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-32 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition">
                            {{ $quiz->title }}
                        </h3>
                        
                        @if($quiz->description)
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                {{ $quiz->description }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between mb-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $quiz->questions_count }} Questions
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $quiz->time_limit }} min
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                {{ $quiz->attempts_count }} attempts
                            </span>
                            <a href="{{ route('quiz.start', $quiz->id) }}" 
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-300 shadow-md hover:shadow-lg">
                                Start Quiz
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-xl text-gray-500">No quizzes available at the moment.</p>
                    <p class="text-gray-400 mt-2">Check back later for new quizzes!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($quizzes->hasPages())
            <div class="mt-12">
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>
</div>
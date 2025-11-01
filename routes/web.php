<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use App\Livewire\Admin\QuizIndex;
use App\Livewire\Admin\QuizForm;
use App\Livewire\Admin\QuestionForm;
use App\Livewire\QuizList;
use App\Livewire\TakeQuiz;
use App\Livewire\QuizResults;



use App\Livewire\Admin\QuizManager;
use App\Livewire\Admin\QuestionManager;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/', QuizList::class)->name('home');
Route::get('/quiz/{quizId}', TakeQuiz::class)->name('quiz.start');
Route::get('/quiz-results/{attemptId}', QuizResults::class)->name('quiz.results');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

 Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Quiz management
    Route::get('/quizzes', QuizIndex::class)->name('quizzes.index');
    Route::get('/quizzes/create', QuizForm::class)->name('quizzes.create');
    Route::get('/quizzes/{quizId}/edit', QuizForm::class)->name('quizzes.edit');
    Route::get('/quizzes/{quizId}/questions', QuestionForm::class)->name('quizzes.questions');
    
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

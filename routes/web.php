<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;



use App\Livewire\Admin\QuizManager;
use App\Livewire\Admin\QuestionManager;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Manage Quizzes (The main quiz records)
    Route::get('/quizzes', QuizManager::class)->name('quizzes.index');

    // 2. Manage Questions/Answers for a specific Quiz
    // Uses route model binding for the Quiz model
    Route::get('/quizzes/{quiz}/questions', QuestionManager::class)->name('questions.manage');
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

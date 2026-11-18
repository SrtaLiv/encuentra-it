<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ChallengeProgressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\RandomController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\StepProgressController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('test', [QuizController::class, 'show'])->name('quiz.show');
Route::post('test', [QuizController::class, 'store'])->middleware('throttle:20,1')->name('quiz.store');
Route::get('test/resultado/{result}', [QuizController::class, 'result'])->name('quiz.result');

Route::get('carreras', [CareerController::class, 'index'])->name('careers.index');
Route::get('carreras/{career}', [CareerController::class, 'show'])->name('careers.show');

Route::get('recursos', [ResourceController::class, 'index'])->name('resources.index');

Route::get('azar', RandomController::class)->name('random');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('pasos/{step}/toggle', StepProgressController::class)->name('steps.toggle');
    Route::post('desafios/{challenge}/completar', [ChallengeProgressController::class, 'store'])->name('challenges.complete');
    Route::delete('desafios/{challenge}/completar', [ChallengeProgressController::class, 'destroy'])->name('challenges.undo');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/careers')->name('home');
    Route::resource('areas', Admin\AreaController::class)->except('show');
    Route::resource('careers', Admin\CareerController::class)->except('show');
    Route::resource('resources', Admin\ResourceController::class)->except('show');
    Route::resource('quiz', Admin\QuizQuestionController::class)->except('show')->parameters(['quiz' => 'question']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

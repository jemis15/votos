<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VoterController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::get('/events/{event}/delete', [EventController::class, 'delete'])->name('events.delete');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    Route::get('/cargos/create', [CargosController::class, 'create'])->name('cargos.create');
    Route::post('/cargos', [CargosController::class, 'store'])->name('cargos.store');
    Route::get('/cargos/{cargo}/edit', [CargosController::class, 'edit'])->name('cargos.edit');
    Route::put('/cargos/{cargo}', [CargosController::class, 'update'])->name('cargos.update');
    Route::get('/cargos/{cargo}/delete', [CargosController::class, 'delete'])->name('cargos.delete');
    Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])->name('cargos.destroy');

    Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
    Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
    Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
    Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
    Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
    Route::get('/candidates/{candidate}/delete', [CandidateController::class, 'delete'])->name('candidates.delete');
    Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('candidates.destroy');

    Route::get('voters/create', [VoterController::class, 'create'])->name('voters.create');
    Route::post('voters', [VoterController::class, 'store'])->name('voters.store');
    Route::delete('voters/{event}/{user}', [VoterController::class, 'destroy'])->name('voters.delete');

    Route::post('events/{event}/import-candidates', [CandidateController::class, 'import'])->name('candidates.import');
    Route::post('events/{event}/import-voters', [VoterController::class, 'import'])->name('voters.import');
});


require __DIR__ . '/auth.php';

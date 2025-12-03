<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoterController;
use App\Livewire\Rooms\CreateElections;
use App\Livewire\Rooms\Elections\Index as ElectionsIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::redirect('/', '/login')->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
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
    Route::get('events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::post('events/{event}/import-candidates', [CandidateController::class, 'import'])->name('candidates.import');
    Route::post('events/{event}/import-voters', [VoterController::class, 'import'])->name('voters.import');
    Route::post('events/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('events.toggle-status');

    Route::get('/cargos/create', [CargosController::class, 'create'])->name('cargos.create');
    Route::post('/cargos', [CargosController::class, 'store'])->name('cargos.store');
    Route::get('/cargos/{cargo}/edit', [CargosController::class, 'edit'])->name('cargos.edit');
    Route::put('/cargos/{cargo}', [CargosController::class, 'update'])->name('cargos.update');
    Route::get('/cargos/{cargo}/delete', [CargosController::class, 'delete'])->name('cargos.delete');
    Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])->name('cargos.destroy');
    Route::post('cargos/{cargo}/start', [CargosController::class, 'start'])->name('events.cargos.start');

    Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
    Route::delete('events/{event}/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('rooms.candidates.destroy');
    
    Route::post('/elections/{election}/candidates/toggle', [ElectionController::class, 'toggleCandidate'])->name('elections.candidates.toggle');

    Route::get('voters/create', [VoterController::class, 'create'])->name('voters.create');
    Route::post('voters', [VoterController::class, 'store'])->name('voters.store');
    Route::delete('voters/{event}/{user}', [VoterController::class, 'destroy'])->name('voters.delete');

    Route::post('elections', [ElectionController::class, 'store'])->name('elections.store');
    Route::delete('elections/{election}', [ElectionController::class, 'destroy'])->name('elections.destroy');
    Route::post('elections/{election}/toggle-status', [ElectionController::class, 'toggleStatus'])->name('elections.toggle-status');

    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('users', [UserController::class, 'create'])->name('users.create');

    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');

    Route::get('events/{event}/live', [EventController::class, 'live'])->name('events.live');
    Route::post('register-vote', [RoomController::class, 'registerVote']);
    Route::get('rooms/{room}', [RoomController::class, 'index'])->name('rooms.register-votes');

    Route::get('rooms/{room}/elections', ElectionsIndex::class)->name('rooms.elections.index');

    Route::get('rooms/{room}/elections/create', CreateElections::class)->name('rooms.elections.create');

    Route::get('rooms/{room}/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('rooms.candidates.edit');
    Route::put('rooms/{room}/candidates/{candidate}', [CandidateController::class, 'update'])->name('rooms.candidates.update');
});


require __DIR__ . '/auth.php';

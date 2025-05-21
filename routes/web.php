<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\KlasemenController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\BracketController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchResultController;
use App\Http\Controllers\TournamentRequestController;

Route::middleware('auth')->group(function () {
    Route::get('/tournament/create', [TournamentController::class, 'create'])->name('tournament.create');
    Route::post('/tournament/store', [TournamentController::class, 'store'])->name('tournament.store');
    Route::get('/tournament/{id}/edit', [TournamentController::class, 'edit'])->name('tournament.edit');
    Route::put('/tournament/{id}', [TournamentController::class, 'update'])->name('tournament.update');
    Route::delete('/tournament/{id}', [TournamentController::class, 'destroy'])->name('tournament.destroy');

    Route::get('/tourney-saya', [TournamentController::class, 'myTournaments'])->name('tournament.mine');
    Route::get('/teams/{id}', [TeamController::class, 'showHome'])->name('teams.home');

    Route::put('/schedule/update/{id}', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::post('/tournament/{id}/generate-schedule', [ScheduleController::class, 'generateSchedule'])->name('generate.schedule');

    Route::post('/tournament/{tournament_id}/generate-bracket', [ScheduleController::class, 'generateBracket'])
        ->name('generate.bracket');

    //Match Result
    Route::get('/dashboard/jadwal/{id_tournament}/{id_schedule}/insert-result', [MatchResultController::class, 'create'])
        ->name('matchResults.create');
    Route::post('/dashboard/jadwal/{id_tournament}/{id_schedule}/insert-result', [MatchResultController::class, 'store'])
        ->name('matchResults.store');
    Route::get('/dashboard/jadwal/{id_tournament}/{id_schedule}/edit-result', [MatchResultController::class, 'edit'])
        ->name('matchResults.edit');
    Route::put('/dashboard/jadwal/{id_tournament}/{id_schedule}/update-result', [MatchResultController::class, 'update'])
        ->name('matchResults.update');

    Route::resource('teams', TeamController::class);
    Route::resource('players', PlayerController::class);

    //Request Join
    Route::post('/tournament/{id}/join', [TournamentRequestController::class, 'requestJoin'])
        ->middleware('auth')
        ->name('tournament.join');
    Route::get('/admin/tournament-requests', [TournamentRequestController::class, 'adminRequests'])
        ->name('admin.tournament.requests');
    Route::post('/admin/tournament-requests/{id}/approve', [TournamentRequestController::class, 'approveRequest'])
        ->name('admin.tournament.approve');
    Route::post('/admin/tournament-requests/{id}/reject', [TournamentRequestController::class, 'rejectRequest'])
        ->name('admin.tournament.reject');
});

//Route Tanpa Login
Route::get('/dashboard/jadwal/{id}', [ScheduleController::class, 'show'])->name('dashboard.jadwal');
Route::get('/dashboard/klasemen/{id}', [KlasemenController::class, 'show'])->name('tournament.klasemen');
Route::get('/dashboard/statistik/{tournamentId}', [StatistikController::class, 'show'])->name('statistik');
Route::get('/dashboard/bracket/{id}', [BracketController::class, 'generateBracket'])->name('tournament.bracket');
Route::get('/tournament/{id}/teams', [TeamController::class, 'index'])->name('tournament.teams');
Route::get('/tournament/{tournament_id}/teams/{id}', [TeamController::class, 'show'])->name('teams.show');


Route::get('/tournament/{id}', [TournamentController::class, 'show'])->name('tournament.show');
Route::get('/tournament/detail/{id}', [TournamentController::class, 'detail'])->name('tournament.detail');

// Match Result Detail (untuk semua user kecuali role:1)
Route::get('/dashboard/jadwal/{id_tournament}/matchresult/{id_schedule}', [MatchResultController::class, 'show'])->name('matchResults.show');

// Route untuk halaman welcome yang menampilkan daftar turnamen
Route::get('/', [TournamentController::class, 'index'])->name('welcome');

//Sheet Import & Download
Route::get(
    '/tournaments/{id_tournament}/schedules/{id_schedule}/download-template',
    [MatchResultController::class, 'downloadTemplate']
)
    ->name('matchResults.downloadTemplate');

Route::post(
    '/tournaments/{id_tournament}/schedules/{id_schedule}/import-sheet',
    [MatchResultController::class, 'importFromSheet']
)
    ->name('matchResults.importFromSheet');

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

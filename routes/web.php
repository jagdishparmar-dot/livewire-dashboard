<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'pages::login')->name('login');
    Route::livewire('/register', 'pages::register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::livewire('/', 'pages::dashboard')->name('dashboard');
    Route::livewire('/table-editor', 'pages::table-editor')->name('table-editor');
    Route::livewire('/sql-editor', 'pages::sql-editor')->name('sql-editor');
    Route::livewire('/database', 'pages::database')->name('database');
    Route::livewire('/authentication', 'pages::authentication')->name('authentication');
    Route::livewire('/storage', 'pages::storage')->name('storage');
    Route::livewire('/edge-functions', 'pages::edge-functions')->name('edge-functions');
    Route::livewire('/realtime', 'pages::realtime')->name('realtime');
    Route::livewire('/reports', 'pages::reports')->name('reports');
    Route::livewire('/logs', 'pages::logs')->name('logs');
    Route::livewire('/profile', 'pages::profile')->name('profile');
    Route::livewire('/project-settings', 'pages::project-settings')->name('project-settings');

    Route::post('/logout', function () {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

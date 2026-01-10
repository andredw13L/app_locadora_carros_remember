<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('locadora/Index');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

route::get('/marcas', function() {
    return Inertia::render('locadora/Marcas');
})->name('marcas')->middleware('auth:sanctum');

// TODO: Rota de fallback e página em Vue.js

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

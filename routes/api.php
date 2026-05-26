<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// =========================================================
//  PUBLIC ROUTES (tanpa token)
// =========================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// =========================================================
//  PRIVATE ROUTES (wajib pakai Bearer Token dari Sanctum)
// =========================================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Tasks
    Route::get   ('/tasks',              [TaskController::class, 'apiIndex']);   // GET semua tugas
    Route::post  ('/tasks',              [TaskController::class, 'apiStore']);   // POST tambah tugas
    Route::get   ('/tasks/{id}',         [TaskController::class, 'apiShow']);    // GET detail tugas
    Route::put   ('/tasks/{id}',         [TaskController::class, 'apiUpdate']);  // PUT edit tugas
    Route::delete('/tasks/{id}',         [TaskController::class, 'apiDestroy']); // DELETE hapus tugas
    Route::patch ('/tasks/{id}/toggle',  [TaskController::class, 'apiToggle']);  // PATCH toggle selesai

});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    
    // Admin routes
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', UserController::class);
        Route::get('user-datatable', [UserController::class,'datatable'])->name('user-datatable');
    });

    // Project Manager routes (and Admin routes)
    Route::group(['middleware' => ['role:admin|project manager']], function () {
        Route::resource('projects', ProjectController::class);
        Route::get('project-datatable', [ProjectController::class,'datatable'])->name('project-datatable');

        Route::resource('tasks', TaskController::class);
        Route::get('task-datatable', [TaskController::class,'datatable'])->name('task-datatable');
        Route::post('getProjectTeamMembers', [TaskController::class,'getProjectTeamMembers'])->name('get-team-members');
    });

    // Team Member routes (and Admin routes)
    Route::group(['middleware' => ['role:admin|team member']], function () {
        Route::get('assignedTask', [TaskController::class,'assignedTask'])->name('assigned-task');
        Route::get('assignedTaskList', [TaskController::class,'assignedTaskList'])->name('assigned-task-list');
        Route::post('updateStatus', [TaskController::class,'updateStatus'])->name('update-status');
    });
});

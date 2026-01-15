<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XatStudentResultController;
use App\Http\Controllers\XatCollegeController;
use App\Http\Controllers\XatUserController;
use App\Http\Controllers\SnapUserController;
use App\Http\Controllers\SnapStudentResultController;


Route::group(['middleware'=>'guest'],function(){
    Route::get('/login',[AuthController::class,'login'])->name('login');
    Route::post('/signin',[AuthController::class,'signin'])->name('signin');
});

Route::post('admin/logout', [AuthController::class, 'signout'])->name('admin.logout');
Route::as('backend.')->prefix('backend')->group(function(){
    Route::get('dashboard',[DashboardController::class, 'index'])->name('dashboard');
    Route::get('student-result',[StudentResultController::class, 'index'])->name('student-result');
    Route::get('student-results/{id}', [StudentResultController::class, 'show'])->name('student-result.show');
    Route::resource('colleges', CollegeController::class);
    Route::get('users',[UserController::class, 'index'])->name('users');
    Route::get('/student-result/export/{id}', [StudentResultController::class, 'exportStudent'])->name('student-result.export');

    Route::get('xat-student-result',[XatStudentResultController::class, 'index'])->name('xat-student-result');
    Route::get('xat-student-results/{id}', [XatStudentResultController::class, 'show'])->name('xat-student-result.show');
    Route::resource('xat-colleges', XatCollegeController::class);
    Route::get('xat-users',[XatUserController::class, 'index'])->name('xat-users');
    Route::get('/xat-student-result/export/{id}', [XatStudentResultController::class, 'exportStudent'])->name('xat-student-result.export');

    Route::get('snap-users',[SnapUserController::class, 'index'])->name('snap-users');
    Route::get('snap-student-result',[SnapStudentResultController::class, 'index'])->name('snap-student-result');
    Route::get('snap-student-results/{id}', [SnapStudentResultController::class, 'show'])->name('snap-student-result.show');
    Route::get('/snap-student-result/export/{id}', [SnapStudentResultController::class, 'exportStudent'])->name('snap-student-result.export');

    


});

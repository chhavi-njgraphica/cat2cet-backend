<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserCatResultController;
use App\Http\Controllers\Api\CollegeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserXatResultController;
use App\Http\Controllers\Api\XatCollegeController;
use App\Http\Controllers\Api\UserXatController;
use App\Http\Controllers\Api\SnapController;


// Route::get('score-calculator/cat',[UserCatResultController::class,'index'])->name('cat-result');
Route::post('user-cat-result',[UserCatResultController::class,'catResult'])->name('user-cat-result');
Route::post('cat-result-pdf', [UserCatResultController::class, 'downloadCatResultPdf'])->name('cat-result-pdf');
Route::get('colleges',[CollegeController::class,'index'])->name('colleges');
Route::post('leads-submit',[UserController::class,'submit'])->name('leads-submit');

Route::post('user-xat-result',[UserXatResultController::class,'xatResult'])->name('user-xat-result');
Route::post('xat-result-pdf', [UserXatResultController::class, 'downloadXatResultPdf'])->name('xat-result-pdf');
Route::get('xat-colleges',[XatCollegeController::class,'index'])->name('xat-colleges');
Route::post('xat-leads-submit',[UserXatController::class,'submit'])->name('xat-leads-submit');

Route::post('snap-pdf-upload',[SnapController::class,'pdfUpload'])->name('snap-pdf-upload');
Route::post('/snap-lead-submit', [SnapController::class, 'userSubmit'])->name('snap-lead-submit');
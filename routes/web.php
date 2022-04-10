<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/file-upload', [App\Http\Controllers\FileUploadController::class, 'fileUpload'])->middleware('auth');
Route::get('/get-all-files', [App\Http\Controllers\FileUploadController::class, 'getFiles'])->middleware('auth');
Route::post('/add-tags', [App\Http\Controllers\FileUploadController::class, 'addTags'])->middleware('auth');
Route::post('/select-file', [App\Http\Controllers\FileUploadController::class, 'selectFile'])->middleware('auth');
Route::post('/get-selected-file', [App\Http\Controllers\FileUploadController::class, 'getSelectedFile'])->middleware('auth');
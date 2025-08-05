<?php

use App\Http\Controllers\ToDoAppController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ToDoAppController::class, 'index'])->name('home');


Route::prefix('ToDoList')->name('todo.')->controller(ToDoAppController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::put('/{task}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
    Route::post('/complete/{task}', 'complete')->name('complete');
});
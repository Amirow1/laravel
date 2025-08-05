<?php

use App\Http\Controllers\SettingController;
use App\Http\Controllers\ToDoAppController;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::prefix('ToDoList')->name('todo.')->controller(ToDoAppController::class)->group(function(){

    Route::get('/',  'index')->name('index');
    Route::post('/',  'store')->name('store');
    Route::put('/{task}',  'update')->name('update');
    Route::delete('/{id}',  'destroy')->name('destroy');
    Route::post('/complete/{task}',  'complete')->name('complete');

});

Route::prefix('Setting')->name('setting.')->controller(SettingController::class)->group(function(){

    Route::get('/',  'index')->name('index');
    Route::post('/',  'store')->name('store');

});

Route::get('/Blog', function () {
    return view('blog.blog');
})->name('blog.index');
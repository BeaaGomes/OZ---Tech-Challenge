<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return "Back-end Challenge 2021 🏅 - Space Flight News";
});

Route::resource('articles', ArticleController::class);

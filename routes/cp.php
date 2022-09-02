<?php

use Illuminate\Support\Facades\Route;
use Appswithlove\StatamicTranslateMe\Http\Controllers\TranslateMeController;


Route::post('/translate-me', [TranslateMeController::class, 'index']);

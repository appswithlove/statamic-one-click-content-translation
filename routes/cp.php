<?php

use Appswithlove\StatamicOneClickContentTranslation\Http\Controllers\TranslateMeController;
use Illuminate\Support\Facades\Route;

Route::get('/one-click-need-translation', [TranslateMeController::class, 'check']);
Route::post('/one-click-content-translation', [TranslateMeController::class, 'index']);

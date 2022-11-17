<?php

use Illuminate\Support\Facades\Route;
use Appswithlove\StatamicOneClickContentTranslation\Http\Controllers\TranslateMeController;


Route::post('/one-click-content-translation', [TranslateMeController::class, 'index']);

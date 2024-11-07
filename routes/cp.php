<?php

use Appswithlove\StatamicOneClickContentTranslation\Http\Controllers\TranslateMeController;
use Illuminate\Support\Facades\Route;

Route::post('/one-click-content-translation', [TranslateMeController::class, 'index']);

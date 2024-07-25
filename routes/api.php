<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidController;


Route::post('/rfid', [RfidController::class, 'store']);




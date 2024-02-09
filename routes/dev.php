<?php

use App\Http\Controllers\Demande\DemandeController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Dev API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Dev API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::delete('demandes/purge', [DemandeController::class, 'purge']);
});
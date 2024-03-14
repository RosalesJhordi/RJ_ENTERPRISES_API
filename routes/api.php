<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('Registro',[AuthController::class, 'registro']); //ENDPOINT Registro = Registrar usuarios
Route::post('Login',[AuthController::class, 'login']); //ENDPOINT Login = autenticacion de usuarios
Route::get('All',[AuthController::class,'all']);
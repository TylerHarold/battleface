<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\QuoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/')->group(function() {
    Route::get('/', function() {
        return view('login');
    });

   Route::get('/register', function() {
      return view('register');
   });

   Route::get('/quote', [QuoteController::class, 'create']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\API\AgentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'message' => 'Welcome to API',
    ]);
});

// AGENT
// Route::controller(AgentController::class)
//     ->prefix('agent')
//     ->group(function () {
//         Route::post('/register', 'register');
//     });

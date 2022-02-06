<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/test', 'BulkSmsController@testCase');
Route::get('/deliver', 'BulkSmsController@deliverVfDlr');
Route::get('/hourly-dlr-check', 'BulkSmsController@hourlyDlrCheck');

Route::post('/bulk', 'BulkSmsController@generateUrl');
Route::post('/bulk/backup', 'BulkSmsController@generateUrl');
Route::any('/bulk/{random}', 'BulkSmsController@nodesSmsRefactored');

Route::get('/bulk/dlr', 'BulkSmsController@dlrReport');
Route::get('/bulk/dlr/client', 'BulkSmsController@dlrReportFromClient');


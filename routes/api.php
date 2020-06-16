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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::any('/goods/modify','Api\GoodsController@modify');

Route::any('/goods/sync','Api\GoodsController@sync');

Route::any('/barcode/query','Api\GoodsController@query');

Route::any('/payment/create','Api\PaymentController@pay');

Route::any('/order/add','Api\OrderController@add');
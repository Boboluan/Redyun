<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Admin\Controllers\UploadController;
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



Route::group(['prefix'=>'upload'],function (){

    Route::post('uploads',[UploadController::class,'Upload']);

    Route::post('import',[UploadController::class,'ImportExcel']);

});



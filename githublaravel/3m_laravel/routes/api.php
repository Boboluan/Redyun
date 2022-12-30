<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Api\Controllers\ProductController;
use App\Api\Controllers\FeedbackController;

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


/**
 * 3M京东小程序接口
 */
Route::middleware('self')->group(function(){

    Route::post('productList',[ProductController::class,'listData']);

    Route::post('detail',[ProductController::class,'detail']);

    Route::post('dispose',[ProductController::class,'DataDispose']);

    Route::post('productName',[ProductController::class,'ProductName']);
    //名称处理流程
    Route::any('ProductNamedispose',[ProductController::class,'ProductNamedispose']);

    Route::post('ProductDetail',[ProductController::class,'ProductDetail']);

    Route::post('Information',[ProductController::class,'Information']);

    Route::get('Informationpage',[ProductController::class,'Informationpage']);

    Route::post('getid',[ProductController::class,'getid']);
    //price
    Route::any('price',[ProductController::class,'price']);
    //BackUrl
    Route::any('CallBackUrl',[ProductController::class,'CallBackUrl']);
    //清除没关联商品的物质
    Route::any('ChangeMatter',[ProductController::class,'ChangeMatter']);
    //商品点击记录
    Route::post('ProductStat',[ProductController::class,'ProductStat']);
    //提交反馈
    Route::post('SubFeedback',[ProductController::class,'feedback']);
});


/**
 * 3M京东小程序接口
 */
Route::middleware('self')->group(function(){
    //提交反馈
    Route::post('SubFeedback',[FeedbackController::class,'feedback']);
});

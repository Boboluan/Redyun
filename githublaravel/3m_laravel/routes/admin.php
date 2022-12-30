<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Admin\Controllers\UsersController;
use App\Admin\Controllers\ProductController;
use App\Admin\Controllers\IndexController;
use App\Admin\Controllers\FeedbackController;
use App\Admin\Controllers\CountController;

/**
 * 操作请求接口
 */
Route::group(['prefix'=>'login'],function(){
    //登录页面
    Route::get('login', function () {
        return view('index.login');
    });
    //登录接口
    Route::post('CheckLogin',[UsersController::class,'CheckLogin']);
});


//主页
Route::group(['prefix'=>'index'],function(){
    Route::any('index',[IndexController::class,'index']);
    Route::any('earth',[IndexController::class,'earth']);
});



//操作接口
Route::middleware('admin')->group(function(){
    //修改密码
    Route::post('SetPassword',[UsersController::class,'SetPassword']);
    //添加商品
    Route::any('AddProduct',[ProductController::class,'AddProduct']);
    //修改商品
    Route::any('EditProduct',[ProductController::class,'EditProduct']);
    //删除商品
    Route::any('DeleteProduct',[ProductController::class,'DeleteProduct']);
    //批量删除商品
    Route::any('DeleteAllProduct',[ProductController::class,'DeleteAllProduct']);
    //商品列表
    Route::any('Plist',[ProductController::class,'ProductList']);
    //添加物质
    Route::any('AddMatter',[ProductController::class,'AddMatter']);
    //更新物质
    Route::any('EditEMatter',[ProductController::class,'EditEMatter']);
    //删除物质
    Route::post('DelMatter',[ProductController::class,'DelMatter']);
    //物质关系
    Route::post('MatterRelotion',[ProductController::class,'MatterRelotion']);
    //物质列表
    Route::any('MatterList',[ProductController::class,'MatterList']);
    //物质列表页
    Route::any('MatterListPage',[ProductController::class,'MatterListPage']);
    //用户列表页
    Route::any('UserList',[UsersController::class,'UserList']);
    //用户修改页面
    Route::any('UserEdit',[UsersController::class,'UserEdit']);
    //用户退出
    Route::get('Logout',[UsersController::class,'Logout']);
    //请求数据
    Route::any('ProductListPage',[ProductController::class,'ProductListPage']);
    //批量删除物质
    Route::any('delAllMatter',[ProductController::class,'delAllMatter']);
    //反馈列表
    Route::any('FeedbackList',[FeedbackController::class,'FeedbackList']);
    //反馈删除
    Route::any('FeedbackDelete',[FeedbackController::class,'FeedbackDelete']);
    //反馈数据导出
    Route::any('FeedExportData',[FeedbackController::class,'FeedExportData']);
    //统计数据页面
    Route::any('CountList',[CountController::class,'CountList']);
    //统计页面导出
    Route::any('ObjectDataExport',[CountController::class,'ObjectDataExport']);
});

Route::middleware('admin')->group(function() {
    //test 测试方法路由
    Route::any('Test', [ProductController::class, 'Test']);
    //测试导出路由2
    Route::any('Export', [ProductController::class, 'Export']);
});

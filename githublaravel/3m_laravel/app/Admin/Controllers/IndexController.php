<?php

namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class IndexController extends AdminBaseController
{

    public function index()
    {
        if(loginCheck()===false){return Redirect::to('/admin/login/login');}
        $admin =  request()->session()->get('user');
        $user = $admin['name'][0];
        return view('index.index',['user'=>$user]);
    }


    public function earth()
    {
        if(loginCheck()===false){return Redirect::to('/admin/login/login');}
        $matter = Product::query()->count();
        $product= ProductType::query()->count();
        $admin =  request()->session()->get('user');
        $user = $admin['name'][0];
        return view('index.echarts1',['product'=>$product,'matter'=>$matter,'user'=>$user]);
    }



}

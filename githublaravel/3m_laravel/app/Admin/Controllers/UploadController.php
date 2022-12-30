<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\ToolService;


class UploadController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 调用实例
     */
    public function Upload(Request $request)
    {
        $file = $request->file();
        return ApiReturn(ToolService::UploadImg($file));
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 导入解析excel
     */
    public function ImportExcel(Request $request)
    {
        $file = $request->file('file');
        return ApiReturn(ToolService::import($file));
    }



}

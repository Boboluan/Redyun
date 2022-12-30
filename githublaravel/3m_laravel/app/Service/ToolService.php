<?php

namespace App\Service;

use App\Exports\ProductExport;
use App\Imports\ProductTypeImport;
use App\Imports\ProductImport;//导入类
use App\Imports\MatterImport;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Exports\DataExport;//导出
use App\Exports\FeedbackExport;//导出
use Maatwebsite\Excel\Facades\Excel;

class ToolService
{


    /**
     * @param $file
     * @return array|mixed|\Symfony\Component\HttpFoundation\ParameterBag|null
     * 图片上传
     */
    public static function UploadImg($file)
    {
        $validator = Validator::make($file,[
            'file' => 'file|max:1000|mimes:jpeg,png',
        ],[
            //验证是否为文件
            'file.file' => '请确认你的头像格式',
            //验证文件上传大小
            'file.max' => '头像最大上传大小为1M',
            //验证上传文件格式
            'file.mimes' => '请确认上传为jpg或jpeg的格式图片',
        ]);
        if ($validator->fails()) {//如果有错误
            return DataReturn($validator->errors(),404); //返回得到错误
        }
        $dirPath = date('Y-m-d',time());
        $filePath =$file['file']->store($dirPath,'uploads');
        $path="/storage/uploads/".$filePath;
        if ($path){
            return DataReturn('上传成功',200,$path);
        }
        else{
            return DataReturn('上传失败',-1);
        }

    }

//  /storage/uploads/2022-04-29/8577CN P95有机蒸气异味减除功能.jpg


    /**
     * @param $file
     * @return string
     * 数据导入
     */
    public static function import($file)
    {
        //导入方法
        Excel::import(new ProductImport,$file);
        return 'success';
    }



    /**·
     * @param $data
     * @param $header
     * @param $fileName
     * @param $map
     * @return mixed
     * 导出
     */
    public static function export($data,$head,$fileName)
    {
        return Excel::download(new DataExport($data,$head),$fileName);
    }



    //反馈导出
    public static function feedbackExportData($data,$fileName)
    {
        return Excel::download(new ProductExport($data),$fileName);
    }



}

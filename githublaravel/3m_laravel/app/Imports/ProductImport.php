<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
error_reporting(0);
class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            //导入的数据
            'chinese_name'      =>$row[0],//物质中文名
            'cas'               =>$row[1],
            'english_name'      =>$row[2],
            'mac'               =>$row[3],
            'twa'               =>$row[4],
            'stel'              =>$row[5],
            'remark'            =>$row[6],
            'recommend'         =>$row[7],
            'masktype_one'      =>$row[8],
            'facemasktype_one'  =>$row[9],
            'filterbox_one'     =>$row[10],
            'filtercover_one'   =>$row[11],
            'cottonfilter_one'  =>$row[12],
            'facemasktype_two'  =>$row[13],
            'filterbox_two'     =>$row[14],
            'filtercover_two'   =>$row[15],
            'cottonfilter_two'  =>$row[16],
            'facemasktype_three'=>$row[17],
            'filterbox_three'   =>$row[18],
            'filtercover_three' =>$row[19],
            'cottonfilter_three'=>$row[20]
        ]);
    }
}

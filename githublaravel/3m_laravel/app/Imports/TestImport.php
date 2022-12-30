<?php

namespace App\Imports;

use App\Models\Test;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TestImport implements ToCollection
{
    /**
     * 测试导入
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        //去除标题
        unset($collection[0]);
        //循环添加
        foreach ($collection as $row){
            Test::create([
                'chinese_name'=>$row[1],
                'cas'=>$row[2],
                'english_name'=>$row[3]
            ]);
        }
    }

}

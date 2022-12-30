<?php

namespace App\Imports;

use App\Models\Matter;

use Maatwebsite\Excel\Concerns\ToModel;

class MatterImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Matter
     */
    public function model(array $row)
    {
        return new Matter([
            //导入的数据
            'chinese_name'      =>$row[0],//物质中文名
            'cas'               =>$row[1],
            'english_name'      =>$row[2],
        ]);
    }
}

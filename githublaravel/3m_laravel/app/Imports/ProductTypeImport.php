<?php

namespace App\Imports;

use App\Models\ProductType;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductTypeImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new ProductType([
            //导入的数据
            'product_type'      =>$row[0],
            'buy_link'          =>$row[1],
            'stock'             =>$row[2],
        ]);
    }
}

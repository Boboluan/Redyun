<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;//指定使用集合结构
use Maatwebsite\Excel\Concerns\FromArray;  // 指定使用数组结构
use Maatwebsite\Excel\Concerns\WithMapping; // 设置excel中每列要展示的数据
use Maatwebsite\Excel\Concerns\WithHeadings; // 设置excel的首行对应的表头信息


class DataExport implements FromArray, WithHeadings
{

    protected $data;

    protected $head;

    public function __construct(array $data,$head)
    {
        $this->data = $data;
        $this->head = $head;
    }


    /**
     * 返回的数据
     * @return array
     */
    public function array():array
    {
        return $this->data;
    }

    /**
     * @return array
     * 头部
     */
    public function headings():array
    {
        return $this->head;
    }

}

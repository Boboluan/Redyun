<?php

namespace App\Service;

use App\Models\NavListModel;

class IndexService
{
    /**
     * @return array
     * 首页导航
     */
    public static function NavList()
    {
        $List = NavListModel::where('pid',0)->get()->toArray();
        foreach ($List as $key=>&$item){
            $Son = NavListModel::where('pid',$item['id'])->get()->toArray();
            foreach($Son as $k =>&$value){
                if(!empty($value)){
                    $grandson = NavListModel::where('pid',$value['id'])->get()->toArray();
                }
            }
            $List[$key]['son'] = $Son;
            $Son[$k]['grandson'] = $grandson;
        }
        return $List;
    }



}
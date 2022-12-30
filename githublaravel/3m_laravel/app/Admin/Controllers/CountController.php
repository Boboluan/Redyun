<?php



namespace App\Admin\Controllers;

use App\Models\Matter;
use App\Models\ObjectCount;

use App\Models\Product;
use App\Models\ProductType;
use App\Service\ExportOffice;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Service\ToolService;



class CountController extends AdminBaseController

{



    /**
     * @return array|\think\response\View
     *
     */
    public function CountList()
    {

        if(request()->isMethod('post')){

            $map = [];

            $Params = PostData();

            if(!empty($Params['start_time']) && isset($Params['start_time']) ){

                $map[] = ['create_time','>=',strtotime($Params['start_time'])];

            }

            if(!empty($Params['end_time']) && isset($Params['end_time'])){

                $map[] = ['create_time','<=',strtotime($Params['end_time'])];

            }

            $List = ObjectCount::query()->where($map)->orderBy('create_time','desc')->get()->toArray();

            foreach ($List as &$datum){

                $datum['create_time'] = date('Y-m-d H:s',$datum['create_time']);

                switch ($datum['action']){

                    case 'choose':
                        $datum['action'] = '选择';
                        break;

                    case 'search':
                        $datum['action'] = '搜索';
                        break;

                    case 'buy':
                        $datum['action'] = '购买';
                        break;

                }

                switch ($datum['object_type']){

                    case 'matter':
                        $datum['object_type'] = '物质';
                        break;

                    case 'product':
                        $datum['object_type'] = '商品';
                        break;
                }

            }

            $Count = ObjectCount::query()->where($map)->count();

            return ['data'=>$List,'code'=>0,'msg'=>'','count'=>$Count];
        }

        return  view('count.index');
    }







    /**
     * @param $Params
     * @return array|string
     * 导出方法
     */

    public static function ObjectDataExport()
    {
        $Params = GetData();
        $map = [];
        if(!empty($Params['start_time']) && isset($Params['start_time'])){

            $map[] = ['create_time','>=',strtotime($Params['start_time'])];
        }

        if(!empty($Params['end_time']) && isset($Params['end_time'])){

            $map[] = ['create_time','<=',strtotime($Params['end_time'])];
        }
        //pv uv 总统计
        $token = ObjectCount::query()->where($map)->pluck('user_token')->toArray();
        $pv = ObjectCount::query()->where($map)->count();
        $uv = count(array_unique($token));
        //$List = self::dataTreating($map);
        //return ToolService::export($List['data'],$List['header'],$List['fileName']);//使用Maatwebsite导出
        //return $ExportOffice->exportList($List['header'],$List['data'],$List['fileName']);//传统php office 导出

        //所有物质总数据
        $allMatter = Product::query()->get()->pluck('chinese_name')->toArray();
        //物质数据
        $object_matter_name = ObjectCount::query()->where($map)->where(['object_type'=>'matter'])->pluck('object_name')->toArray();
        $object_matter_name = array_unique($object_matter_name);
        //筛选出未被记录的数据并合并数据
        foreach ($object_matter_name as &$vk)
        {
            $index = array_search($vk,$allMatter);
            if(isset($index)){
                unset($allMatter[$index]);
            }
        }
        $object_matter_name = array_merge($object_matter_name,$allMatter);

        $List['matter'] = [];
        foreach ($object_matter_name as $key => $value){
            $List['matter'][$key]['pv'] = ObjectCount::query()->where(['object_name'=>$value])->count();
            $token_arr = ObjectCount::query()->where(['object_name'=>$value])->pluck('user_token')->toArray();
            $List['matter'][$key]['uv'] = count(array_unique($token_arr));
            $List['matter'][$key]['name'] = $value;
        }

        //所有产品总数据
        $allProduct = ProductType::query()->get()->pluck('product_type')->toArray();
        //产品数据
        $object_product_name = ObjectCount::query()->where($map)->where(['object_type'=>'product'])->pluck('object_name')->toArray();
        $object_product_name = array_unique($object_product_name);

        //筛选出未被记录的数据并合并数据
        foreach ($object_product_name as &$vc)
        {
            $index = array_search($vc,$allProduct);
            if(isset($index)){
                unset($allProduct[$index]);
            }
        }
        $object_product_name = array_merge($object_product_name,$allProduct);

        $List['product'] = [];
        foreach ($object_product_name as $k => $item){
            $List['product'][$k]['choose_pv'] = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'choose'])->count();
            $token_arr = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'choose'])->pluck('user_token')->toArray();
            $List['product'][$k]['choose_uv'] = count(array_unique($token_arr));

            $List['product'][$k]['buy_pv'] = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'buy'])->count();
            $token_arr = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'buy'])->pluck('user_token')->toArray();
            $List['product'][$k]['buy_uv'] = count(array_unique($token_arr));

            $List['product'][$k]['addcart_pv'] = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'addcart'])->count();
            $token_arr = ObjectCount::query()->where(['object_name'=>$item])->where(['action'=>'addcart'])->pluck('user_token')->toArray();
            $List['product'][$k]['addcart_uv'] = count(array_unique($token_arr));
            $List['product'][$k]['name'] = $item;
        }
        $ExportOffice = new ExportOffice();
        if(empty($Params['start_time'])){
            $Params['start_time'] = '2022-01-01';
        }
        if(empty($Params['end_time'])){
            $Params['end_time'] = date("Y-m-d",time());
        }
        $timeString = $Params['start_time'].'至'. $Params['end_time'];
        return $ExportOffice->getCsv('',$List,1,$timeString,$pv,$uv);
    }





    /**
     * @param $Params
     * @return array
     * 导出数据查询
     */
    public static function dataTreating($Where = [])
    {
        $List  = [];

        $data = ObjectCount::query()->where($Where)->orderBy('create_time','desc')->get();

        empty($data)?$data = []:$data = $data->toArray();

        foreach ($data as &$datum){

            $datum['create_time'] = date('Y-m-d H:i', $datum['create_time']);

            switch ($datum['action']){

                case 'choose':
                    $datum['action'] = '选择';
                    break;

                case 'search':
                    $datum['action'] = '搜索';
                    break;

                case 'buy':
                    $datum['action'] = '购买';
                    break;
            }

            switch ($datum['object_type']){

                case 'matter':
                    $datum['object_type'] = '物质';
                    break;

                case 'product':
                    $datum['object_type'] = '商品';
                    break;
            }
        }

        $header = ['序号','对象类别','对象名称','用户令牌','行为','记录时间'];

        $fileName = '物质及商品搜索点击统计'.date('Y-m-d',time()).'.csv';

        $List['data'] = $data;

        $List['header'] = $header;

        $List['fileName'] = $fileName;

        return $List;
    }















}


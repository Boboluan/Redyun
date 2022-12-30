<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ObjectCount;

use Illuminate\Http\Request;

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

            $List = ObjectCount::query()->where($map)->get()->toArray();

            foreach ($List as &$datum){

                $datum['create_time'] = date('Y-m-d H:s',$datum['create_time']);

                switch ($datum['action']){
                    case 'choose':
                        $datum['action'] = '选择';
                        break;

                    case 'buy':
                        $datum['action'] = '购买';
                        break;

                    case 'search':
                        $datum['action'] = '搜索';
                        break;
                }

                if($datum['object_type']=='matter'){

                    $datum['object_type']= '物质';
                }else{

                    $datum['object_type']= '商品';
                }

            }

            $Count = ObjectCount::query()->where($map)->count();

            return ['data'=>$List,'code'=>0,'msg'=>'','count'=>$Count];

        }

        return  view('count.index');
    }







    /**

     * @param $Params

     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\think\response\Json

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

        $List = self::dataTreating($map);

        return ToolService::export($List['data'],$List['header'],$List['fileName']);

    }





    /**

     * @param $Params

     * @return array

     * 导出数据查询
     *
     */

    public static function dataTreating($Where = [])
    {

        $List  = [];

        $data = ObjectCount::query()->where($Where)->get();

        empty($data)?$data = []:$data = $data->toArray();

        foreach ($data as &$datum){
            switch ($datum['action']){
                case 'choose':
                    $datum['action'] = '选择';
                    break;

                case 'buy':
                    $datum['action'] = '购买';
                    break;

                case 'search':
                    $datum['action'] = '搜索';
                    break;
            }

            if($datum['object_type']=='matter'){

                $datum['object_type']= '物质';
            }else{

                $datum['object_type']= '商品';
            }

        }
        $header = ['序号','对象类别','对象名称','对象编号','用户令牌','行为','搜索时间'];

        $fileName = '物质及商品搜索点击统计'.date('Y-m-d',time()).'.csv';

        $List['data'] = $data;

        $List['header'] = $header;

        $List['fileName'] = $fileName;

        return $List;

    }



}


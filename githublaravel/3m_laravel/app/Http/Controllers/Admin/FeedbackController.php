<?php



namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Feedback;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Service\ToolService;



class FeedbackController extends AdminBaseController
{

    /**
     * @return array|\think\response\View
     *
     */
    public function FeedbackList()
    {

        if(request()->isMethod('post')){

            $map = [];

            $Params = PostData();

            if(!empty($Params['start_time']) && isset($Params['start_time'])){

                $map[] = ['create_time','>=',strtotime($Params['start_time'])];
            }

            if(!empty($Params['end_time']) && isset($Params['end_time'])){

                $map[] = ['create_time','<=',strtotime($Params['end_time'])];
            }

            $List = Feedback::query()->where($map)->get()->toArray();
//            dump($List);die();
            foreach ($List as &$item){

                $item['create_time'] = date('Y-m-d H:s',$item['create_time']);
            }

            $Count = Feedback::query()->where($map)->count();

            return ['data'=>$List,'code'=>0,'msg'=>'','count'=>$Count];
        }
        
        return  view('feedback.index');

    }







    /**

     * @return array

     * 删除数据

     */

    public function FeedbackDelete()
    {

        $params = PostData();

        if(isset($params['ids']) && !empty($params['ids'])){

            $delete = Feedback::whereIn('id',$params['ids'])->delete();

        }else{

            $delete = Feedback::where(['id'=>$params['feedback_id']])->delete();

        }

        return $delete ? DataReturn('删除成功',200):DataReturn('删除失败',-1);

    }





    /**

     * @return mixed

     * 反馈数据导出

     */

    public function FeedExportData()

    {
        $Params = GetData();
        $map = [];
        if(!empty($Params['start_time']) && isset($Params['start_time'])){

            $map[] = ['create_time','>=',strtotime($Params['start_time'])];
        }
        if(!empty($Params['end_time']) && isset($Params['end_time'])){

            $map[] = ['create_time','<=',strtotime($Params['end_time'])];
        }
        $data = Feedback::where($map)->get()->toArray();

        foreach ($data as &$item){$item['create_time'] = date('Y-m-d H:s',$item['create_time']);}

        $head = [ '序号','标题',  '内容','联系方式','姓名','反馈时间'];

        $fileName = '反馈意见'.date('Y-m-d',time()).'.csv';

        return ToolService::export($data,$head,$fileName);

        //测试
//        return  ToolService::feedbackExportData($data,$fileName);

    }





}


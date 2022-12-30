<?php



namespace App\Api\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Feedback;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Validator;



class FeedbackController extends Controller

{



    /**

     * @return \think\response\Json

     * 提交反馈

     */

    public function feedback()

    {

        $Params = PostData();

//        $rule = [
//
//            //验证字段
//
//            'content'=>'required',
//
//            'phone'=>'required',
//
//        ];
//
//        $message = [
//
//            'content.required'=>'反馈内容必须',
//
//            'phone.required'=>'联系方式必须',
//
//        ];
//
//        $Validate = Validator::make($Params,$rule,$message);
//
//        if($Validate->fails())  return $Validate->errors();

        $Add  = Feedback::insert([

            'title'  =>$Params['title']??'',

            'content'=>$Params['content']??'',

            'name'   =>$Params['name']??'',

            'phone'  =>$Params['phone']??'',

            'create_time' =>time(),

        ]);

        return $Add ? ApiReturn(DataReturn('反馈提交成功',0)):ApiReturn(DataReturn('反馈提交失败，稍后重试',-1));

    }





}


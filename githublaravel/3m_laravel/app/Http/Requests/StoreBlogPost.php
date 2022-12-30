<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //验证字段
            'username'=>'require|max:20',
            'password'=>'require|min:6|confirmed',
        ];
    }


    public function message()
    {
        return[
            'username.require'=>'用户名不能为空',
            'password.require'=>'密码不能为空',
        ];
    }


    public function scene()
    {
        return[

        ];
    }




}

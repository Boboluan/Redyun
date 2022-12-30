<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\ProductType;

use App\Service\JdService;

use App\Service\ProductService as product;

use Illuminate\Support\Facades\Redirect;



class ProductController extends AdminBaseController

{



    /**

     * @return array

     * 物质数据列表

     */

    public function listData()

    {

        $list = product::List(PostData());

        return $list;

    }







    /**

     * @return

     * 后台商品列表数据

     */

    public function ProductListPage()

    {

        if(request()->isMethod('post')){

            return product::ProductListPost(PostData());
        }

        return  view('adminProduct.index');
    }







    /**

     * @return

     * 添加商品

     */

    public function AddProduct()

    {

        if(request()->isMethod('post')){

            $params = PostData();

            unset($params['file']);

            return ApiReturn(product::addProductType($params));

        }

        return view('adminProduct.add');

    }





    /**

     * @return

     * 更新商品

     */

    public function EditProduct()

    {

        if(request()->isMethod('post')){

            return ApiReturn(product::editProductType(PostData()));

        }

//        dump(product::productData(GetData()));

        return view('adminProduct.edit',['data'=>product::productData(GetData())]);

    }





    /**

     * @return \Illuminate\Http\JsonResponse

     * 商品删除

     */

    public function DeleteProduct()

    {

        return ApiReturn(product::delProductType(PostData()));

    }





    /**

     * @return \Illuminate\Http\JsonResponse

     * 商品批量删除

     */

    public function DeleteAllProduct()

    {

        $ids = PostData('post.ids');

        return ApiReturn(product::delAll($ids));

    }



    /**

     * @return

     * 物质列表页面

     */

    public function MatterListPage()

    {

        if(loginCheck()===false)  return Redirect::to('/admin/Logout');

        return view('matter.index');

    }



    /**

     * @return array

     * 物质列表数据

     */

    public function MatterList()

    {

        $params = GetData();

        return product::MList($params);

    }





    /**

     * @return

     * 添加物质

     */

    public function AddMatter()

    {

        if(request()->isMethod('post')){

            return ApiReturn(product::addMatter(PostData()));

        }

        return view('matter.add',['data'=>product::ChooseProduct()]);

    }





    /**

     * @return

     * 修改物质

     */

    public function EditEMatter()

    {

        if(request()->isMethod('post')){

            return ApiReturn(product::editMatter(PostData()));

        }

        $params = PostData();

        return view('matter.edit',[

            'choose' =>product::ChooseProduct(),

            'data'   =>product::Relation($params)['data']]);

    }





    /**

     * @return \Illuminate\Http\JsonResponse

     * 删除物质

     */

    public function DelMatter()

    {

        return ApiReturn(product::delMatter(PostData()));

    }





    /**

     * @return \Illuminate\Http\JsonResponse

     * 批量删除物质

     */

    public function delAllMatter()

    {

        return ApiReturn(product::delManyMatter(PostData()));

    }





    /**

     * @return \Illuminate\Http\JsonResponse

     * 物质关系

     */

    public function MatterRelotion()

    {

        return ApiReturn(product::Relation(PostData()));

    }





    /**

     * @return \think\response\View

     * 测试页面

     */

    public function Test()

    {

        return view('test');

    }





    /**

     * @return mixed

     * 下载

     */

    public function Export()

    {

        return product::DataExport(GetData());

    }







}


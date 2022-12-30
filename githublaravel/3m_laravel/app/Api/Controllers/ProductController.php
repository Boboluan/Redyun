<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Service\JdService;
use App\Service\ProductService as product;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{

    /**
     * @return array
     *
     * 物质数据列表
     */
    public function listData()
    {
        $list = product::List(PostData());
        return $list;
    }



    /**
     * @return array
     * 搜索跟进详情
     */
    public function Detail()
    {
        $list = product::detail(PostData());
        return $list;
    }



    /**
     * @return array
     * 名称接口
     */
    public function ProductName()
    {
        $list = product::ProductNames();
        return $list;
    }


    /**
     * @return array
     * 名称处理
     */
    public function ProductNamedispose()
    {
        $list = product::ProductNames1();
        return $list;
    }


    /**
     *
     * 数据处理
     */
    public function DataDispose()
    {
        $params = PostData();
        $list = product::Dispose($params);
        return $list;
    }



    /**
     * 产品规格详情
     */
    public function ProductDetail()
    {
        $list = product::ProductDetail(PostData());
        return $list;
    }



    /**
     * 输出产品信息
     *
     */
    public function Information()
    {
        $params = PostData();
        $list = product::ProductInfo($params);
        return $list;
    }


    /**
     * @return \think\response\View
     */
    public function Informationpage()
    {
        return view('product.index');
    }


    /**
     * @return array
     * 查找id
     */
    public function getid()
    {
        $params = PostData();
        $list = product::getids($params);
        return $list;
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
     * @param string $sku
     * 测试方法：获取商品价格
     */
    public function price()
    {
        $sku = PostData()['sku'];
        $price = new JdService();
        $result = $price->ProductPrice($sku);
        if(empty(object_array($result)['jingdong_jdprices_get_responce'])){
            $code = object_array($result)['error_response']['code'];
            $data =  DataReturn('api调用失败',$code);
        }else{
            $return = object_array($result)['jingdong_jdprices_get_responce']['returnType']['skuPriceInfoResponseList'][0]['priceResult'];
            $data['sku'] = object_array($result) ['jingdong_jdprices_get_responce']['returnType']['skuPriceInfoResponseList'][0]['skuId'];
            $data['jdprice'] = $return['jdPrice'];//前台京东价
            $data['originalPrice'] = $return['originalPrice'];//后台京东价
            $data['marketPrice'] = $return['marketPrice'];//市场价
        }
        return $data;
    }



    /**
     * @return \think\response\Json
     *
     * 改变没有商品的物质的状态
     */
    public function ChangeMatter()
    {
        return ApiReturn(product::ChangeMatter());
    }


    /*点击商品统计点击量*/
    public function ProductStat()
    {
        return ApiReturn(product::ProductStatistics(PostData()));
    }


}

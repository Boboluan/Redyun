<?php

namespace App\Service;

use App\Models\Product;
use App\Models\ProductName;
use App\Models\ProductRelation;
use App\Models\ProductType;
use App\Models\Matter;
use App\Models\ObjectCount;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Service\TokenService;


class ProductService
{

    /**
     * @param array $Params
     * @return array
     * 用例
     */
    public static function List(array $Params)
    {
        $map = [];
        if(isset($Params['chinese_name']) && !empty($Params['chinese_name'])) $map[] = ['chinese_name','like',"%".$Params['chinese_name']."%"];
        if(isset($Params['english_name']) && !empty($Params['english_name'])) $map[] = ['english_name','like',"%".$Params['english_name']."%"];
        if(isset($Params['cas']) && !empty($Params['cas'])) $map[] = ['cas','like',"%".$Params['cas']."%"];
        $column = ['id','chinese_name','cas','english_name','mac','twa','stel','remark','recommend','masktype_one'];
        $List = Product::query()->where($map)->where(['status'=>1])->get($column)->toArray();
        //记录
        $creat_token =null;
        if(empty($Params['token'])){
            $TokenService = new TokenService();
            $creat_token = $TokenService->definedToken();
        }else{
            $token = $Params['token'];
            $firstProduct = $List[0];
            self::MatterStatistics($firstProduct,$token);
        }
        foreach ($List as &$value){
            if($value['masktype_one']=='|' || empty($value['masktype_one']) || $value['masktype_one']=='/'){
                $value['is_mask'] = 'false';
            }else{
                $value['is_mask'] = 'true';
            }
        }
        return DataReturnToken('获取数据成功',0,$List,$creat_token);
    }



    /**
     * @param array $Params
     * @return array
     * 搜索详情
     */
    public static function detail(array $Params)
    {

        $column = ['id','chinese_name','cas','english_name','mac','twa','stel','remark','recommend','masktype_one'];
        $ProductInfo = Product::query()->where(['id'=>$Params['product_id']])->get($column)->toArray()[0];
        if(empty($ProductInfo)){
            return DataReturn('此物质不存在',-1);
        }
        if($ProductInfo['masktype_one']=='|' || empty($ProductInfo['masktype_one']) || $ProductInfo['masktype_one']=='/'){

            $maskType = 'false';

        }else{

            $maskType = 'true';
        }
        $ProductInfo['recommend'] = explode(" ", $ProductInfo['recommend']);
        $ProductInfo['maskType'] = $maskType;

        $ProductInfo['productList'] = self::ProductDetail($Params['product_id'],$maskType);
        return DataReturn('获取数据成功',0,$ProductInfo);
    }




    /**
     * @return array
     * 名稱處理
     */
    public static function ProductNames2()
    {
        $Data = [];
        $Data['chinese_name'] = [];
        $Data['english_name'] = [];
        $Data['cas'] = [];
        $column = ['id','chinese_name','english_name','cas'];
        $Name = Product::query()->get($column)->toArray();
        foreach ($Name as $key =>&$value){
            array_push($Data['chinese_name'],trim($value['chinese_name']));

            $engnameone = explode(',',$value['english_name']);

            foreach ($engnameone as $vv){
                $engnametwo = explode('，',$vv);
                foreach ($engnametwo as $vvv){
                    if(!empty($vvv) && $vvv != ' '){
                        array_push($Data['english_name'],trim($vvv));
                    }
                }
            }
            //cas号码
            if(!empty($value['cas'])){
                $casone = explode('/',$value['cas']);
                foreach ($casone as $vv){
                    $castwo = explode('；',$vv);
                    foreach ($castwo as $vvv){
                        if(!empty($vvv) && $vvv != ' '){
                            array_push($Data['cas'],trim($vvv));
                        }
                    }
                }
            }
        }
        return $Data;
    }




    /**
     * @return array|string
     * 名稱處理
     */
    public static function ProductNames1()
    {
        $Data = [];
        $Data['chinese_name'] = [];
        $Data['english_name'] = [];
        $Data['cas'] = [];
        $column = ['id','chinese_name','cas','english_name'];
        $Name = Product::where(['status'=>1])->get($column)->toArray();
        foreach ($Name as $key =>&$value){
            //物质中文名称
            array_push($Data['chinese_name'],trim($value['chinese_name']));
            //物质英文名称处理
            $EnglishName = explode(",",$value['english_name']);
            foreach ($EnglishName as $k=>&$item){
                $EnglishNameTwo = explode('，',$item);
                foreach ($EnglishNameTwo as &$vv){
                    if($vv!=' ' && $vv!=''){
                        array_push($Data['english_name'],trim($vv));
                    }
                }
            }

            //cas号处理
            $Cas =  explode("/",$value['cas']);
            foreach ($Cas as $ca){
                $CasTwo = explode('；',$ca);
                foreach ($CasTwo as $cat){
                    if(!empty($cat)&&$cat!=''&&$cat!=' '){
                        array_push($Data['cas'],$cat);
                    }
                }
            }
        }
        return self::ProductNameLast($Data);
    }


    /**
     * @param $data
     * @return string
     * 最终处理结果
     */
    public static function ProductNameLast($data)
    {
        $chinese =  $data['chinese_name'];
        $english =  $data['english_name'];
        $cas     =  $data['cas'];
        //中文名称写入
        foreach ($chinese as$ch){
            $ch_array = [
                'chinese_name'=>$ch
            ];
            $query = ProductName::insert($ch_array);
        }
        //英文名称写入
        foreach ($english as &$en){
            $en_array = [
                'english_name'=>$en
            ];
            $query = ProductName::insert($en_array);
        }
        //cas写入
        foreach ($cas as &$ca){
            $ca_array = [
                'cas'=>$ca,
            ];
            $query = ProductName::insert($ca_array);
        }
        return $query ? 'success':'error';
    }



    public static function ProductNames()
    {
        $data = [];
        $data['chinese_name'] = [];
        $data['english_name'] = [];
        $data['cas'] = [];
        $list = ProductName::query()->get()->toArray();
        foreach ($list as &$val){

            if(!empty($val['chinese_name'])){
                array_push($data['chinese_name'],$val['chinese_name']);
            }

            if(!empty($val['english_name'])){
                array_push($data['english_name'],$val['english_name']);
            }
            if(!empty($val['cas'])){
                array_push($data['cas'],$val['cas']);
            }
        }
        return $data;
    }


    /**
     * @param array $Params
     * @return array
     * 产品分组数据
     */
    public static function ProductDetail($Product_id,$maskType)
    {
        $Data = [];
        if ($maskType == 'true') {
            $Info = ProductRelation::query()->where(['product_id' => $Product_id, 'series' => 'mask'])->get()->toArray();
            foreach ($Info as $key=>&$item) {
                $Mask = ProductType::query()->where(['id' => $item['product_type_id']])->get()->toArray();
                $Data['mask'][$key]['images'] = http_type().$Mask[0]['images'];
                $Data['mask'][$key]['othername'] = $Mask[0]['othername'];
                $Data['mask'][$key]['spec'] = $Mask[0]['spec'];
                $Data['mask'][$key]['product_type'] = $Mask[0]['product_type'];
                $Data['mask'][$key]['product_id'] = $Mask[0]['id'];
                $Data['mask'][$key]['buy_link'] = $Mask[0]['buy_link'];
                if(is_numeric($Mask[0]['buy_link']) && !empty($Mask[0]['buy_link'])){
                    $Data['mask'][$key]['JdInfo'] = self::getJdPrice($Mask[0]['buy_link']);
                }else{
                    $Data['mask'][$key]['JdInfo'] = false;
                }
            }

        } else {

            //第一組
            //面罩
            $ALL = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 1, 'series' => 'facemasktype'])->get()->toArray();

            if (!empty($ALL)) {

                foreach ($ALL as $keyy => &$datum) {

                    $facemasktype = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($facemasktype[0]['buy_link']=='暂时无货，敬请期待'){
                        $facemasktype[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($facemasktype[0]['buy_link']) && !empty($facemasktype[0]['buy_link'])){
                        $Data['facemasktype_one'][$keyy]['JdInfo'] = self::getJdPrice($facemasktype[0]['buy_link']);
                    }else{
                        $Data['facemasktype_one'][$keyy]['JdInfo'] = false;
                    }
                    $Data['facemasktype_one'][$keyy]['othername'] = $facemasktype[0]['othername'];
                    $Data['facemasktype_one'][$keyy]['product_id'] = $facemasktype[0]['id'];
                    $Data['facemasktype_one'][$keyy]['spec'] = $facemasktype[0]['spec'];
                    $Data['facemasktype_one'][$keyy]['buy_link'] = $facemasktype[0]['buy_link'];
                    $Data['facemasktype_one'][$keyy]['images'] = http_type() . $facemasktype[0]['images'];
//                    $Data['facemasktype_one'][$keyy]['stock'] = $facemasktype[0]['stock'];
                    $Data['facemasktype_one'][$keyy]['product_type'] = $facemasktype[0]['product_type'];
                }
            }else{
                $Data['facemasktype_one'] = [];
            }

            //滤毒盒
            $ALL1 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 1, 'series' => 'filterbox'])->get()->toArray();
            if (!empty($ALL1)) {

                foreach ($ALL1 as $keyy => &$datum) {

                    $filterbox = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filterbox[0]['buy_link']=='暂时无货，敬请期待'){
                        $filterbox[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filterbox[0]['buy_link']) && !empty($filterbox[0]['buy_link'])){
                        $Data['filterbox_one'][$keyy]['JdInfo'] = self::getJdPrice($filterbox[0]['buy_link']);
                    }else{
                        $Data['filterbox_one'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filterbox_one'][$keyy]['spec'] = $filterbox[0]['spec'];
                    $Data['filterbox_one'][$keyy]['othername'] = $filterbox[0]['othername'];
                    $Data['filterbox_one'][$keyy]['buy_link'] = $filterbox[0]['buy_link'];
                    $Data['filterbox_one'][$keyy]['images'] = http_type() . $filterbox[0]['images'];
//                    $Data['filterbox_one'][$keyy]['stock'] = $filterbox[0]['stock'];
                    $Data['filterbox_one'][$keyy]['product_type'] = $filterbox[0]['product_type'];
                    $Data['filterbox_one'][$keyy]['product_id'] = $filterbox[0]['id'];

                }
            }else{
                $Data['filterbox_one'] = [];
            }

            //过滤棉盖
            $ALL2 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 1, 'series' => 'filtercover'])->get()->toArray();
            if (!empty($ALL2)) {

                foreach ($ALL2 as $keyy => &$datum) {
                    $filtercover = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filtercover[0]['buy_link']=='暂时无货，敬请期待'){
                        $filtercover[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filtercover[0]['buy_link']) && !empty($filtercover[0]['buy_link'])){
                        $Data['filtercover_one'][$keyy]['JdInfo'] = self::getJdPrice($filtercover[0]['buy_link']);
                    }else{
                        $Data['filtercover_one'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filtercover_one'][$keyy]['spec'] = $filtercover[0]['spec'];
                    $Data['filtercover_one'][$keyy]['othername'] = $filtercover[0]['othername'];
                    $Data['filtercover_one'][$keyy]['buy_link'] = $filtercover[0]['buy_link'];
                    $Data['filtercover_one'][$keyy]['images'] = http_type() . $filtercover[0]['images'];
//                    $Data['filtercover_one'][$keyy]['stock'] = $filtercover[0]['stock'];
                    $Data['filtercover_one'][$keyy]['product_type'] = $filtercover[0]['product_type'];
                    $Data['filtercover_one'][$keyy]['product_id'] = $filtercover[0]['id'];
                }
            }else{

                $Data['filtercover_one'] = [];
            }

            //或滤棉
            $ALL3 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 1, 'series' => 'cottonfilter'])->get()->toArray();
//            dump($ALL3);die();
            if (!empty($ALL3)) {

                foreach ($ALL3 as $keyy => &$datum) {
                    $cottonfilter = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($cottonfilter[0]['buy_link']=='暂时无货，敬请期待'){
                        $cottonfilter[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($cottonfilter[0]['buy_link']) && !empty($cottonfilter[0]['buy_link'])){
                        $Data['cottonfilter_one'][$keyy]['JdInfo'] = self::getJdPrice($cottonfilter[0]['buy_link']);
                    }else{
                        $Data['cottonfilter_one'][$keyy]['JdInfo'] = false;
                    }
                    $Data['cottonfilter_one'][$keyy]['spec'] = $cottonfilter[0]['spec'];
                    $Data['cottonfilter_one'][$keyy]['othername'] = $cottonfilter[0]['othername'];
                    $Data['cottonfilter_one'][$keyy]['buy_link'] = $cottonfilter[0]['buy_link'];
                    $Data['cottonfilter_one'][$keyy]['images'] = http_type() . $cottonfilter[0]['images'];
//                    $Data['cottonfilter_one'][$keyy]['stock'] = $cottonfilter[0]['stock'];
                    $Data['cottonfilter_one'][$keyy]['product_type'] = $cottonfilter[0]['product_type'];
                    $Data['cottonfilter_one'][$keyy]['product_id'] = $cottonfilter[0]['id'];
                }

            }else{
                $Data['cottonfilter_one'] = [];
            }

            //-------------------------------------------------------------------------------------------------------------------
            //第二组
            //面罩
            $ALL = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 2, 'series' => 'facemasktype'])->get()->toArray();

            if (!empty($ALL)) {

                foreach ($ALL as $keyy => &$datum) {
                    $facemasktype = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($facemasktype[0]['buy_link']=='暂时无货，敬请期待'){
                        $facemasktype[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($facemasktype[0]['buy_link']) && !empty($facemasktype[0]['buy_link'])){
                        $Data['facemasktype_two'][$keyy]['JdInfo'] = self::getJdPrice($facemasktype[0]['buy_link']);
                    }else{
                        $Data['facemasktype_two'][$keyy]['JdInfo'] = false;
                    }
                    $Data['facemasktype_two'][$keyy]['spec'] = $facemasktype[0]['spec'];
                    $Data['facemasktype_two'][$keyy]['othername'] = $facemasktype[0]['othername'];
                    $Data['facemasktype_two'][$keyy]['buy_link']  = $facemasktype[0]['buy_link'];
                    $Data['facemasktype_two'][$keyy]['images'] = http_type() . $facemasktype[0]['images'];
//                    $Data['facemasktype_two'][$keyy]['stock'] = $facemasktype[0]['stock'];
                    $Data['facemasktype_two'][$keyy]['product_type'] = $facemasktype[0]['product_type'];
                    $Data['facemasktype_two'][$keyy]['product_id'] = $facemasktype[0]['id'];
                }
            }else{

                $Data['facemasktype_two'] = [];
            }


            $ALL1 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 2, 'series' => 'filterbox'])->get()->toArray();
            if (!empty($ALL1)) {

                foreach ($ALL1 as $keyy => &$datum) {

                    $filterbox = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filterbox[0]['buy_link']=='暂时无货，敬请期待'){
                        $filterbox[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filterbox[0]['buy_link']) && !empty($filterbox[0]['buy_link'])){
                        $Data['filterbox_two'][$keyy]['JdInfo'] = self::getJdPrice($filterbox[0]['buy_link']);
                    }else{
                        $Data['filterbox_two'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filterbox_two'][$keyy]['spec'] = $filterbox[0]['spec'];
                    $Data['filterbox_two'][$keyy]['othername'] = $filterbox[0]['othername'];
                    $Data['filterbox_two'][$keyy]['buy_link'] = $filterbox[0]['buy_link'];
                    $Data['filterbox_two'][$keyy]['images'] = http_type() . $filterbox[0]['images'];
//                    $Data['filterbox_two'][$keyy]['stock'] = $filterbox[0]['stock'];
                    $Data['filterbox_two'][$keyy]['product_type'] = $filterbox[0]['product_type'];
                    $Data['filterbox_two'][$keyy]['product_id'] = $filterbox[0]['id'];
                }
            }else{
                $Data['filterbox_two'] = [];
            }

            $ALL2 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 2, 'series' => 'filtercover'])->get()->toArray();
            if (!empty($ALL2)) {

                foreach ($ALL2 as $keyy => &$datum) {
                    $filtercover = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filtercover[0]['buy_link']=='暂时无货，敬请期待'){
                        $filtercover[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filtercover[0]['buy_link']) && !empty($filtercover[0]['buy_link'])){
                        $Data['filtercover_two'][$keyy]['JdInfo'] = self::getJdPrice( $filtercover[0]['buy_link']);
                    }else{
                        $Data['filtercover_two'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filtercover_two'][$keyy]['spec'] = $filtercover[0]['spec'];
                    $Data['filtercover_two'][$keyy]['othername'] = $filtercover[0]['othername'];
                    $Data['filtercover_two'][$keyy]['buy_link'] = $filtercover[0]['buy_link'];
                    $Data['filtercover_two'][$keyy]['images'] = http_type() . $filtercover[0]['images'];
//                    $Data['filtercover_two'][$keyy]['stock'] = $filtercover[0]['stock'];
                    $Data['filtercover_two'][$keyy]['product_type'] = $filtercover[0]['product_type'];
                    $Data['filtercover_two'][$keyy]['product_id'] = $filtercover[0]['id'];
                }
            }else{
                $Data['filtercover_two'] = [];
            }


            $ALL3 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 2, 'series' => 'cottonfilter'])->get()->toArray();
            if (!empty($ALL3)) {

                foreach ($ALL3 as $keyy => &$datum) {
                    $cottonfilter = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($cottonfilter[0]['buy_link']=='暂时无货，敬请期待'){
                        $cottonfilter[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($cottonfilter[0]['buy_link']) && !empty($cottonfilter[0]['buy_link'])){
                        $Data['cottonfilter_two'][$keyy]['JdInfo'] = self::getJdPrice($cottonfilter[0]['buy_link']);
                    }else{
                        $Data['cottonfilter_two'][$keyy]['JdInfo'] = false;
                    }
                    $Data['cottonfilter_two'][$keyy]['spec'] = $cottonfilter[0]['spec'];
                    $Data['cottonfilter_two'][$keyy]['othername'] = $cottonfilter[0]['othername'];
                    $Data['cottonfilter_two'][$keyy]['buy_link'] = $cottonfilter[0]['buy_link'];
                    $Data['cottonfilter_two'][$keyy]['images'] = http_type() . $cottonfilter[0]['images'];
//                    $Data['cottonfilter_two'][$keyy]['stock'] = $cottonfilter[0]['stock'];
                    $Data['cottonfilter_two'][$keyy]['product_type'] = $cottonfilter[0]['product_type'];
                    $Data['cottonfilter_two'][$keyy]['product_id'] = $cottonfilter[0]['id'];
                }
            }else{
                $Data['cottonfilter_two'] = [];
            }


            //-----------------------------------------------------------------------------------------------
            //第三组

            $ALL = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 3, 'series' => 'facemasktype'])->get()->toArray();
            if (!empty($ALL)) {

                foreach ($ALL as $keyy => &$datum) {
                    $facemasktype = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($facemasktype[0]['buy_link']=='暂时无货，敬请期待'){
                        $facemasktype[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($facemasktype[0]['buy_link']) && !empty($facemasktype[0]['buy_link'])){
                        $Data['facemasktype_three'][$keyy]['JdInfo'] = self::getJdPrice($facemasktype[0]['buy_link']);
                    }else{
                        $Data['facemasktype_three'][$keyy]['JdInfo'] = false;
                    }
                    $Data['facemasktype_three'][$keyy]['spec'] = $facemasktype[0]['spec'];
                    $Data['facemasktype_three'][$keyy]['othername'] = $facemasktype[0]['othername'];
                    $Data['facemasktype_three'][$keyy]['buy_link'] = $facemasktype[0]['buy_link'];
                    $Data['facemasktype_three'][$keyy]['images'] = http_type() . $facemasktype[0]['images'];
//                    $Data['facemasktype_three'][$keyy]['stock'] = $facemasktype[0]['stock'];
                    $Data['facemasktype_three'][$keyy]['product_type'] = $facemasktype[0]['product_type'];
                    $Data['facemasktype_three'][$keyy]['product_id'] = $facemasktype[0]['id'];
                }
            }else{
                $Data['facemasktype_three'] = [];
            }


            $ALL1 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 3, 'series' => 'filterbox'])->get()->toArray();
            if (!empty($ALL1)) {

                foreach ($ALL1 as $keyy => &$datum) {

                    $filterbox = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filterbox[0]['buy_link']=='暂时无货，敬请期待'){
                        $filterbox[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filterbox[0]['buy_link']) && !empty($filterbox[0]['buy_link'])){
                        $Data['filterbox_three'][$keyy]['JdInfo'] = self::getJdPrice($filterbox[0]['buy_link']);
                    }else{
                        $Data['filterbox_three'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filterbox_three'][$keyy]['spec'] = $filterbox[0]['spec'];
                    $Data['filterbox_three'][$keyy]['othername'] = $filterbox[0]['othername'];
                    $Data['filterbox_three'][$keyy]['buy_link'] = $filterbox[0]['buy_link'];
                    $Data['filterbox_three'][$keyy]['images'] = http_type() . $filterbox[0]['images'];
//                    $Data['filterbox_three'][$keyy]['stock'] = $filterbox[0]['stock'];
                    $Data['filterbox_three'][$keyy]['product_type'] = $filterbox[0]['product_type'];
                    $Data['filterbox_three'][$keyy]['product_id'] = $filterbox[0]['id'];
                }

            }else{

                $Data['filterbox_three'] = [];
            }


            $ALL2 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 3, 'series' => 'filtercover'])->get()->toArray();
            if (!empty($ALL2)) {

                foreach ($ALL2 as $keyy => &$datum) {
                    $filtercover = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($filtercover[0]['buy_link']=='暂时无货，敬请期待'){
                        $filtercover[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($filtercover[0]['buy_link']) && !empty($filtercover[0]['buy_link'])){
                        $Data['filtercover_three'][$keyy]['JdInfo'] = self::getJdPrice($filtercover[0]['buy_link']);
                    }else{
                        $Data['filtercover_three'][$keyy]['JdInfo'] = false;
                    }
                    $Data['filtercover_three'][$keyy]['spec'] = $filtercover[0]['spec'];
                    $Data['filtercover_three'][$keyy]['othername'] = $filtercover[0]['othername'];
                    $Data['filtercover_three'][$keyy]['buy_link'] = $filtercover[0]['buy_link'];
                    $Data['filtercover_three'][$keyy]['images'] = http_type() . $filtercover[0]['images'];
//                    $Data['filtercover_three'][$keyy]['stock'] = $filtercover[0]['stock'];
                    $Data['filtercover_three'][$keyy]['product_type'] = $filtercover[0]['product_type'];
                    $Data['filtercover_three'][$keyy]['product_id'] = $filtercover[0]['id'];
                }
            }else{
                $Data['filtercover_three'] = [];
            }

            $ALL3 = ProductRelation::query()->where(['product_id' => $Product_id, 'type_num' => 3, 'series' => 'cottonfilter'])->get()->toArray();
            if (!empty($ALL3)) {

                foreach ($ALL3 as $keyy => &$datum) {
                    $cottonfilter = ProductType::query()->where(['id' => $datum['product_type_id']])->get()->toArray(); //滤毒盒
                    if($cottonfilter[0]['buy_link']=='暂时无货，敬请期待'){
                        $cottonfilter[0]['buy_link'] = 'false';
                    }

                    if(is_numeric($cottonfilter[0]['buy_link']) && !empty($cottonfilter[0]['buy_link'])){
                        $Data['cottonfilter_three'][$keyy]['JdInfo'] = self::getJdPrice($cottonfilter[0]['buy_link']);
                    }else{
                        $Data['cottonfilter_three'][$keyy]['JdInfo'] = false;
                    }
                    $Data['cottonfilter_three'][$keyy]['spec'] = $cottonfilter[0]['spec'];
                    $Data['cottonfilter_three'][$keyy]['othername'] = $cottonfilter[0]['othername'];
                    $Data['cottonfilter_three'][$keyy]['buy_link'] = $cottonfilter[0]['buy_link'];
                    $Data['cottonfilter_three'][$keyy]['images'] = http_type() . $cottonfilter[0]['images'];
//                    $Data['cottonfilter_three'][$keyy]['stock'] = $cottonfilter[0]['stock'];
                    $Data['cottonfilter_three'][$keyy]['product_type'] = $cottonfilter[0]['product_type'];
                    $Data['cottonfilter_three'][$keyy]['product_id'] = $cottonfilter[0]['id'];
                }
            }else{
                $Data['cottonfilter_three'] = [];
            }

        }
        return $Data;
    }





    /**
     * @param $params
     * @return array
     * 输出内容
     */
    public static function ProductInfo($params)
    {
        $ProductInfo = self::detail($params);
        return $ProductInfo;
    }


    /**
     * @param array $params
     * @return array
     * 查找物质的id
     */
    public static function getids(array $params)
    {
        $where = [];
        if(!empty($params['product_name'])) $where[] = ['chinese_name','like',"%".$params['product_name']."%"];
        $column = ['id','chinese_name'];
        $list = Product::query()->where($where)->get($column)->toArray();
        return $list;
    }



    /**
     * @return
     * 商品列表
     */
    public static function ProductList(array $params)
    {
        $page = isset($params['page']);
        $limit = isset($params['limit'])?$params['limit']:10;
        $List['list'] = ProductType::query()->orderBy('id','desc')->forPage($page,$limit)->get();
        $List['total'] = ProductType::query()->count();
        $List['allPage'] = ceil($List['total']/$limit);
        return $List;
    }



    /**
     * @return
     * 后台商品列表
     */
    public static function ProductListPost(array $params)
    {
        $map = [];
        $page = ($params['page']);
        $limit = $params['limit'];
        if(!empty($params['product_name'] && isset($params['product_name']))){
            $map[] = ['product_type','like',"%".$params['product_name']."%"];
        }
        $List = ProductType::query()->where($map)->orderBy('id','desc')->forPage($page,$limit)->get();
        foreach ($List as &$item){
            if($item['status']==1){
                $item['status']='在售';
            }else{
                $item['status']='停售';
            }
        }
        $Count = ProductType::query()->where($map)->count();
        return ['data'=>$List,'code'=>0,'msg'=>'','count'=>$Count];
    }




    /**
     * @param string $sku
     * 实时获取京东商品价格
     */
    public static function getJdPrice($sku)
    {
        $price = new JdService();
        $result = $price->ProductPrice($sku);
        // var_dump($result);
        // exit();
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
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 添加商品
     */
    public static function addProductType(array $params)
    {
        $rule = [
            //验证字段
            'product_type'=>'required',
            'buy_link'=>'required',
//            'stock'=>'required|numeric',
            'status'=>'required',
            'type'=>'required',
        ];
        $message = [
            'product_type.required'=>'商品名不能为空',
            'buy_link.required'=>'购买链接不能为空',
//            'stock.required'=>'库存不能为空',
//            'stock.numeric'=>'库存必须为数字',
            'status.required'=>'产品状态不能为空',
            'type.required'=>'产品类别不能为空',
        ];
        $Validate = Validator::make($params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();
        $Insert = ProductType::insert($params);
        return $Insert ? DataReturn('添加成功',200):DataReturn('添加失败',-1);
    }



    /**
     * @param array $params
     * @return
     * 编辑商品
     */
    public static function editProductType(array $params)
    {
        $data = [
            'product_type'=> isset($params['product_type'])?$params['product_type']:'',
            'buy_link'    => isset($params['buy_link'])?$params['buy_link']:'',
            'stock'       => isset($params['stock'])?$params['stock']:'',
            'images'      => isset($params['images'])?$params['images']:'',
            'status'      => isset($params['status'])?$params['status']:1,
            'type'        => isset($params['type'])?$params['type']:'mask',
            'spec'        => isset($params['spec'])?$params['spec']:'',
            'othername'   => isset($params['othername'])?$params['othername']:'',
        ];
        $rule = [
            //验证字段
            'product_type'=>'required',
            'buy_link'=>'required',
//            'stock'=>'required|numeric',
            'type'=>'required'
        ];
        $message = [
            'product_type.required'=>'商品名不能为空',
            'buy_link.required'=>'购买链接不能为空',
//            'stock.required'=>'库存不能为空',
//            'stock.numeric'=>'库存必须为数字',
            'type.required'=>'产品类别不能为空',
        ];
        $Validate = Validator::make($data,$rule,$message);
        if($Validate->fails()){
            return $Validate->errors();
        }
        if(self::CheckProduct('product',$params['product_id'])==false){
            return DataReturn('此商品不存在',-1);
        }

        $Update = ProductType::where(['id'=>$params['product_id']])->update($data);
        return $Update ? DataReturn('更新成功',200):DataReturn('更新失败',-1);
    }



    /**
     * @return array
     * 商品页面数据
     */
    public static function productData(array $params)
    {
        if(self::CheckProduct('product',$params['product_id'])==false){
            return DataReturn('此商品不存在',-1);
        }
        $Detail = ProductType::query()->where(['id'=>$params['product_id']])->first()->toArray();
        return $Detail;
    }


    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 删除商品
     */
    public static function delProductType(array $params)
    {
        if(empty($params)) return DataReturn('参数不可为空',-1);
        $delete = ProductType::where(['id'=>$params['product_id']])->delete();
        return $delete ? DataReturn('删除成功',200):DataReturn('删除失败',-1);
    }


    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 删除商品
     */
    public static function delAll(array $params)
    {
        if(empty($params)) return DataReturn('参数不可为空',-1);
        $delete = DB::table('product_type')->whereIn('id',$params['ids'])->delete();
        return $delete ? DataReturn('删除成功',200):DataReturn('删除失败',-1);
    }


    /**
     * @param array $params
     * @return array
     * 物质数据
     */
    public static function MList(array $params)
    {
        $map = [];
        $limit = $params['limit'];
        $page = $params['page'];
        if(!empty($params['matter_name'] && isset($params['matter_name']))){
            $map[] = ['chinese_name','like',"%".$params['matter_name']."%"];
        }
        $List = Product::query()->orderBy('id','desc')->where($map)->forPage($page,$limit)->get();
        foreach($List as &$item){
            $item['status']==1? $item['status'] = '显示':$item['status'] = '隐藏';
        }
        $Count = Product::query()->where($map)->count();
        return ['msg'=>'','data'=>$List,'count'=>$Count,'code'=>0];
    }



    /**
     * @return mixed
     * 产品分类选择
     */
    public static function ChooseProduct()
    {
        $data['mask'] = [];
        $data['facemask'] = [];
        $data['filterbox'] = [];
        $data['filtercover'] = [];
        $data['cottonfilter'] = [];
        $column = ['id','product_type','type'];
        $product = ProductType::query()->where(['status'=>1])->get($column)->toArray();

        foreach ($product as &$item){

            if($item['type']=='facemask'){
                array_push($data['facemask'],$item);
            }
            if($item['type']=='filterbox'){
                array_push($data['filterbox'],$item);
            }
            if($item['type']=='filtercover'){
                array_push($data['filtercover'],$item);
            }
            if($item['type']=='cottonfilter'){
                array_push($data['cottonfilter'],$item);
            }
            if($item['type']=='mask'){
                array_push($data['mask'],$item);
            }
        }
        return $data;
    }




    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 添加物质
     */
    public static function addMatter(array $params)
    {
        $rule = [
            //验证字段
            'chinese_name'=>'required',
//            'english_name'=>'required',
//            'cas'=>'required',
        ];
        $message = [
            'chinese_name.required'=>'物质名不能为空',
//            'english_name.required'=>'英文物质名称不能为空',
//            'cas.required'=>'cas号不能为空',
        ];
        $Validate = Validator::make($params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();

        DB::beginTransaction();
        try {
            $is_mask = $params['is_mask'];
            unset($params['is_mask']);
            $matterId = Product::query()->insertGetId($params);
            //循环关系表
            //1.判断当前物质是否为口罩类
            if($matterId>0){

                if($is_mask=='false'){

                    //1.面罩型号id
                    if(!empty($params['facemasktype_one'])) {

                        $write = self::InsertData($params['facemasktype_one'], $matterId, 1, 'facemasktype');
                    }
                    //滤毒盒
                    if (!empty($params['filterbox_one'])) {

                        $write = self::InsertData($params['filterbox_one'], $matterId, 1, 'filterbox');
                    }

                    //过滤棉
                    if (!empty($params['cottonfilter_one'])) {

                        $write = self::InsertData($params['cottonfilter_one'], $matterId, 1, 'cottonfilter');
                    }

                    //过滤棉盖
                    if (!empty($params['filtercover_one'])) {

                        $write = self::InsertData($params['filtercover_one'], $matterId, 1, 'filtercover');
                    }


                    //型号2
                    if(!empty($params['facemasktype_two'])) {

                        $write = self::InsertData($params['facemasktype_two'], $matterId, 2, 'facemasktype');
                    }
                    //滤毒盒
                    if (!empty($params['filterbox_two'])) {

                        $write = self::InsertData($params['filterbox_two'], $matterId, 2, 'filterbox');
                    }

                    //过滤棉
                    if (!empty($params['cottonfilter_two'])) {

                        $write = self::InsertData($params['cottonfilter_two'], $matterId, 2, 'cottonfilter');
                    }

                    //过滤棉盖
                    if (!empty($params['filtercover_two'])) {

                        $write = self::InsertData($params['filtercover_two'], $matterId, 2, 'filtercover');
                    }


                    //型号3
                    if(!empty($params['facemasktype_three'])) {

                        $write = self::InsertData($params['facemasktype_three'], $matterId, 3, 'facemasktype');
                    }
                    //滤毒盒
                    if (!empty($params['filterbox_three'])) {

                        $write = self::InsertData($params['filterbox_three'], $matterId, 3, 'filterbox');
                    }

                    //过滤棉
                    if (!empty($params['cottonfilter_three'])) {

                        $write = self::InsertData($params['cottonfilter_three'], $matterId, 3, 'cottonfilter');
                    }

                    //过滤棉盖
                    if (!empty($params['filtercover_three'])) {

                        $write = self::InsertData($params['filtercover_three'], $matterId, 3, 'filtercover');
                    }

                }else{

                    $write = self::InsertData($params['masktype_one'], $matterId, 'mask', 'mask');
                }
            }

            DB::commit();
            return DataReturn('添加成功',200);
        }catch(\Exception $e){
            DB::rollBack();
            return DataReturn($e->getMessage(),-1);
        }
    }




    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 更新物质
     */
    public static function editMatter(array $params)
    {
        $rule = [
            //验证字段
            'chinese_name'=>'required',
//            'english_name'=>'required',
//            'cas'=>'required',
        ];
        $message = [
            'chinese_name.required'=>'物质名不能为空',
//            'english_name.required'=>'英文物质名称不能为空',
//            'cas.required'=>'cas号不能为空',
        ];
        $Validate = Validator::make($params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();
        $matterId = $params['matter_id'];
        $is_mask = $params['is_mask'];
        unset($params['matter_id'],$params['is_mask']);
        if(self::CheckProduct('matter',$matterId)==false){
            return DataReturn('此物质不存在',-1);
        }
        DB::beginTransaction();

        try {
            Product::query()->where(['id'=>$matterId])->Update($params);
            //循环关系表
            $del = ProductRelation::where(['product_id'=>$matterId])->delete(); //清除原始数据
//            dump($del);die();
            if($is_mask=='false'){

                //1.面罩型号id
                if(!empty($params['facemasktype_one'])) {

                    $write = self::InsertData($params['facemasktype_one'], $matterId, 1, 'facemasktype');
                }
                //滤毒盒
                if (!empty($params['filterbox_one'])) {

                    $write = self::InsertData($params['filterbox_one'], $matterId, 1, 'filterbox');
                }

                //过滤棉
                if (!empty($params['cottonfilter_one'])) {

                    $write = self::InsertData($params['cottonfilter_one'], $matterId, 1, 'cottonfilter');
                }

                //过滤棉盖
                if (!empty($params['filtercover_one'])) {

                    $write = self::InsertData($params['filtercover_one'], $matterId, 1, 'filtercover');
                }


                //型号2
                if(!empty($params['facemasktype_two'])) {

                    $write = self::InsertData($params['facemasktype_two'], $matterId, 2, 'facemasktype');
                }
                //滤毒盒
                if (!empty($params['filterbox_two'])) {

                    $write = self::InsertData($params['filterbox_two'], $matterId, 2, 'filterbox');
                }

                //过滤棉
                if (!empty($params['cottonfilter_two'])) {

                    $write = self::InsertData($params['cottonfilter_two'], $matterId, 2, 'cottonfilter');
                }

                //过滤棉盖
                if (!empty($params['filtercover_two'])) {

                    $write = self::InsertData($params['filtercover_two'], $matterId, 2, 'filtercover');
                }


                //型号3
                if(!empty($params['facemasktype_three'])) {

                    $write = self::InsertData($params['facemasktype_three'], $matterId, 3, 'facemasktype');
                }
                //滤毒盒
                if (!empty($params['filterbox_three'])) {

                    $write = self::InsertData($params['filterbox_three'], $matterId, 3, 'filterbox');
                }

                //过滤棉
                if (!empty($params['cottonfilter_three'])) {

                    $write = self::InsertData($params['cottonfilter_three'], $matterId, 3, 'cottonfilter');
                }

                //过滤棉盖
                if (!empty($params['filtercover_three'])) {

                    $write = self::InsertData($params['filtercover_three'], $matterId, 3, 'filtercover');
                }

            }else{
                if (!empty($params['masktype_one'])) {
                    $write = self::InsertData($params['masktype_one'], $matterId, 'mask', 'mask');
                }
            }

            DB::commit();
            return DataReturn('更新成功',200);
        }catch(\Exception $e){
            DB::rollBack();
            return DataReturn('更新失败',-1);
        }
    }


    /**
     * @param $type
     * @param $id
     * @return bool
     * 检查产品是否存在
     */
    public static function CheckProduct($type,$id)
    {
        if($type=='matter'){
            $Detail = Product::query()->where(['id'=>$id])->first();
        }else{
            $Detail = ProductType::query()->where(['id'=>$id])->first();
        }
        if(!empty($Detail)){

            return true;

        }else{

            return false;
        }
    }


    /**
     * @param $data
     * @param $matterid
     * @param $type_num
     * @param $series
     * @return bool
     * 插入数据
     */
    public static function InsertData($data,$matterid,$type_num,$series)
    {
        if(is_array($data)){
            $dataList = $data;
        }else{
            $dataList = explode(",",$data);
        }
        foreach ($dataList as &$value){
            $dataArr = [
                'product_type_id'=>$value,
                'product_id'=>$matterid,
                'type_num'=>$type_num,
                'series'=>$series
            ];
            $query = ProductRelation::insert($dataArr);
        }
        return $query ? true : false;
    }


    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 删除物质
     */
    public static function delMatter(array $params)
    {
        DB::beginTransaction();
        try {
            if(empty($params['matter_id'])) return DataReturn('参数不可为空',-1);
            $del = Product::query()->where(['id'=>$params['matter_id']])->delete();
            $relation = ProductRelation::query()->where(['product_id'=>$params['matter_id']])->get();
            if(!empty($relation)){
                ProductRelation::query()->where(['product_id'=>$params['matter_id']])->delete();
            }
            if(!$del){
                throw new \Exception("error");
            }
            DB::commit();
            return DataReturn('删除成功',200);
        }catch (\Exception $e){
            DB::rollBack();
            return DataReturn('删除失败',-1);
        }
    }


    /**
     * @param array $params
     * @return array|\Illuminate\Support\MessageBag
     * 批量删除物质
     */
    public static function delManyMatter(array $params)
    {
        DB::beginTransaction();
        try {
            if(empty($params['ids'])) return DataReturn('参数不可为空',-1);
            $del = Product::query()->whereIn('id',$params['ids'])->delete();
            $relation = ProductRelation::query()->whereIn('product_id',$params['ids'])->get();
            if(!empty($relation)){
                ProductRelation::query()->whereIn('product_id',$params['ids'])->delete();
            }
            if(!$del){
                throw new \Exception("error");
            }
            DB::commit();
            return DataReturn('删除成功',200);
        }catch (\Exception $e){
            DB::rollBack();
            return DataReturn('删除失败',-1);
        }
    }


    /**
     * @param array $params
     * @return mixed
     * 物质关系
     */
    public static function Relation(array $params)
    {
        if(empty($params['matter_id'])){

            return DataReturn('必要参数不能为空',-1);
        }

        if(self::CheckProduct('matter',$params['matter_id'])==false){
            return DataReturn('此物质不存在',-1);
        }

        //物质关系
        $ReturnDate['Mask']     = [];

        $ReturnDate['faceMask_one']     = [];
        $ReturnDate['filterBox_one']    = [];
        $ReturnDate['cottonFilter_one'] = [];
        $ReturnDate['filterCover_one']  = [];

        $ReturnDate['faceMask_two']     = [];
        $ReturnDate['filterBox_two']    = [];
        $ReturnDate['cottonFilter_two'] = [];
        $ReturnDate['filterCover_two']  = [];

        $ReturnDate['faceMask_three']     = [];
        $ReturnDate['filterBox_three']    = [];
        $ReturnDate['cottonFilter_three'] = [];
        $ReturnDate['filterCover_three']  = [];

        $MatterRelation = ProductRelation::query()->where(['product_id'=>$params['matter_id']])->get()->toArray();
        $column = ['id','chinese_name','cas','english_name','mac','twa','stel','remark','recommend','status'];
        $ReturnDate['MatterInfo'] = Product::query()->where(['id'=>$params['matter_id']])->first($column)->toArray();

        foreach ($MatterRelation as &$Ma){

            //口罩
            if($Ma['series']=='mask' && $Ma['type_num']=='mask'){

                array_push($ReturnDate['Mask'],$Ma['product_type_id']);
            }
            //第一组
            if($Ma['series']=='facemasktype' && $Ma['type_num']==1){

                array_push($ReturnDate['faceMask_one'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filterbox' && $Ma['type_num']==1){

                array_push($ReturnDate['filterBox_one'],$Ma['product_type_id']);
            }

            if($Ma['series']=='cottonfilter' && $Ma['type_num']==1){

                array_push($ReturnDate['cottonFilter_one'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filtercover' && $Ma['type_num']==1){

                array_push($ReturnDate['filterCover_one'],$Ma['product_type_id']);
            }


            //第二组
            if($Ma['series']=='facemasktype' && $Ma['type_num']==2){

                array_push($ReturnDate['faceMask_two'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filterbox' && $Ma['type_num']==2){

                array_push($ReturnDate['filterBox_two'],$Ma['product_type_id']);
            }

            if($Ma['series']=='cottonfilter' && $Ma['type_num']==2){

                array_push($ReturnDate['cottonFilter_two'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filtercover' && $Ma['type_num']==2){

                array_push($ReturnDate['filterCover_two'],$Ma['product_type_id']);
            }


            //第三组
            if($Ma['series']=='facemasktype' && $Ma['type_num']==3){

                array_push($ReturnDate['faceMask_three'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filterbox' && $Ma['type_num']==3){

                array_push($ReturnDate['filterBox_three'],$Ma['product_type_id']);
            }

            if($Ma['series']=='cottonfilter' && $Ma['type_num']==3){

                array_push($ReturnDate['cottonFilter_three'],$Ma['product_type_id']);
            }

            if($Ma['series']=='filtercover' && $Ma['type_num']==3){

                array_push($ReturnDate['filterCover_three'],$Ma['product_type_id']);
            }
        }
        return DataReturn('数据加载成功',0,$ReturnDate);
    }



    public static function ChangeMatter()
    {
        $Matter = Product::where(['status'=>1])->get()->toArray();//获取到所有物质id
        foreach ($Matter as &$item){
            $relation = ProductRelation::where(['product_id'=>$item])->first();
            if(empty($relation)){
                Product::where(['id'=>$item['id']])->update(['status'=>0]);
            }
        }
        return DataReturn('success',200);
    }




    /**
     * @param $Params
     * 搜索物质统计
     */
    public static function MatterStatistics($Params,$token)
    {
        $matterName = Product::query()->where(['id'=>$Params['id']])->value('chinese_name');
        $Search = [
            'object_id'   =>$Params['id'],
            'object_name' =>$matterName,
            'create_time' =>time(),
            'action'      =>'search',
            'user_token'  =>$token,
            'object_type' =>'matter'
        ];
        ObjectCount::insert($Search);
    }



    /**
     * @param $Params
     * 点击商品统计
     * @param action 行为 buy:购买 choose:选择
     */
    public static function ProductStatistics($Params)
    {
        $rule = [
            'product_id'=>'required',
            'action'=>'required',
            'token'=>'required'
        ];
        $message = [
            'product_id.required'=>'商品id必须',
            'action.required'=>'行为必须',
            'token.required'=>'令牌必须'
        ];
        $Validate = Validator::make($Params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();
        $productName = ProductType::query()->where(['id'=>$Params['product_id']])->value('product_type');
//        dump($productName);die();
        $Search = [
            'object_id'  =>$Params['product_id'],
            'object_name'=>$productName,
            'action'      =>$Params['action'],
            'user_token'  =>$Params['token'],
            'create_time' =>time(),
            'object_type' =>'product'
        ];
        ObjectCount::insert($Search);
    }



}

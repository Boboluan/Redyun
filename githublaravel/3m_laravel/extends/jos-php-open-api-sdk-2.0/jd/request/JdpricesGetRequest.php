<?php
class JdpricesGetRequest
{


    private $apiParas = array();

    public function getApiMethodName(){
        return "jingdong.jdprices.get";
    }

    public function getApiParas(){
        if(empty($this->apiParas)){
            return "{}";
        }
        return json_encode($this->apiParas);
    }

    public function check(){

    }

    public function putOtherTextParam($key, $value){
        $this->apiParas[$key] = $value;
        $this->$key = $value;
    }

    private $version;

    public function setVersion($version){
        $this->version = $version;
    }

    public function getVersion(){
        return $this->version;
    }
    private $area;

    public function setArea($area){
        $this->area = $area;
        $this->apiParas["area"] = $area;
    }

    public function getArea(){
        return $this->area;
    }

    private $skuId;
    public function setSkuId($skuId ){
        $this->skuId=$skuId;
        $this->apiParas["skuId"] = $skuId;
    }

    public function getSkuId(){
        return $this->skuId;
    }
    private $channel;

    public function setChannel($channel){
        $this->channel = $channel;
        $this->apiParas["channel"] = $channel;
    }

    public function getChannel(){
        return $this->channel;
    }

    private $priceFieldSet;
    public function setPriceFieldSet($priceFieldSet ){
        $this->priceFieldSet=$priceFieldSet;
        $this->apiParas["priceFieldSet"] = $priceFieldSet;
    }

    public function getPriceFieldSet(){
        return $this->priceFieldSet;
    }
    private $source;

    public function setSource($source){
        $this->source = $source;
        $this->apiParas["source"] = $source;
    }

    public function getSource(){
        return $this->source;
    }

    private $priceInfoSet;
    public function setPriceInfoSet($priceInfoSet ){
        $this->priceInfoSet=$priceInfoSet;
        $this->apiParas["priceInfoSet"] = $priceInfoSet;
    }

    public function getPriceInfoSet(){
        return $this->priceInfoSet;
    }
}









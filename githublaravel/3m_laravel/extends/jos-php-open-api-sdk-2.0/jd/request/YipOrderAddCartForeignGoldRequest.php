<?php
class YipOrderAddCartForeignGoldRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.yip.order.addCartForeignGold";
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
                                    	                   			private $thirdPartyParam;
    	                        
	public function setThirdPartyParam($thirdPartyParam){
		$this->thirdPartyParam = $thirdPartyParam;
         $this->apiParas["thirdPartyParam"] = $thirdPartyParam;
	}

	public function getThirdPartyParam(){
	  return $this->thirdPartyParam;
	}

}





        
 


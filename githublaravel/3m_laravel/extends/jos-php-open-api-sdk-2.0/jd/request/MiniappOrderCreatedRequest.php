<?php
class MiniappOrderCreatedRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.miniapp.order.created";
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
                                                        		                                    	                   			private $totalFee;
    	                        
	public function setTotalFee($totalFee){
		$this->totalFee = $totalFee;
         $this->apiParas["totalFee"] = $totalFee;
	}

	public function getTotalFee(){
	  return $this->totalFee;
	}

                        	                   			private $skuName;
    	                        
	public function setSkuName($skuName){
		$this->skuName = $skuName;
         $this->apiParas["skuName"] = $skuName;
	}

	public function getSkuName(){
	  return $this->skuName;
	}

                        	                        	                   			private $openId;
    	                        
	public function setOpenId($openId){
		$this->openId = $openId;
         $this->apiParas["openId"] = $openId;
	}

	public function getOpenId(){
	  return $this->openId;
	}

                        	                   			private $callBackUrl;
    	                        
	public function setCallBackUrl($callBackUrl){
		$this->callBackUrl = $callBackUrl;
         $this->apiParas["callBackUrl"] = $callBackUrl;
	}

	public function getCallBackUrl(){
	  return $this->callBackUrl;
	}

                        	                   			private $openIdBuyer;
    	                                                                        
	public function setOpenIdBuyer($openIdBuyer){
		$this->openIdBuyer = $openIdBuyer;
         $this->apiParas["open_id_buyer"] = $openIdBuyer;
	}

	public function getOpenIdBuyer(){
	  return $this->openIdBuyer;
	}

                        	                   			private $xidBuyer;
    	                                                            
	public function setXidBuyer($xidBuyer){
		$this->xidBuyer = $xidBuyer;
         $this->apiParas["xid_buyer"] = $xidBuyer;
	}

	public function getXidBuyer(){
	  return $this->xidBuyer;
	}

                            }





        
 


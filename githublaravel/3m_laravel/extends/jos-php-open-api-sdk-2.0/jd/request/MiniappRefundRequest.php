<?php
class MiniappRefundRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.miniapp.refund";
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
                                                        		                                    	                   			private $userId;
    	                        
	public function setUserId($userId){
		$this->userId = $userId;
         $this->apiParas["userId"] = $userId;
	}

	public function getUserId(){
	  return $this->userId;
	}

                        	                   			private $refundUuid;
    	                        
	public function setRefundUuid($refundUuid){
		$this->refundUuid = $refundUuid;
         $this->apiParas["refundUuid"] = $refundUuid;
	}

	public function getRefundUuid(){
	  return $this->refundUuid;
	}

                        	                   			private $isHalfRefund;
    	                        
	public function setIsHalfRefund($isHalfRefund){
		$this->isHalfRefund = $isHalfRefund;
         $this->apiParas["isHalfRefund"] = $isHalfRefund;
	}

	public function getIsHalfRefund(){
	  return $this->isHalfRefund;
	}

                        	                   			private $orderId;
    	                        
	public function setOrderId($orderId){
		$this->orderId = $orderId;
         $this->apiParas["orderId"] = $orderId;
	}

	public function getOrderId(){
	  return $this->orderId;
	}

                        	                        	                   			private $refundAmount;
    	                        
	public function setRefundAmount($refundAmount){
		$this->refundAmount = $refundAmount;
         $this->apiParas["refundAmount"] = $refundAmount;
	}

	public function getRefundAmount(){
	  return $this->refundAmount;
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





        
 


<?php
class MiniappBrandBenefitReportRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.miniapp.brand.benefit.report";
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
                                                        		                                    	                   			private $benId;
    	                        
	public function setBenId($benId){
		$this->benId = $benId;
         $this->apiParas["benId"] = $benId;
	}

	public function getBenId(){
	  return $this->benId;
	}

                        	                   			private $benState;
    	                        
	public function setBenState($benState){
		$this->benState = $benState;
         $this->apiParas["benState"] = $benState;
	}

	public function getBenState(){
	  return $this->benState;
	}

                        	                   			private $openId;
    	                        
	public function setOpenId($openId){
		$this->openId = $openId;
         $this->apiParas["openId"] = $openId;
	}

	public function getOpenId(){
	  return $this->openId;
	}

                        	                   			private $benType;
    	                        
	public function setBenType($benType){
		$this->benType = $benType;
         $this->apiParas["benType"] = $benType;
	}

	public function getBenType(){
	  return $this->benType;
	}

                        	                   			private $benNo;
    	                        
	public function setBenNo($benNo){
		$this->benNo = $benNo;
         $this->apiParas["benNo"] = $benNo;
	}

	public function getBenNo(){
	  return $this->benNo;
	}

                        	                   			private $reportTime;
    	                        
	public function setReportTime($reportTime){
		$this->reportTime = $reportTime;
         $this->apiParas["reportTime"] = $reportTime;
	}

	public function getReportTime(){
	  return $this->reportTime;
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





        
 


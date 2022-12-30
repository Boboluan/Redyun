<?php
class MiniappApiActivityMiniAppActivityApiUpdateActivityStatusRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.miniapp.api.activity.miniAppActivityApi.updateActivityStatus";
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
                                                        		                                    	                        	                        	                   			private $activityUuid;
    	                        
	public function setActivityUuid($activityUuid){
		$this->activityUuid = $activityUuid;
         $this->apiParas["activityUuid"] = $activityUuid;
	}

	public function getActivityUuid(){
	  return $this->activityUuid;
	}

                        	                   			private $status;
    	                        
	public function setStatus($status){
		$this->status = $status;
         $this->apiParas["status"] = $status;
	}

	public function getStatus(){
	  return $this->status;
	}

                            }





        
 


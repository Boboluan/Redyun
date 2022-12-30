<?php
class JingdongMiniappApiActivityMiniAppActivityApiUpdateActivityRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.jingdong.miniapp.api.activity.miniAppActivityApi.updateActivity";
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
                                                        		                                    	                   			private $activityMsg;
    	                        
	public function setActivityMsg($activityMsg){
		$this->activityMsg = $activityMsg;
         $this->apiParas["activityMsg"] = $activityMsg;
	}

	public function getActivityMsg(){
	  return $this->activityMsg;
	}

                        	                        	                   			private $name;
    	                        
	public function setName($name){
		$this->name = $name;
         $this->apiParas["name"] = $name;
	}

	public function getName(){
	  return $this->name;
	}

                        	                        	                   			private $startTime;
    	                        
	public function setStartTime($startTime){
		$this->startTime = $startTime;
         $this->apiParas["startTime"] = $startTime;
	}

	public function getStartTime(){
	  return $this->startTime;
	}

                        	                   			private $endTime;
    	                        
	public function setEndTime($endTime){
		$this->endTime = $endTime;
         $this->apiParas["endTime"] = $endTime;
	}

	public function getEndTime(){
	  return $this->endTime;
	}

                        	                   			private $activityUuid;
    	                        
	public function setActivityUuid($activityUuid){
		$this->activityUuid = $activityUuid;
         $this->apiParas["activityUuid"] = $activityUuid;
	}

	public function getActivityUuid(){
	  return $this->activityUuid;
	}

                            }





        
 


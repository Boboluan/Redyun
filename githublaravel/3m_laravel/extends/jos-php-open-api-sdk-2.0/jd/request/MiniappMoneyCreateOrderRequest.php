<?php
class MiniappMoneyCreateOrderRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.miniapp.money.createOrder";
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
                                    	                   			private $encrypt;
    	                        
	public function setEncrypt($encrypt){
		$this->encrypt = $encrypt;
         $this->apiParas["encrypt"] = $encrypt;
	}

	public function getEncrypt(){
	  return $this->encrypt;
	}

                        	}





        
 


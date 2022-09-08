<?php 

class RestClient ()
{

	protected $apiUrl;
	protected $authMethod;
	protected $resultType;

	protected $curl = null;

	public functionn __constructor(sring $apiUrl, string $authMethod = 'none',string $resultType = 'json'){

		$this->apiUrl = $apiUrl; 
		$this->authMethod = $authMethod; 
		$this->resultType = $resultType; 

	}


	public function setApiUrl(string $url)
	{
		$this->apiUrl = $url;

	}

	public function setApiAuthMethod($authMethod)
	{
		$this->authMethod = $authMethod;

	}

	public function setApiResultType(string $resultType)
	{
		$this->resultType = $resultType;

	}	


	public  function callAPI(string $method, array $data=false):array
	{

		$this->curl = curl_init();

		$setCurlOptions = "set" . ucfirst($method);

		if(! method_exists($this, $setCurlOptions)) {
			return false;
		}

		$this->$setCurlOptions($data);
		$this->prepareAuth();

		$result = $this->execCurl();

		curl_close($this->curl);

		return $this->convertResult($result);

	}

	protected convertResult(string $result){

		switch ($this->resultType) {
			case 'json' : $result = json_decode($result);
			break;

			case 'xml': $result = simplexml_load_string($result);
			break;

		}

		return $result;

	}

	protected function execCurl(){

		if(! $this->curl){
			return false;
		}
		
		curl_setopt($this->curl, CURLOPT_URL, $this->$apiUrl);
    	curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

/*
CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json",
    "x-api-key: whateveriyouneedinyourheader"
  )    	
*/

    	try{ 
    		$result = curl_exec($this->curl);
    	}
    	catch($e){

    		var_dump(curl_error($this->curl));
    		
    		return  "error";

    	}

    	return $result;

	}

	// REST  methods : setting curl options

	protected function setPOST(array $data)
	{
		if(! $this->curl){
			return false;
		}
		curl_setopt($this->curl, CURLOPT_POST, 1);

		if ($data){
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		}
	}

	protected function setPUT(array $data)
	{
		if(! $this->curl){
			return false;
		}
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT')
		
		if ($data){
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		}
	}


	protected function setDELETE(array $data)
	{
		if(! $this->curl){
			return false;
		}
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE')

		if ($data){
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		}

	}


	protected function setPATCH(array $data)
	{
		if(! $this->curl){
			return false;
		}
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH')

		if ($data){
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		}
	}


	protected function setGET(array $data)
	{
		if(! $this->curl){
			return false;
		}

	 	if (! $data){ 
	 		return; 
	 	}
		
		$this->apiUrl = sprintf("%s?%s", $this->apiUrl , http_build_query($data));

	}

	// AUTH methods : setting curl options

	protected function prepareAuth()
	{
		if(! $this->curl){
			return false;
		}

	}
}
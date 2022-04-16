<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for performing multiple API-requests at the same time
*
* The class APIMultiRequest provides an implementation for querying multiple API-requests at the same time 
*
* @method void addRequest(CurlHandle $request)
* @method void setRequest(Array $requests)
* @method void clearRequests()
* @method Generator|SimpleXMLElement execute()
*/
class APIMultiRequest {
	private Array $requests;
	
	/**
	* constructor for class APIMultiRequest
	*
	* @param Array $requests  an array of CurlHandle-requests
	* @access public
	*/
	public function __construct(Array $requests = array()) {
		$this->requests = $requests;
	}
	
	/**
	* adder for additional CurlHandle-requests
	*
	* @param CurlHandle $request  the request to add to the request queue
	* @access public
	*/
	public function addRequest(CurlHandle &$request) {
		array_push($this->requests, $request);
	}
	
	/**
	* setter for the request queue
	*
	* @param Array $requests  an array containing CurlHandle-requests
	* @access public
	*/
	public function setRequest(Array &$requests) {
		$this->requests = $requests;
	}
	
	/**
	* clears the request queue
	*
	* @access public
	*/
	public function clearRequests() {
		$this->requests = array();
	}
	
	/**
	* executor of all the requests in the request queue
	*
	* @return Generator|SimpleXMLElement  yields the SimpleXMLElement results for each query
	* @access public
	*/
	public function execute() {
		while(!empty($this->requests)) {
			$requests = array_slice($this->requests, 0, 500);
			$this->requests = array_splice($this->requests, 500);
			
			$requestHandler = curl_multi_init();
			
			foreach($requests as $request) {
				curl_multi_add_handle($requestHandler, $request);
			}
			
			$active = null;
			do {
				$execCode = curl_multi_exec($requestHandler, $active);
			} while($execCode == CURLM_CALL_MULTI_PERFORM);

			while($active && $execCode == CURLM_OK) {
				if(curl_multi_select($requestHandler) != -1) {
					do {
						$execCode = curl_multi_exec($requestHandler, $active);
					} while($execCode == CURLM_CALL_MULTI_PERFORM);
				}
			}
			
			foreach($requests as $request) {
				$return = @simplexml_load_string(curl_multi_getcontent($request));
				if($return === false) {
					$return = @simplexml_load_string(curl_exec(curl_copy_handle($request)));
				}
				yield $return;
				curl_multi_remove_handle($requestHandler, $request);
				curl_close($request);
			}

			curl_multi_close($requestHandler);
		}
	}
}
?>
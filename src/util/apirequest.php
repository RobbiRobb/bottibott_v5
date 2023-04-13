<?php
/**
* A class representing API-requests
*
* The class APIRequest allows the creation and execution of APIRequest to a wiki specified in the url
* All requests are processed as POST requests. It is possible to send data both via GET parameters and the POST body
*
* @method array getGetFields()
* @method void setGetFields(array $fields)
* @method void addToGetFields(string $key, string $value)
* @method array getPostFields()
* @method void setPostFields(array $fields)
* @method void addToPostFields(string $key, string $value)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class APIRequest extends Request {
	private array $get;
	private array $post;
	
	/**
	* constructor for class APIRequest
	*
	* @param string $url  the url to the wiki
	* @access public
	*/
	public function __construct(string $url = "") {
		$this->url = $url;
		$this->get = array();
		$this->post = array();
	}
	
	/**
	* getter for the currently set GET-fields
	*
	* @return array  the currently set GET-fields
	* @access public
	*/
	public function getGetFields() : array {
		return $this->get;
	}
	
	/**
	* setter for setting multiple GET-fields at the same time
	* will overwrite any existing GET-fields
	*
	* @param array $fields  the fields that should be set
	* @access public
	*/
	public function setGetFields(array $fields) : void {
		$this->get = $fields;
	}
	
	/**
	* setter to add to existing GET-fields
	*
	* @param string $key    the key that should be used
	* @param string $value  the value that should be set
	* @access public
	*/
	public function addToGetFields(string $key, string $value) : void {
		$this->get[$key] = $value;
	}
	
	/**
	* getter for the currently set POST-fields
	*
	* @return array  the currently set POST-fields
	* @access public
	*/
	public function getPostFields() : array {
		return $this->post;
	}
	
	/*
	* setter for setting multiple POST-fields at the same time
	* will overwrite any existing POST-fields
	*
	* @param array $fields  the fields that should be set
	* @access public
	*/
	public function setPostFields(array $fields) : void {
		$this->post = $fields;
	}
	
	/**
	* setter to add to existing POST-fields
	*
	* @param string $key    the key that should be used
	* @param string $value  the value that should be set
	* @access public
	*/
	public function addToPostFields(string $key, mixed $value) : void {
		$this->post[$key] = $value;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->url . "?" . http_build_query($this->get));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_COOKIEFILE, $this->cookiefile);
		curl_setopt($request, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $this->post);
		if($this->logger !== null) { $this->logger->logRequest($this->url, $this->get, $this->post); }
		$queryResult = simplexml_load_string(curl_exec($request));
		if($this->logger !== null) { $this->logger->logResponse($request); }
		curl_close($request);
		return $queryResult;
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->url . "?" . http_build_query($this->get));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_COOKIEFILE, $this->cookiefile);
		curl_setopt($request, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $this->post);
		return $request;
	}
}
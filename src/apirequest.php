<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests
*
* The class APIRequest allows the creation and execution of APIRequest to a wiki specified in the url
* All requests are processed as POST requests. It is possible to send data both via GET parameters and the POST body
*
* @method Array getGetFields()
* @method void setGetFields(Array $fields)
* @method void addToGetFields(String $key, String $value)
* @method Array getPostFields()
* @method void setPostFields(Array $fields)
* @method void addToPostFields(String $key, String $value)
* @method SimpleXMLElement execute()
* @method CurlHandle getRequest()
*/
class APIRequest extends Request {
	private Array $get;
	private Array $post;
	
	/**
	* constructor for class APIRequest
	*
	* @access public
	* @param String $url  the url to the wiki
	* @access public
	*/
	public function __construct(String $url = "") {
		$this->url = $url;
		$this->get = array();
		$this->post = array();
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* getter for the currently set GET-fields
	*
	* @return Array  the currently set GET-fields
	* @access public
	*/
	public function getGetFields() {
		return $this->get;
	}
	
	/**
	* setter for setting multiple GET-fields at the same time
	* will overwrite any existing GET-fields
	*
	* @param Array $fields  the fields that should be set
	* @access public
	*/
	public function setGetFields(Array $fields) {
		$this->get = $fields;
	}
	
	/**
	* setter to add to existing GET-fields
	*
	* @param String $key    the key that should be used
	* @param String $value  the value that should be set
	* @access public
	*/
	public function addToGetFields(String $key, String $value) {
		$this->get[$key] = $value;
	}
	
	/**
	* getter for the currently set POST-fields
	*
	* @return Array  the currently set POST-fields
	* @access public
	*/
	public function getPostFields() {
		return $this->post;
	}
	
	/*
	* setter for setting multiple POST-fields at the same time
	* will overwrite any existing POST-fields
	*
	* @param Array $fields  the fields that should be set
	* @access public
	*/
	public function setPostFields(Array $fields) {
		$this->post = $fields;
	}
	
	/**
	* setter to add to existing POST-fields
	*
	* @param String $key    the key that should be used
	* @param String $value  the value that should be set
	* @access public
	*/
	public function addToPostFields(String $key, mixed $value) {
		$this->post[$key] = $value;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->url."?".http_build_query($this->get));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_COOKIEFILE, $this->cookiefile);
		curl_setopt($request, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $this->post);
		$queryResult = simplexml_load_string(curl_exec($request));
		curl_close($request);
		return $queryResult;
	}
	
	/**
	* getter for the API-request for executing multiple requests at once
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() {
		$request = curl_init();
		curl_setopt($request, CURLOPT_URL, $this->url."?".http_build_query($this->get));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_COOKIEFILE, $this->cookiefile);
		curl_setopt($request, CURLOPT_COOKIEJAR, $this->cookiefile);
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt($request, CURLOPT_POSTFIELDS, $this->post);
		return $request;
	}
}
?>
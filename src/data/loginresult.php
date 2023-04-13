<?php
/**
* A class representing the result of a login
*
* The class LoginResult reprsents results of an attempt to log a bot into a wiki
*
* @method string getStatus()
* @method string getUsername()
*/
class LoginResult {
	private readonly string $status;
	private readonly string $username;
	
	/**
	* constructor for class LoginResult
	*
	* @param SimpleXMLElement $result  result from a login query
	*/
	public function __construct(SimpleXMLElement $result) {
		$this->status = (string)$result->clientlogin["status"];
		if(isset($result->clientlogin["username"])) { $this->username = (string)$result->clientlogin["username"]; }
	}
	
	/**
	* getter for the status
	*
	* @return string  the status of the login
	* @access public
	*/
	public function getStatus() : string {
		return $this->status;
	}
	
	/**
	* getter for the username
	*
	* @return string  the username if the login was successful
	* @access public
	*/
	public function getUsername() : string {
		if($this->getStatus() !== "PASS") { throw new Exception("Not logged in"); }
		return $this->username;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->status)) { $info["status"] = $this->status; }
		if(isset($this->username)) { $info["username"] = $this->username; }
		return $info;
	}
}
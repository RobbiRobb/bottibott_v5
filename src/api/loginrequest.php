<?php
/**
* A class for representing API-requests for a login
*
* The class LoginRequest allows the creation of API-requests for logging in to a wiki
* to get access to additional functions that may require a login
*
* @method SimpleXMLElement execute()
*/
class LoginRequest extends Request {
	private string $username;
	private string $password;
	
	/**
	* constructor for class LoginRequest
	*
	* @param string $url       the url to the wiki
	* @param string $username  the username of the user that wants to login
	* @param string $password  the password for the user
	* @access public
	*/
	public function __construct(string $url, string $username, string $password) {
		$this->url = $url;
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	* executor for the API-request
	*
	* @param string $token      the token required to login
	* @return SimpleXMLElement  true on success, an error message on failure
	* @access public
	*/
	public function execute(string $token) : SimpleXMLElement {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "clientlogin");
		$request->addToGetFields("format", "xml");
		$request->addToPostFields("loginreturnurl", $this->url);
		$request->addToPostFields("username", $this->username);
		$request->addToPostFields("password", $this->password);
		$request->addToPostFields("logintoken", $token);
		return $request->execute();
	}
	
	/**
	* debug function
	* never display password in plaintext
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->url)) { $info["url"] = $this->url; }
		if(isset($this->username)) { $info["username"] = $this->username; }
		if(isset($this->password)) { $info["password"] = password_hash($this->password, PASSWORD_DEFAULT); }
		return $info;
	}
}
<?php
/**
* A class for representing API-requests for a login
*
* The class Login allows the creation of API-requests for logging in to a wiki got get access to additional functions that may require a login
*
* @method void setUsername(String $username)
* @method void setPassword(String $password)
* @method mixed execute()
*/
class Login extends Request {
	private String $username;
	private String $password;
	
	/**
	* constructor for class Login
	*
	* @param String $url       the url to the wiki
	* @param String $username  the username of the user that wants to login
	* @param String $password  the password for the user
	* @access public
	*/
	public function __construct(String $url, String $username, String $password) {
		$this->url = $url;
		$this->username = $username;
		$this->password = $password;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the username
	*
	* @param String $username  the username that should be set
	* @access public
	*/
	public function setUsername(String $username) {
		$this->username = $username;
	}
	
	/**
	* setter for the password
	*
	* @param String $password  the password that should be set
	* @access public
	*/
	public function setPassword(String $password) {
		$this->password = $password;
	}
	
	/**
	* executor for the API-request
	*
	* @param String $token  the token required to login
	* @return mixed         true on success, an error message on failure
	* @access public
	*/
	public function execute(String $token) {
		$login = new APIRequest($this->url);
		$login->setCookieFile($this->cookiefile);
		$login->setLogger($this->logger);
		$login->addToGetFields("action", "clientlogin");
		$login->addToGetFields("format", "xml");
		$login->addToPostFields("loginreturnurl", $this->url);
		$login->addToPostFields("username", $this->username);
		$login->addToPostFields("password", $this->password);
		$login->addToPostFields("logintoken", $token);
		$queryResult = $login->execute();
		return ((String)$queryResult->clientlogin["status"] == "PASS") ? true : $queryResult->clientlogin["message"];
	}
}
?>
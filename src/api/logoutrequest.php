<?php
/**
* A class representing API-requests for a logout
*
* The class LogoutRequest allows the creation of API-requests for logging out of a wiki after a successful login
*
* @method void execute(string $token)
*/
class LogoutRequest extends Request {
	/**
	* constructor for class LogoutRequest
	*
	* @param string $url  the url to the wiki
	* @access public
	*/
	public function __construct(string $url) {
		$this->url = $url;
	}
	
	/**
	* executor for the API-request
	*
	* @param string $token      the token required to logout
	* @access public
	*/
	public function execute(string $token) : void {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "logout");
		$request->addToGetFields("format", "xml");
		$request->addToPostFields("token", $token);
		$request->execute();
	}
}
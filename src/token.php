<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests for a token
*
* The class Token allows the creation of API-requests for a token of a given type
*
* @method void setType(String $type)
* @method String execute()
*/
class Token extends Request {
	private String $type;
	
	/**
	* constructor for class Token
	*
	* @param String $url   the url to the wiki
	* @param String $type  the type of the token
	* @access public
	*/
	public function __construct(String $url, String $type) {
		$this->url = $url;
		$this->type = $type;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the type
	*
	* @param String $type  the type that should be set
	* @access public
	*/
	public function setType(String $type) {
		$this->type = $type;
	}
	
	/**
	* executor for the API-request
	*
	* @return String  a token of the requested type
	* @access public
	*/
	public function execute() {
		$token = new APIRequest($this->url);
		$token->setCookieFile($this->cookiefile);
		$token->addToGetFields("action", "query");
		$token->addToGetFields("meta", "tokens");
		$token->addToGetFields("type", $this->type);
		$token->addToGetFields("format", "xml");
		return $token->execute()->query->tokens[$this->type."token"];
	}
}
?>
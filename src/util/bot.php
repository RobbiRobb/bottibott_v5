<?php
/**
* A class representing the connection to a wiki
*
* The class Bot represents the connection to a wiki, storing session and cookies
* It has to be passed to all controllers to give the necessary information which wiki to connect to
* It also has logging capabilities for debugging purposes
* It is recommended to always log in if you have access to apihighlimits
* as that will speed up the process significantly
*
* @method bool isLoggedIn()
* @method void startLogging()
* @method void stopLogging()
* @method string getToken(string $token)
* @method bool hasRight(string $right)
* @method bool login(string $username, string $password)
* @method void logout()
*/
class Bot extends Request {
	private bool $loggedIn;
	private array $tokens;
	private array $userrights;
	
	/**
	* constructor for class Bot
	*
	* @param string $url         the url to the wiki
	* @param string $cookiefile  the name of the cookiefile used for this bot instance
	* @param string $logfile     the name of the logfile used for this bot instance
	* @access public
	*/
	public function __construct(string $url, string $cookiefile = "cookies.txt", string $logfile = "latest.log") {
		$this->url = $url;
		$this->cookiefile = $cookiefile;
		$this->logger = new Logger($logfile);
		$this->loggedIn = false;
		$this->tokens = array();
		$this->userrights = array();
	}
	
	/**
	* destructor for class Bot
	*
	* @access public
	*/
	public function __destruct() {
		$this->logger->stopLogging();
	}
	
	/**
	* check for login
	*
	* @return bool  true if the bot is logged in, false otherwise
	* @access public
	*/
	public function isLoggedIn() : bool {
		return $this->loggedIn;
	}
	
	/**
	* start logging of requests
	*
	* @access public
	*/
	public function startLogging() : void {
		$this->logger->startLogging();
	}
	
	/**
	* stop logging of requests
	*
	* @access public
	*/
	public function stopLogging() : void {
		$this->logger->stopLogging();
	}
	
	/**
	* getter for a token of a given type
	*
	* @param string $type  the type of token that should be queried
	* @return string       the token
	* @access public
	*/
	public function getToken(string $type) : string {
		if(!isset($this->tokens[$type])) {
			$token = new Token($this, $type);
			$this->tokens[$type] = $token->execute();
		}
		return $this->tokens[$type];
	}
	
	/**
	* check whether the current user has a given right
	*
	* @param string $right  the right to check
	* @return bool          true if the user has the right, false if not
	* @access public
	*/
	public function hasRight(string $right) : bool {
		if(empty($this->userrights)) {
			$userrights = new Userrights($this);
			
			foreach($userrights->getRights() as $userright) {
				$this->userrights[$userright] = 1;
			}
		}
		return isset($this->userrights[trim($right)]);
	}
	
	/**
	* loggin in to a wiki with an account
	*
	* @param string $username  the username of the account that tries to log in
	* @param string $password  the password of the account that tries to log in
	* @return bool             true on success, false otherwise
	* @access public
	*/
	public function login(string $username, string $password) : bool {
		$login = new Login($this, $username, $password);
		if($login->execute()->getStatus() === "PASS") {
			$this->loggedIn = true;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* logging out of a wiki
	*
	* @access public
	*/
	public function logout() : void {
		if(!$this->isLoggedIn()) { return; }
		
		$logout = new Logout($this);
		$logout->execute();
		$this->loggedIn = false;
	}
}
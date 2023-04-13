<?php
/**
* A class for representing logins
*
* The class Login allows a user to log in to a wiki
*
* @method LoginResult execute()
*/
class Login {
	private Bot $bot;
	private string $username;
	private string $password;
	
	/**
	* constructor for class Login
	*
	* @param Bot $bot          a reference to the bot object
	* @param string $username  the name of the user
	* @param string $password  the password of the user
	* @access public
	*/
	public function __construct(Bot &$bot, string $username, string $password) {
		$this->bot = $bot;
		$this->username = $username;
		$this->password = $password;
	}
	
	/**
	* executor for login
	*
	* @return LoginResult  the result of the login
	* @access public
	*/
	public function execute() : LoginResult {
		$login = new LoginRequest($this->bot->getUrl(), $this->username, $this->password);
		$login->setCookieFile($this->bot->getCookieFile());
		$login->setLogger($this->bot->getLogger());
		return new LoginResult($login->execute($this->bot->getToken("login")));
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
		if(isset($this->bot)) { $info["bot"] = $this->bot; }
		if(isset($this->username)) { $info["username"] = $this->username; }
		if(isset($this->password)) { $info["password"] = password_hash($this->password, PASSWORD_DEFAULT); }
		return $info;
	}
}
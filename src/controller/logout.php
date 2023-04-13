<?php
/**
* A class for loggin a bot out
*
* The class Logout is used to log a logged in bot out of a wiki
*/
class Logout {
	private Bot $bot;
	
	/**
	* constructor for class Logout
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot) {
		$this->bot = $bot;
	}
	
	/**
	* executor for logout
	*
	* @access public
	*/
	public function execute() : void {
		$request = new LogoutRequest($this->bot->getUrl());
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		$request->execute($this->bot->getToken("csrf"));
	}
}
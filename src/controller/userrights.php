<?php
/**
* A class for listing userrights
*
* The class Userrights represents a list of all rights the current bot user has access to
*
* @method Generator|string getRights()
*/
class Userrights {
	private Bot $bot;
	
	/**
	* constructor for class Userrights
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot) {
		$this->bot = $bot;
	}
	
	/**
	* generator for list of rights
	*
	* @return Generator|string  a list of all rights the current user has
	* @access public
	*/
	public function getRights() : Generator|string {
		$request = new UserrightsRequest($this->bot->geturl());
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		$queryResult = $request->execute();
		
		foreach($queryResult->query->userinfo->rights->r as $userright) {
			yield (string)$userright;
		}
	}
}
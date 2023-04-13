<?php
/**
* A class for representing lists of all users
*
* The class UserList represents a list of all registered users on a wiki
*
* @method Generator|User getAllUsers()
*/
class UserList {
	private Bot $bot;
	private string $limit;
	
	/**
	* constructor for class UserList
	*
	* @param Bot $bot          a reference to the bot object
	* @param string $limit     the maximum amount of users to query
	* @param bool $active      whether to query for only active users
	* @access public
	*/
	public function __construct(Bot &$bot, string $limit = "max", bool $active = false) {
		$this->bot = $bot;
		$this->limit = $limit;
		$this->active = $active;
	}
	
	/**
	* executor for list generation
	*
	* @return Generator|User  list of users
	* @access public
	*/
	public function getAllUsers() : Generator|User {
		$continue = "";
		
		do {
			$allusers = new AllusersRequest($this->bot->getUrl(), $this->limit, $continue);
			$allusers->setCookieFile($this->bot->getCookieFile());
			$allusers->setLogger($this->bot->getLogger());
			$allusers->setActive($this->active);
			$queryResult = $allusers->execute();
			
			foreach($queryResult->query->allusers->u as $userData) {
				$user = new User((string)$userData["name"], (int)$userData["userid"]);
				$user->setEditcount((int)$userData["editcount"]);
				$user->setRegistration(strtotime((string)$userData["registration"]));
				if($this->active) { $user->setRecentactions((int)$userData["recentactions"]); }
				yield $user;
			}
			
			if(isset($queryResult->continue["aufrom"])) {
				$continue = $queryResult->continue["aufrom"];
			}
		} while(isset($queryResult->continue["aufrom"]));
	}
}
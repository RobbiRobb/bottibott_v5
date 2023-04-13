<?php
/**
* A class for collecting information about a user
*
* The class Userinfo collects information about users on a wiki
*
* @method Generator|User execute()
*/
class Userinfo {
	private Bot $bot;
	private array $users;
	
	/**
	* constructor for class Userinfo
	*
	* @param Bot $bot             a reference to the bot object
	* @Param array|string $users  a string or array of users. Multiple users as a string must be divided by "|"
	* @access public
	*/
	public function __construct(Bot &$bot, array|string $users) {
		if(gettype($users) === "string") { $users = explode("|", $users); }
		$this->bot = $bot;
		$this->users = $users;
	}
	
	/**
	* executor for userinfo
	* returns a user object if only a single user is requested
	* or a generator if multiple users are requested
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|User  a generator for all users for which the information was requested
	* @access public
	*/
	public function execute(bool $generator = false) : Generator|User {
		if(count($this->users) === 1 && !$generator) {
			$request = new UserinfoRequest($this->bot->getUrl(), implode("|", $this->users));
			$request->setCookieFile($this->bot->getCookieFile());
			$request->setLogger($this->bot->getLogger());
			$queryResult = $request->execute();
			
			foreach($queryResult->query->users->user as $user) {
				$userData = new User((string)$user["name"], (int)$user["userid"]);
				$userData->setEditcount((int)$user["editcount"]);
				$userData->setRegistration(strtotime((string)$user["registration"]));
				
				foreach($user->groups->g as $group) {
					$userData->addGroup((string)$group);
				}
				
				foreach($user->rights->r as $right) {
					$userData->addRight((string)$right);
				}
				return $userData;
			}
		} else {
			$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
			$requester = new APIMultiRequest();
			$context = &$this;
			
			return (function() use (&$max, &$requester, &$context) {
				while(count($context->users) > 0) {
					$request = new UserinfoRequest($this->bot->getUrl(), implode("|", array_slice($context->users, 0, $max)));
					$request->setCookieFile($this->bot->getCookieFile());
					$request->setLogger($this->bot->getLogger());
					$queryResult = $request->execute();
					$requester->addRequest($request->getRequest());
					$context->users = array_splice($context->users, $max);
				}
				
				foreach($requester->execute() as $queryResult) {
					foreach($queryResult->query->users->user as $user) {
						if($user["missing"]) { continue; }
						$userData = new User((string)$user["name"], (int)$user["userid"]);
						$userData->setEditcount((int)$user["editcount"]);
						$userData->setRegistration(strtotime((string)$user["registration"]));
						
						foreach($user->groups->g as $group) {
							$userData->addGroup((string)$group);
						}
						
						foreach($user->rights->r as $right) {
							$userData->addRight((string)$right);
						}
						yield $userData;
					}
				}
			})();
		}
	}
}
<?php
/**
* A class for representing lists of user contributions
*
* The class Usercontribs represents a list of users and their respective contributions to a wiki
*
* @method Generator|User getContribs(bool $generator)
*/
class Usercontribs {
	private Bot $bot;
	private array $users;
	private string $limit;
	
	/**
	* constructor for class Usercontribs
	*
	* @param Bot $bot             a reference to the bot object
	* @param array|string $users  a list of users for which the contributions will be loaded
	*                             multiple users as a string must be divided by "|"
	* @param string $limit        the maximum amount of users to query
	* @access public
	*/
	public function __construct(Bot &$bot, array|string $users, string $limit = "max") {
		if(gettype($users) === "string") { $users = explode("|", $users); }
		$this->bot = $bot;
		$this->users = $users;
		$this->limit = $limit;
	}
	
	/**
	* executor for list generation
	*
	* @return Generator|User  list of users
	* @access public
	*/
	public function getContribs(bool $generator = false) : Generator|User {
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		
		$userEdits = array();
		
		do {
			$users = array_slice($this->users, 0, $max);
			$this->users = array_splice($this->users, $max);
			
			$continue = "";
			
			do {
				$request = new UsercontribsRequest($this->bot->getUrl(), implode("|", $users), $this->limit, $continue);
				$request->setCookieFile($this->bot->getCookieFile());
				$request->setLogger($this->bot->getLogger());
				$queryResult = $request->execute();
				
				foreach($queryResult->query->usercontribs->item as $contribution) {
					if(!isset($userEdits[(string)$contribution["user"]])) {
						$userEdits[(string)$contribution["user"]] = new User((string)$contribution["user"], (int)$contribution["userid"]);
					}
					$contrib = new Revision((int)$contribution["revid"]);
					$contrib->setParentId((int)$contribution["parentid"]);
					$contrib->setSizediff((int)$contribution["sizediff"]);
					$contrib->setTimestamp(strtotime((string)$contribution["timestamp"]));
					$page = new Page((string)$contribution["title"]);
					$page->setId((int)$contribution["pageid"]);
					$page->setNamespace((int)$contribution["ns"]);
					$contrib->setPage($page);
					$userEdits[(string)$contribution["user"]]->addContribution($contrib);
				}
				
				$continue = (string)$queryResult->continue["uccontinue"];
			} while(isset($queryResult->continue["uccontinue"]));
		} while(!empty($this->users));
		
		if(count($userEdits) === 1 && $generator === false) {
			foreach($userEdits as $user) {
				return $user;
			}
		} else {
			return (function() use ($userEdits) {
				foreach($userEdits as $user) {
					yield $user;
				}
			})();
		}
	}
}
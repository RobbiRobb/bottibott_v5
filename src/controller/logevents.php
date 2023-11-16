<?php
/**
* A class for representing lists of logged events
*
* The class Logevents represents a list of events performed by users that were logged by the system
*
* @method Generator|Event getEvents()
*/
class Logevents {
	private Bot $bot;
	private string $action;
	private string $user;
	private string $namespace;
	private string $limit;
	
	/**
	* constructor for class Logevents
	*
	* @param Bot $bot           a reference to the bot object
	* @param string $action     the action that should be queried
	* @param string $user       a filter for the user responsible for the event
	* @param string $namespace  a filter for the effected namespace
	* @param string $limit      the maximum amount of logevents to be queried
	* @access public
	*/
	public function __construct(
		Bot &$bot,
		string $action,
		string $user = "",
		string $namespace = "",
		string $limit = "max"
	) {
		$this->bot = $bot;
		$this->action = $action;
		$this->user = $user;
		$this->namespace = $namespace;
		$this->limit = $limit;
	}
	
	/**
	* list generator for events
	*
	* @return Generator|Event  an event object for each object
	* @access public
	*/
	public function getEvents() : Generator|Event {
		$continue = "";
		
		do {
			$logevents = new LogeventsRequest(
				$this->bot->getUrl(),
				$this->action,
				$this->user,
				$this->namespace,
				$this->limit,
				$continue
			);
			$logevents->setCookieFile($this->bot->getCookieFile());
			$logevents->setLogger($this->bot->getLogger());
			$queryResult = $logevents->execute();
			
			foreach($queryResult->query->logevents->item as $item) {
				$event = new Event(
					(string)$item["type"],
					(string)$item["action"],
					(isset($item["title"]) ? (string)$item["title"] : (string)$item["type"])
				);
				$event->setNamespace((int)$item["ns"]);
				$event->setTimestamp(strtotime((string)$item["timestamp"]));
				$user = new User((string)$item["user"], (int)$item["userid"]);
				$event->setUser($user);
				yield $event;
			}
			
			if(isset($queryResult->continue["lecontinue"])) {
				$continue = (string)$queryResult->continue["lecontinue"];
			}
		} while(isset($queryResult->continue["lecontinue"]));
	}
}
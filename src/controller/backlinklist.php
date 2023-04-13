<?php
/**
* A class for representing lists of all pages linking to a specific page
*
* The class BacklinkList represents a list of all pages linking to a specific page
*
* @method Generator|Page getBacklinks()
*/
class BacklinkList {
	private Bot $bot;
	private string $link;
	private string $limit;
	
	/**
	* constructor for class BacklinkList
	*
	* @param Bot $bot          a reference to the bot object
	* @param string $link      the page linking to
	* @param string $limit     the maximum amount of pages to query
	* @access public
	*/
	public function __construct(Bot &$bot, string $link, string $limit = "max") {
		$this->bot = $bot;
		$this->link = $link;
		$this->limit = $limit;
	}
	
	/**
	* executor for list generation
	*
	* @return Generator|Page  list of users
	* @access public
	*/
	public function getBacklinks() : Generator|Page {
		$continue = "";
		
		do {
			$backlinks = new BacklinksRequest($this->bot->getUrl(), $this->link, $this->limit, $continue);
			$backlinks->setCookieFile($this->bot->getCookieFile());
			$backlinks->setLogger($this->bot->getLogger());
			$queryResult = $backlinks->execute();
			
			foreach($queryResult->query->backlinks->bl as $backlink) {
				$res = new Page((string)$backlink["title"]);
				$res->setId((int)$backlink["pageid"]);
				$res->setNamespace((int)$backlink["ns"]);
				yield $res;
			}
			
			if(isset($queryResult->continue["blcontinue"])) {
				$continue = $queryResult->continue["blcontinue"];
			}
		} while(isset($queryResult->continue["blcontinue"]));
	}
}
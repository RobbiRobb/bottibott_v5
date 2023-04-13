<?php
/**
* A class for representing lists of all pages transcluding a specific page
*
* The class TransclusionList represents a list of all pages transcluding a specific page
*
* @method Generator|Page getTransclusions()
*/
class TransclusionList {
	private Bot $bot;
	private string $link;
	private string $limit;
	
	/**
	* constructor for class TransclusionList
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
	* @return Generator|Page  list of pages
	* @access public
	*/
	public function getTransclusions() : Generator|Page {
		$continue = "";
		
		do {
			$transclusions = new TransclusionsRequest($this->bot->getUrl(), $this->link, $this->limit, $continue);
			$transclusions->setCookieFile($this->bot->getCookieFile());
			$transclusions->setLogger($this->bot->getLogger());
			$queryResult = $transclusions->execute();
			
			foreach($queryResult->query->embeddedin->ei as $transclusion) {
				$res = new Page((string)$transclusion["title"]);
				$res->setId((int)$transclusion["pageid"]);
				$res->setNamespace((int)$transclusion["ns"]);
				yield $res;
			}
			
			if(isset($queryResult->continue["eicontinue"])) {
				$continue = $queryResult->continue["eicontinue"];
			}
		} while(isset($queryResult->continue["eicontinue"]));
	}
}
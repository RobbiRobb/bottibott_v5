<?php
/*
* A class for representing lists of language links
*
* The class Langlinks represents lists of langlinks of one or multiple pages
*
* @method Generator|Page getLinks(bool $generator)
*/
class Langlinks {
	private Bot $bot;
	private array $titles;
	private string $lang;
	private string $limit;
	
	/**
	* constructor for class Langlinks
	*
	* @param Bot $bot              a reference to the bot object
	* @param string|array $titles  a string or array of titles. Multiple titles as a string must be divided by "|"
	* @param string $lang          a language code to filter only langlinks for that language
	* @param string $limit         the maximum amount of fileusages to query
	* @access public
	*/
	public function __construct(Bot &$bot, array|string $titles, string $lang = "", string $limit = "max") {
		if(gettype($titles) === "string") { $titles = explode("|", $titles); }
		$this->bot = $bot;
		$this->titles = $titles;
		$this->lang = $lang;
		$this->limit = $limit;
	}
	
	/**
	* executor for langlink list generation
	* will evaluate all requests before yielding pages to make sure all lists are complete
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  list of pages or a page if only usages for one page were requested
	* @access public
	*/
	public function &getLinks(bool $generator = false) : Generator|Page {
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		
		$links = array();
		
		do {
			$queryLinks = array_slice($this->titles, 0, $max);
			$this->titles = array_splice($this->titles, $max);
			$this->continue = "";
			
			do {
				$langlinks = new LanglinksRequest(
					$this->bot->getUrl(),
					implode("|", $queryLinks),
					$this->lang,
					$this->limit,
					$this->continue
				);
				$langlinks->setCookieFile($this->bot->getCookieFile());
				$langlinks->setLogger($this->bot->getLogger());
				$queryResult = $langlinks->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($links[(string)$page["title"]])) {
						$linkingPage = new Page((string)$page["title"]);
						if(isset($page["missing"])) {
							$linkingPage->setExists(false);
							$links[(string)$page["title"]] = $linkingPage;
							continue;
						}
						$linkingPage->setId((int)$page["pageid"]);
						$linkingPage->setNamespace((int)$page["ns"]);
						$links[(string)$page["title"]] = $linkingPage;
					}
					if(isset($page->langlinks)) {
						foreach($page->langlinks->ll as $langlink) {
							$links[(string)$page["title"]]->addLangLink((string)$langlink["lang"], (string)$langlink);
						}
					}
				}
				
				if(isset($queryResult->continue["llcontinue"])) {
					$this->continue = (string)$queryResult->continue["llcontinue"];
				}
			} while(isset($queryResult->continue["llcontinue"]));
		} while(!empty($this->titles));
		
		if(count($links) === 1 && $generator === false) {
			foreach($links as $page) {
				return $page;
			}
		} else {
			return (function() use ($links) {
				foreach($links as $page) {
					yield $page;
				}
			})();
		}
	}
}
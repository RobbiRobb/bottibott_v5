<?php
/**
* A class for creaiting lists of pages a page links to
*
* The class Links represents a list of all pages a page links to
*
* @method Generator|Page getLinks(bool $generator)
*/
class Links {
	private Bot $bot;
	private array $titles;
	private array $targets;
	private string $namespace;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class Links
	*
	* @param Bot $bot               a reference to the bot object
	* @param string|array $titles   a string or array of pages. Multiple pages as a string must be divided by "|"
	* @param string|array $targets  a string or array of pages. Multiple pages as a string must be divided by "|"
	* @param string $namespace      the namespace to limit links to
	* @param string $limit          the maximum amount of pages to query
	* @param string $continue       value for additional queries
	* @access public
	*/
	public function __construct(
		Bot &$bot,
		array|string $titles,
		array|string $targets = "",
		string $namespace = "*",
		string $limit = "max",
		string $continue = ""
	) {
		if(gettype($titles) === "string") { $titles = explode("|", $titles); }
		if(gettype($targets) === "string") { $targets = explode("|", $targets); }
		$this->bot = $bot;
		$this->titles = $titles;
		$this->targets = $targets;
		$this->namespace = $namespace;
		$this->limit = $limit;
		$this->continue = $continue;
	}
	
	/**
	* executor for list generation
	* returns a page object if only a single page is requested
	* or a generator if multiple pages are requested
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  a generator for all pages for which the links were requested
	* @access public
	*/
	public function getLinks(bool $generator = false) : Generator|Page {
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		if(count($this->targets) > ($this->bot->hasRight("apihighlimits") ? 500 : 50)) { throw new Error("Too many targets."); }
		
		$linksList = array();
		
		do {
			$toQuery = array_slice($this->titles, 0, $max);
			$this->titles = array_splice($this->titles, $max);
			$this->continue = "";
			
			do {
				$links = new LinksRequest(
					$this->bot->getUrl(),
					implode("|", $toQuery),
					implode("|", $this->targets),
					$this->namespace,
					$this->limit,
					$this->continue
				);
				$links->setCookieFile($this->bot->getCookieFile());
				$links->setLogger($this->bot->getLogger());
				$queryResult = $links->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($page->links)) { continue; }
					
					if(!isset($linksList[(string)$page["title"]])) {
						$res = new Page((string)$page["title"]);
						$res->setId((int)$page["pageid"]);
						$res->setNamespace((int)$page["ns"]);
						$linksList[(string)$page["title"]] = $res;
					}
					
					foreach($page->links->pl as $link) {
						$pl = new Page((string)$link["title"]);
						$pl->setNamespace((int)$link["ns"]);
						$linksList[(string)$page["title"]]->addLink($pl);
					}
				}
				
				if(isset($queryResult->continue["plcontinue"])) {
					$this->continue = $queryResult->continue["plcontinue"];
				}
			} while(isset($queryResult->continue["plcontinue"]));
		} while(!empty($this->titles));
		
		if(count($linksList) === 1 && $generator === false) {
			foreach($linksList as $page) {
				return $page;
			}
		} else {
			return (function() use ($linksList) {
				foreach($linksList as $page) {
					yield $page;
				}
			})();
		}
	}
}
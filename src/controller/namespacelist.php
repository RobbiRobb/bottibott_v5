<?php
/**
* A class for representing lists of all pages in a namespace
*
* The class NamespaceList represents lists of all pages in a given namespace
*
* @method Generator|Page getAllPages()
*/
class NamespaceList {
	private Bot $bot;
	private int $namespace;
	private string $filter;
	private string $limit;
	
	/**
	* constructor for class NamespaceList
	*
	* @param Bot $bot           a reference to the bot object
	* @param int $namespace     the namespace to get pages from
	* @param string $limit      the maximum amount of pages to query
	* @param string $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @access public
	*/
	public function __construct(Bot &$bot, int $namespace, string $filter = "all", string $limit = "max") {
		$this->bot = $bot;
		$this->namespace = $namespace;
		$this->filter = $filter;
		$this->limit = $limit;
	}
	
	/**
	* executor for list generation
	*
	* @return Generator|Page  list of pages
	* @access public
	*/
	public function getAllPages() : Generator|Page {
		$continue = "";
		
		do {
			$allpages = new AllpagesRequest($this->bot->getUrl(), $this->namespace, $this->limit, $this->filter, $continue);
			$allpages->setCookieFile($this->bot->getCookieFile());
			$allpages->setLogger($this->bot->getLogger());
			$queryResult = $allpages->execute();
			
			foreach($queryResult->query->allpages->p as $page) {
				$res = new Page((string)$page["title"]);
				$res->setId((int)$page["pageid"]);
				$res->setNamespace($this->namespace);
				yield $res;
			}
			
			if(isset($queryResult->continue["apcontinue"])) {
				$continue = $queryResult->continue["apcontinue"];
			}
		} while(isset($queryResult->continue["apcontinue"]));
	}
}
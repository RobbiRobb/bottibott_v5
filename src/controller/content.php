<?php
/**
* A class for representing the content of pages
*
* The class Content represents the content of one or multiple pages
*
* @method Generator|Page get(bool $generator)
* @method Generator|Page fromNamespace(int $namespace, string $filter, string $limit)
* @method Generator|Page fromBacklinks(string $link, string $limit)
* @method Generator|Page fromCategorymembers(string $category, string $limit, array $types)
* @method Generator|Page fromLinklist(string $link, string $limit)
* @method Generator|Page fromTransclusions(string $link, string $limit)
*/
class Content {
	private Bot $bot;
	private string|array $pages;
	
	/**
	* constructor for class Content
	*
	* @param Bot $bot             a reference to the bot object
	* @param string|array $pages  a string or array of pages. Multiple pages as a string must be divided by "|"
	* @access public
	*/
	public function __construct(Bot &$bot, string|array $pages = "") {
		if(gettype($pages) === "string") { $pages = explode("|", $pages); }
		$this->bot = $bot;
		$this->pages = $pages;
	}
	
	/**
	* executor for content
	* returns a page object if only a single page is requested
	* or a generator if multiple pages are requested
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  a generator for all pages for which the content was requested
	* @access public
	*/
	public function get(bool $generator = false) : Generator|Page {
		if(count($this->pages) === 1 && !$generator) {
			$request = new ContentRequest($this->bot->getUrl(), implode($this->pages));
			$request->setCookieFile($this->bot->getCookieFile());
			$request->setLogger($this->bot->getLogger());
			$queryResult = $request->execute();
			
			foreach($queryResult->query->pages->page as $pageContent) {
				$page = new Page(implode($this->pages));
				if(isset($pageContent["missing"])) {
					$page->setExists(false);
					return $page;
				}
				$page->setContent((string)$pageContent->revisions->rev->slots->slot);
				return $page;
			}
		} else {
			$redo = array();
			
			$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
			$requester = new APIMultiRequest();
			$context = &$this;
			
			return (function() use (&$redo, &$max, &$requester, &$context) {
				while(count($context->pages) > 0) {
					$request = new ContentRequest($this->bot->getUrl(), implode("|", array_slice($context->pages, 0, $max)));
					$request->setCookieFile($this->bot->getCookieFile());
					$request->setLogger($this->bot->getLogger());
					$requester->addRequest($request->getRequest());
					$context->pages = array_splice($context->pages, $max);
				}
				
				foreach($requester->execute() as $queryResult) {
					if(isset($queryResult->query)) {
						foreach($queryResult->query->pages->page as $content) {
							if(isset($content["missing"])) {
								$page = new Page((string)$content["title"]);
								$page->setExists(false);
								yield $page;
								continue;
							}
							if(!isset($content->revisions->rev)) {
								array_push($redo, (string)($content["title"]));
							} else {
								$page = new Page((string)$content["title"]);
								$page->setContent((string)$content->revisions->rev->slots->slot);
								yield $page;
							}
						}
					}
				}
				
				if(!empty($redo)) {
					$context->pages = $redo;
					yield from $context->get(true);
				}
			})();
		}
	}
	
	/**
	* list generator for namespaces
	*
	* @param int $namespace   the namespace the pages belong to
	* @param string $limit    the maximum amount of pages to query
	* @param Strnig $filter   filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @return Generator|Page  a generator for all pages linking to the page
	* @access public
	*/
	public function fromNamespace(int $namespace, string $filter = "all", string $limit = "max") : Generator|Page {
		$allpages = new NamespaceList($this->bot, $namespace, $filter, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($allpages->getAllPages(), false));
		yield from $this->get(true);
	}
	
	/**
	* list generator for backlinks
	*
	* @param string $link     the linked page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages linking to the page
	* @access public
	*/
	public function fromBacklinks(string $link, string $limit = "max") : Generator|Page {
		$backlinks = new BacklinkList($this->bot, $link, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($backlinks->getBacklinks(), false));
		yield from $this->get(true);
	}
	
	/**
	* list generator for all category members
	*
	* @param string $category  the linked page
	* @param string $limit     the maximum amount of pages to query
	* @param array $types      an array containing the types of the query
	*                          may contain any but at least one of "page", "subcat" or "file"
	* @return Generator|Page   a generator for all pages transcluded on or linking to the page
	* @access public
	*/
	public function fromCategorymembers(
		string $category,
		string $limit = "max",
		array $types = array("page", "subcat", "file")
	) : Generator|Page {
		$categorymembers = new Category($this->bot, $category, $limit, $types);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($categorymembers->getMembers(), false));
		yield from $this->get(true);
	}
	
	/**
	* list generator for an entire linklist
	* this includes both backlinks and transclusions
	*
	* @param string $link     the linked page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages transcluded on or linking to the page
	* @access public
	*/
	public function fromLinklist(string $link, string $limit = "max") : Generator|Page {
		yield from $this->fromBacklinks($link, $limit);
		yield from $this->fromTransclusions($link, $limit);
	}
	
	/**
	* list generator for transclusions
	*
	* @param string $link     the transcluded page
	* @param string $limit    the maximum amount of pages to query
	* @return Generator|Page  a generator for all pages transcluded on the page
	* @access public
	*/
	public function fromTransclusions(string $link, string $limit = "max") : Generator|Page {
		$transclusions = new TransclusionList($this->bot, $link, $limit);
		$this->pages = array_map(function(Page $page) {
			return $page->getTitle();
		}, iterator_to_array($transclusions->getTransclusions(), false));
		yield from $this->get(true);
	}
}
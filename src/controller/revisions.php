<?php
/**
* A class for representing Revisions
*
* The class Revisions represents a list of revisions on a wiki
*
* @method void setIds(array|string $ids)
* @method void setPages(array|string $pages)
* @method void setLimit(string $limit)
* @method Generator|Revision getRevisionsFromRevids(bool $generator)
* @method Generator|Page getRevisionsFromPages(bool $generator)
*/
class Revisions {
	private Bot $bot;
	private array $ids;
	private array $pages;
	
	/**
	* constructor for class Revisions
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot) {
		$this->bot = $bot;
	}
	
	/**
	* setter for ids
	*
	* @param string|array $ids  a string or array of ids. Multiple ids as a string must be divided by "|"
	* @access public
	*/
	public function setIds(array|string $ids) : void {
		$this->ids = gettype($ids) === "string" ? explode("|", $ids) : $ids;
	}
	
	/**
	* setter for pages
	*
	* @param string|array $pages  a string or array of pages. Multiple pages as a string must be divided by "|"
	* @access public
	*/
	public function setPages(array|string $pages) : void {
		$this->pages = gettype($pages) === "string" ? explode("|", $pages) : $pages;
	}
	
	/**
	* setter for the limit
	*
	* @param string $limit  the limit that should be set
	* @access public
	*/
	public function setLimit(string $limit) : void {
		$this->limit = $limit;
	}
	
	/**
	* list generator for revisions from ids
	* returns a revision object if only a single revisions is requested
	* or a generator if multiple revisions are requested
	*
	* @param bool $generator      will always return a generator if set to true
	* @return Generator|Revision  a revision or a generator for all revisions that were requested
	* @access public
	*/
	public function getRevisionsFromRevids(bool $generator = false) : Generator|Revision {
		if(!isset($this->ids)) { throw new Error("Cannot query revisions from ids without setting ids"); }
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		$requester = new APIMultiRequest();
		$revisions = array();
		
		while(count($this->ids) > 0) {
			$request = new RevisionsRequest($this->bot->getUrl());
			$request->setRevids(implode("|", array_slice($this->ids, 0, $max)));
			$request->setCookieFile($this->bot->getCookieFile());
			$request->setLogger($this->bot->getLogger());
			$requester->addRequest($request->getRequest());
			$this->ids = array_splice($this->ids, $max);
		}

		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query->pages)) {
				foreach($queryResult->query->pages->page as $page) {
					$pageData = new Page((string)$page["title"]);
					$pageData->setId((int)$page["pageid"]);
					$pageData->setNamespace((int)$page["ns"]);
					$pageData->setExists(true);
					
					foreach($page->revisions->rev as $revision) {
						$revisionData = new Revision((int)$revision["revid"]);
						$revisionData->setParentId((int)$revision["parentid"]);
						$revisionData->setTimestamp(strtotime((string)$revision["timestamp"]));
						$user = new User((string)$revision["user"], (int)$revision["userid"]);
						$revisionData->setUser($user);
						$revisionData->setPage($pageData);
						
						$revisions[(int)$revision["revid"]] = $revisionData;
					}
				}
			}
			if(isset($queryResult->query->badrevids)) {
				foreach($queryResult->query->badrevids->rev as $revision) {
					$revisionData = new Revision((int)$revision["revid"], true);
					
					$revisions[(int)$revision["revid"]] = $revisionData;
				}
			}
		}
		
		ksort($revisions);
		
		if(count($revisions) === 1 && $generator === false) {
			foreach($revisions as $revision) {
				return $revision;
			}
		} else {
			return (function() use ($revisions) {
				foreach($revisions as $revision) {
					yield $revision;
				}
			})();
		}
	}
	
	/**
	* list generator for revisions from pages
	* returns a page object if revisions of one page are requested
	* or a generator if multiple pages are requested
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  a page or a generator for all page that were requested
	* @access public
	*/
	public function getRevisionsFromPages(bool $generator = false) : Generator|Page {
		if(!isset($this->pages)) { throw new Error("Cannot query revisions from pages without setting pages"); }
		
		$pages = array();
		
		foreach($this->pages as $page) {
			$continue = "";
			
			do {
				$request = new RevisionsRequest($this->bot->getUrl());
				$request->setPage($page);
				$request->setLimit(isset($this->limit) ? $this->limit : "max");
				$request->setContinue($continue);
				$request->setCookieFile($this->bot->getCookieFile());
				$request->setLogger($this->bot->getLogger());
				$queryResult = $request->execute();
				
				$pageTitle = (string)$queryResult->query->pages->page["title"];
				
				if(!isset($pages[$pageTitle])) {
					$pageData = new Page((string)$queryResult->query->pages->page["title"]);
					if(isset($queryResult->query->pages->page["missing"])) {
						$pageData->setExists(false);
						$pages[$pageTitle] = $pageData;
						break;
					} else {
						$pageData->setExists(true);
					}
					$pageData->setId((int)$queryResult->query->pages->page["pageid"]);
					$pageData->setNamespace((int)$queryResult->query->pages->page["ns"]);
					$pages[$pageTitle] = $pageData;
				}
				
				foreach($queryResult->query->pages->page->revisions->rev as $revision) {
					$revisionData = new Revision((int)$revision["revid"]);
					$revisionData->setParentId((int)$revision["parentid"]);
					$revisionData->setTimestamp(strtotime((string)$revision["timestamp"]));
					$user = new User((string)$revision["user"], (int)$revision["userid"]);
					$revisionData->setUser($user);
					$pages[$pageTitle]->addRevision($revisionData);
				}
				
				$continue = (string)$queryResult->continue["rvcontinue"];
			} while(isset($queryResult->continue["rvcontinue"]));
		}
		
		if(count($pages) === 1 && $generator === false) {
			foreach($pages as $page) {
				return $page;
			}
		} else {
			return (function() use ($pages) {
				foreach($pages as $page) {
					yield $page;
				}
			})();
		}
	}
}
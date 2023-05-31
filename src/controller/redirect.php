<?php
/**
* A class for representing redirects
*
* The class Redirect resolves page titles to test if they are redirects or not
*
* @method void setTitles(string|array $titles)
* @method Generator|Page resolve(bool $generator)
*/
class Redirect {
	private Bot $bot;
	private array $titles;
	
	/**
	* constructor for class Redirect
	*
	* @param Bot $bot  a reference to the bot object
	* @param string|array $titles  the titles to set
	* @access public
	*/
	public function __construct(Bot &$bot, string|array $titles = array()) {
		if(gettype($titles) === "string") { $titles = explode("|", $titles); }
		$this->bot = $bot;
		$this->titles = $titles;
	}
	
	/**
	* setter for the titles that should be queried
	*
	* @param string|array $titles  the titles to set
	* @access public
	*/
	public function setTitles(string|array $titles) : void {
		if(gettype($titles) === "string") { $titles = explode("|", $titles); }
		$this->titles = $titles;
	}
	
	/**
	* resolver for redirects
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  a page or a generator for all page that were requested
	* @access public
	*/
	public function resolve(bool $generator = false) : Generator|Page {
		if(empty($this->titles)) { throw new Error("Cannot query redirects without setting titles"); }
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		$requester = new APIMultiRequest();
		$pages = array();
		$pageNames = $this->titles;
		
		do {
			$request = new RedirectRequest($this->bot->getUrl());
			$request->setTitles(implode("|", array_slice($this->titles, 0, $max)));
			$request->setCookieFile($this->bot->getCookieFile());
			$request->setLogger($this->bot->getLogger());
			$requester->addRequest($request->getRequest());
			$this->titles = array_splice($this->titles, $max);
		} while(!empty($this->titles));
		
		foreach($requester->execute() as $queryResult) {
			foreach($queryResult->query->redirects->r as $redirect) {
				$page = new Page((string)$redirect["from"]);
				$page->setIsRedirect(true);
				$page->setRedirectsTo((string)$redirect["to"]);
				
				unset($pageNames[array_keys($pageNames, (string)$redirect["from"])[0]]);
				array_push($pages, $page);
			}
		}
		
		foreach($pageNames as $pageName) {
			$page = new Page($pageName);
			$page->setIsRedirect(false);
			
			array_push($pages, $page);
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
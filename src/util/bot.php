<?php
/**
* A class representing the entry point to all operations performed by the bot
*
* The class Bot represents the bot itself. It has a defined set of actions that are possible to read and write data from a wiki
* Most of the methods require readapi, which usually is allowed for all users, but might need a login in some cases, depending on the configuration of the wiki
* Some of the methods require writeapi, which may require you to log in to the wiki before using them if writeapi is denied for guests
* Depending on if you are logged in or not, you may experience lower limits. Make sure to account for that and choose the highest allowed limit for maximum efficiency
*
* @method String getUrl()
* @method bool isLoggedIn()
* @method void startLogging()
* @method void stopLogging()
* @method SimpleXMLElement delete(String $title, String $reason)
* @method SimpleXMLElement edit(String $page, String $content, String $summary, String $isbot, String $isminor)
* @method boolean exists(String $page)
* @method Page expandTemplates(String $content)
* @method Generator|Page expandTemplatesFromTitles(Array $titles)
* @method Generator|Page expandTemplatesFromBacklinks(Array $backlinks)
* @method Generator|Page expandTemplatesFromCategories(Array $categories)
* @method Generator|Page expandTemplatesFromLinklists(Array $linklists)
* @method Generator|Page expandTemplatesFromNamespaces(Array $namespaces)
* @method Generator|Page expandTemplatesFromTransclusions(Array $transclusions)
* @method String expandWikitext(String $text, String $title)
* @method Generator|String getActiveUsers(String $limit, String $continue)
* @method Generator|String getAllpages(String $namespace, String $filter, String $limit, String $continue)
* @method Generator|String getAllpagesContents(String $namespace, String $filter, String $limit)
* @method Generator|Array getAllusers(String $limit, String $continue)
* @method Generator|String getBacklinks(String $link, String $limit, String $continue)
* @method Generator|String getBacklinksContents(String $link, String $limit)
* @method Generator|Array getCategories(Array $pages, String $limit, Array $filter)
* @method Generator|String getCategoryMembers(String $category, String $limit, Array $types, String $continue)
* @method Generator|String getCategoryMembersContents(String $category, String $limit, Array $types)
* @method Generator|Array getContent(Array|String $articles)
* @method CurlHandle getContentRequest(String $articles)
* @method CurlHandle getEditRequest(String $page, String $content, String $summary, String $isbot, String $isminor)
* @method Generator|Array getFileusage(Array $files, String $namespace, String $limit)
* @method Generator|Array getLanglinks(String $titles, String $lang, String $limit)
* @method Generator|String getLinklist(String $link, String $limit)
* @method Generator|String getLinklistContents(String $link, String $limit)
* @method Generator|Array getLinks(String $titles, String $targets, String $namespace, String $limit, String $continue)
* @method Generator|Array getLogevents(String $action, String $user, String $namespace, String $limit, String $continue)
* @method CurlHandle getMoveRequest(String $from, String $to, String $reason, String $noredirect, String $movetalk)
* @method Generator|Array getRevisions(String $revids)
* @method Generator|String getRevisionUsers(String $page, String $limit, String $continue)
* @method Generator|String getSystemEditCount(String $users)
* @method Array getTemplateParameters(String $content)
* @method String getToken(String $token)
* @method Generator|String getTransclusions(String $link, String $limit, String $continue)
* @method Generator|String getTransclusionsContents(String $link, String $limit)
* @method Generator|Array getUsercontribs(String $user, String $limit, String $continue)
* @method bool hasRight(String $right)
* @method Generator|Array isRedirect(String $titles)
* @method mixed login(String $username, String $password)
* @method SimpleXMLElement logout()
* @method SimpleXMLElement move(String $from, String $to, String $reason, String $noredirect, String $movetalk)
* @method Wikitextparser parse(String $title, String $text)
* @method SimpleXMLElement undo(String $page, String $revision, String $summary, String $isbot, String $isminor)
* @method SimpleXMLElement upload(String $filepath, String $filename, String $text, String $comment, String $ignorewarnings)
* @method SimpleXMLElement uploadbyurl(String $fileurl, String $filename, String $text, String $comment, String $ignorewarnings)
*/
class Bot extends Request {
	private bool $loggedIn = false;
	private array $tokens = array();
	private array $userrights = array();
	
	/**
	* constructor for class Bot
	*
	* @param String $url  the url to the wiki
	* @access public
	*/
	public function __construct(String $url, String $logfile = "latest.log") {
		$this->url = $url;
		$this->cookiefile = "cookies.txt";
		$this->logger = new Logger($logfile);
	}
	
	/**
	* destructor for class Bot
	* 
	* @access public
	*/
	public function __destruct() {
		$this->logger->stopLogging();
	}
	
	/**
	* getter for the url
	*
	* @return String  the url to the wiki
	* @access public
	*/
	public function getUrl() {
		return $this->url;
	}
	
	/**
	* check for login
	*
	* @return bool  true if the bot is logged in, false otherwise
	* @access public
	*/
	public function isLoggedIn() {
		return $this->loggedIn;
	}
	
	/**
	* start logging of requests
	*
	* @access public
	*/
	public function startLogging() {
		$this->logger->startLogging();
	}
	
	/**
	* stop logging of requests
	*
	* @access public
	*/
	public function stopLogging() {
		$this->logger->stopLogging();
	}
	
	/**
	* deleting a page
	*
	* @param String $title      the title of the page that should be deleted
	* @param String $reason     the reason why the page should be deleted
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function delete(String $title, String $reason = "") {
		$delete = new Delete($this->url, $title, $reason);
		$delete->setCookieFile($this->cookiefile);
		$delete->setLogger($this->logger);
		return $delete->execute($this->getToken("csrf"));
	}
	
	/**
	* editing a page
	*
	* @param String $page       the page that will be edited
	* @param String $content    the new content of the page
	* @param String $summary    the summary of the edit
	* @param String $isbot      if the account editing is a bot or not
	* @param String $isminor    if the edit should be marked as minor or not
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function edit(String $page, String $content, String $summary = "", String $isbot = "1", String $isminor = "1") {
		$edit = new Edit($this->url, $page, $content, $summary, $isbot, $isminor);
		$edit->setCookieFile($this->cookiefile);
		$edit->setLogger($this->logger);
		return $edit->execute($this->getToken("csrf"));
	}
	
	/**
	* check if a page exists
	*
	* @param String $page  the page to check
	* @return boolean      wether the page exists or not
	* @access public
	*/
	public function exists(String $page) {
		return $this->getContent($page)->current() !== NULL;
	}
	
	/**
	* handler for expanding templates
	*
	* @param String $content  the content that should be expanded
	* @return Page            a page object containing all templates that were expanded
	* @access public
	*/
	public function expandTemplates(String $content) {
		$parsetree = new Parsetree($this->url);
		$parsetree->setCookieFile($this->cookiefile);
		$parsetree->setLogger($this->logger);
		$parsetree->setContent($content);
		return $parsetree->execute();
	}
	
	/**
	* handler for expanding templates from (multiple) page titles
	*
	* @param Array $titles    an array of the titles that should be expanded
	* @return Generator|Page  a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromTitles(Array $titles) {
		$requester = new APIMultiRequest();
		
		foreach($titles as $title) {
			$parsetree = new Parsetree($this->url);
			$parsetree->setCookieFile($this->cookiefile);
			$parsetree->setTitle($title);
			$requester->addRequest($parsetree->getRequest());
		}
		
		$index = 0;
		
		foreach($requester->execute() as $queryResult) {
			$index++;
			
			$parsetree = new Parsetree($this->url);
			$parsetree->setTitle((String)$queryResult->parse["title"]);
			$parsetree->setExpandedContent((String)$queryResult->parse->parsetree);
			try {
				$res = $parsetree->parseToPage();
			} catch(Error $e) {
				//ignore and continue
				continue;
			}
			yield $res;
			
			if($index % 5000 === 0) {
				sleep(30);
			}
		}
	}
	
	/**
	* handler for expanding templates from (multiple) backlinks
	*
	* @param Array $backlinks  an array of backlinks
	* @return Generator|Page   a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromBacklinks(Array $backlinks) {
		$titles = array();
		
		foreach($backlinks as $backlink) {
			foreach($this->getBacklinks($backlink) as $page) {
				array_push($titles, $page);
			}
		}
		
		yield from $this->expandTemplatesFromTitles($titles);
	}
	
	/**
	* handler for expanding templates from (multiple) categories
	*
	* @param Array $categories  an array of categories
	* @return Generator|Page    a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromCategories(Array $categories) {
		$titles = array();
		
		foreach($categories as $categorie) {
			foreach($this->getCategoryMembers($categorie) as $page) {
				array_push($titles, $page);
			}
		}
		
		yield from $this->expandTemplatesFromTitles($titles);
	}
	
	/**
	* handler for expanding templates from (multiple) linklists
	*
	* @param Array $linklists  an array of linklists
	* @return Generator|Page   a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromLinklists(Array $linklists) {
		$titles = array();
		
		foreach($linklists as $linklist) {
			foreach($this->getLinklist($linklist) as $page) {
				array_push($titles, $page);
			}
		}
		
		yield from $this->expandTemplatesFromTitles($titles);
	}
	
	/**
	* handler for expanding templates from (multiple) namespaces
	*
	* @param Array $namespaces  an array of namespaces
	* @return Generator|Page    a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromNamespaces(Array $namespaces) {
		$titles = array();
		
		foreach($namespaces as $namespace) {
			foreach($this->getAllpages($namespace, "nonredirects") as $page) {
				array_push($titles, $page);
			}
		}
		
		yield from $this->expandTemplatesFromTitles($titles);
	}
	
	/**
	* handler for expanding templates from (multiple) transcluded pages
	*
	* @param Array $transclusions  an array of transcluded pages
	* @return Generator|Page       a page object with all templates transcluded on that page
	* @access public
	*/
	public function expandTemplatesFromTransclusions(Array $transclusions) {
		$titles = array();
		
		foreach($transclusions as $transclusion) {
			foreach($this->getTransclusions($transclusion) as $page) {
				array_push($titles, $page);
			}
		}
		
		yield from $this->expandTemplatesFromTitles($titles);
	}
	
	/**
	* expander for wikitext
	*
	* @param String $text   the text to be expanded
	* @param String $title  a page title for page-sensitive expanding
	* @return String        the expanded wikitext
	* @access public
	*/
	public function expandWikitext(String $text, String $title = "") {
		$wikitext = new Wikitext($this->url, $text, $title);
		$wikitext->setCookieFile($this->cookiefile);
		$wikitext->setLogger($this->logger);
		return $wikitext->execute();
	}
	
	/**
	* generator for all active users in a wiki
	*
	* @param String $limit     the maximum amount of users to query
	* @param String $continue  value for additional queries
	* @return Generator|String  the username and their recentactions in a key => value form
	* @access public
	*/
	public function getActiveUsers(String $limit = "max", String $continue = "") {
		$allusers = new Allusers($this->url, $limit, $continue);
		$allusers->setActive(true);
		$allusers->setCookieFile($this->cookiefile);
		$allusers->setLogger($this->logger);
		$queryResult = $allusers->execute();
		
		foreach($queryResult->query->allusers->u as $user) {
			yield (String)$user["name"] => (String)$user["recentactions"];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getActiveUsers($limit, $queryResult->continue["aufrom"]);
		}
	}
	
	/**
	* generator for all pages of a given namespace
	*
	* @param String $namespace  the namespace to get pages from
	* @param String $limit      the maximum amount of pages to query
	* @param Strnig $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @param String $continue   value for additional queries
	* @return Generator|String  all page titles in a given namespace
	* @access public
	*/
	public function getAllpages(String $namespace, String $filter = "all", String $limit = "max", String $continue = "") {
		$allpages = new Allpages($this->url, $namespace, $limit, $filter, $continue);
		$allpages->setCookieFile($this->cookiefile);
		$allpages->setLogger($this->logger);
		$queryResult = $allpages->execute();
		
		foreach($queryResult->query->allpages->p as $page) {
			yield (String)$page["title"];
		}
		
		if(isset($queryResult->continue["apcontinue"])) {
			yield from $this->getAllpages($namespace, $filter, $limit, $queryResult->continue["apcontinue"]);
		}
	}
	
	/**
	* generator for the content of all pages of a given namespace
	*
	* @param String $namespace  the namespace to get pages from
	* @param Strnig $filter     filter for the type of pages. Allowed values are "all", "redirects" and "nonredirects"
	* @param String $limit      the maximum amount of pages to query
	* @return Generator|String  title and content of a page in the given namespace
	* @access public
	*/
	public function getAllpagesContents(String $namespace, String $filter = "all", String $limit = "max") {
		$pages = iterator_to_array($this->getAllpages($namespace, $filter, $limit), false);
		yield from $this->getContent($pages);
	}
	
	/**
	* generator for all users on a wiki
	*
	* @param String $limit     the maximum amount of users to query
	* @param String $continue  value for additional queries
	* @return Generator|Array  an array containing the user and the id of a user
	* @access public
	*/
	public function getAllusers(String $limit = "max", String $continue = "") {
		$allusers = new Allusers($this->url, $limit, $continue);
		$allusers->setCookieFile($this->cookiefile);
		$allusers->setLogger($this->logger);
		$queryResult = $allusers->execute();
		
		foreach($queryResult->query->allusers->u as $user) {
			yield ["user" => (String)$user["name"], "userid" => (String)$user["userid"]];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getAllusers($limit, $queryResult->continue["aufrom"]);
		}
	}
	
	/**
	* generator for all pages linking a given page
	*
	* @param String $link       the page linking to
	* @param String $limit      the maximum amount of pages to query
	* @param String $continue   value for additional queries
	* @return Generator|String  all page titles linking to a given page
	* @access public
	*/
	public function getBacklinks(String $link, String $limit = "max", String $continue = "") {
		$backlinks = new Backlinks($this->url, $link, $limit, $continue);
		$backlinks->setCookieFile($this->cookiefile);
		$backlinks->setLogger($this->logger);
		$queryResult = $backlinks->execute();
		
		foreach($queryResult->query->backlinks->bl as $backlink) {
			yield (String)$backlink["title"];
		}
		
		if(isset($queryResult->continue["blcontinue"])) {
			yield from $this->getBacklinks($link, $limit, $queryResult->continue["blcontinue"]);
		}
	}
	
	/**
	* generator for the content of all pages linking to a given page
	*
	* @param String $link       the page linking to
	* @param String $limit      the maximum amount of pages to query
	* @return Generator|String  title and content of a page in the list of backlinks
	* @access public
	*/
	public function getBacklinksContents(String $link, String $limit = "max") {
		$pages = iterator_to_array($this->getBacklinks($link, $limit), false);
		yield from $this->getContent($pages);
	}
	
	/**
	* generator for all categories of given pages
	*
	* @param Array $pages      the pages for which the categories should be loaded
	* @param String $limit     the maximum amount of categories to query
	* @param Array $filter     an array of categories for which to filter for. Will only return these pages. Can be used to check if a page is in a specific category
	* @return Generator|Array  an array for each page containing all categories the page is in
	* @access public
	*/
	public function getCategories(Array $pages, String $limit = "max", Array $filter = array()) {
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		if(count($filter) > ($this->hasRight("apihighlimits") ? 500 : 50)) throw new Exception("Filter Array is too large.");
		
		$categoriesList = array();
		
		while(!empty($pages)) {
			$toQuery = array_slice($pages, 0, $max);
			$pages = array_splice($pages, $max);
			
			do {
				$categoriesQuery = new Categories($this->url, $toQuery, $limit, $continue, $filter);
				$categoriesQuery->setCookieFile($this->cookiefile);
				$categoriesQuery->setLogger($this->logger);
				$queryResult = $categoriesQuery->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($page->categories)) continue;
					
					$pageCategories = array();
					
					foreach($page->categories->cl as $category) {
						array_push($pageCategories, (String)$category["title"]);
					}
					if(isset($categoriesList[(String)$page["title"]])) {
						$categoriesList[(String)$page["title"]] = array_merge($categoriesList[(String)$page["title"]], $pageCategories);
					} else {
						$categoriesList[(String)$page["title"]] = $pageCategories;
					}
				}
				
				$continue = $queryResult->continue["clcontinue"];
			} while(isset($queryResult->continue["clcontinue"]));
		}
		
		foreach($categoriesList as $page => $pageCategories) {
			yield $page => $pageCategories;
		}
	}
	
	/**
	* generator for all pages in a given category
	*
	* @param String $category   the category to get pages from
	* @param String $limit      the maximum amount of pages to query
	* @param Array $types       an array containing the types to query. May contain any but at least one of "page", "subcat" or "file"
	* @param String $continue   value for additional queries
	* @return Generator|String  all page titles in a given category
	* @access public
	*/
	public function getCategoryMembers(String $category, String $limit = "max", Array $types = array("page", "subcat", "file"), String $continue = "") {
		$categorymembers = new Categorymembers($this->url, $category, $limit, $types, $continue);
		$categorymembers->setCookieFile($this->cookiefile);
		$categorymembers->setLogger($this->logger);
		$queryResult = $categorymembers->execute();
		
		foreach($queryResult->query->categorymembers->cm as $categorymember) {
			yield (String)$categorymember["title"];
		}
		
		if(isset($queryResult->continue["cmcontinue"])) {
			yield from $this->getCategoryMembers($category, $limit, $types, $queryResult->continue["cmcontinue"]);
		}
	}
	
	/**
	* generator for the content of all pages in a given category
	*
	* @param String $category   the category to get pages from
	* @param String $limit      the maximum amount of pages to query
	* @param Array $types       an array containing the types of the query. May contain any but at least one of "page", "subcat" or "file"
	* @return Generator|String  title and content of a page in the category
	* @access public
	*/
	public function getCategoryMembersContents(String $category, String $limit = "max", Array $types = array("page", "subcat", "file")) {
		$pages = iterator_to_array($this->getCategoryMembers($category, $limit, $types), false);
		yield from $this->getContent($pages);
	}
	
	/**
	* getter for the content of a page
	*
	* @param Array|String $titles  the titles of the pages for which the content should be queried
	* @return Generator|String     the title and the content of the page. NULL if page does not exist
	* @access public
	*/
	public function getContent(Array|String $articles) {
		if(gettype($articles) === "string") $articles = explode("|", $articles);
		
		$redo = array();
		
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		$requester = new APIMultiRequest();
		
		while(count($articles) > 0) {
			$requester->addRequest($this->getContentRequest(implode("|", array_slice($articles, 0, $max))));
			$articles = array_splice($articles, $max);
		}
		
		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query)) {
				foreach($queryResult->query->pages->page as $content) {
					if(isset($content["missing"])) {
						yield (String)$content["title"] => NULL;
						continue;
					}
					if(!isset($content->revisions->rev)) {
						array_push($redo, (string)($content["title"]));
					} else {
						yield (String)$content["title"] => (String)$content->revisions->rev->slots->slot;
					}
				}
			}
		}
		
		if(!empty($redo)) {
			yield from $this->getContent($redo);
		}
	}
	
	/**
	* getter for the request to the content of a page
	*
	* @param String $titles  the titles of the pages for which the content should be queried
	* @return CurlHandle     a reference to the request handle
	* @access public
	*/
	public function &getContentRequest(String $articles) {
		$content = new Content($this->url, $articles);
		$content->setCookieFile($this->cookiefile);
		return $content->getRequest();
	}
	
	/**
	* getter for the request of an edit
	*
	* @param String $page     the page that will be edited
	* @param String $content  the new content of the page
	* @param String $summary  the summary of the edit
	* @param String $isbot    if the account editing is a bot or not
	* @param String $isminor  if the edit should be marked as minor or not
	* @return CurlHandle      a reference to the request handle
	* @access public
	*/
	public function &getEditRequest(String $page, String $content, String $summary = "", String $isbot = "1", String $isminor = "1") {
		$edit = new Edit($this->url, $page, $content, $summary, $isbot, $isminor);
		$edit->setCookieFile($this->cookiefile);
		return $edit->getRequest($this->getToken("csrf"));
	}
	
	
	/**
	* generator for the url of an image from a given title
	*
	* @param String $titles     the titles of the files for which the urls should be queried
	* @return Generator|String  the title and the url of a file
	* @access public
	*/
	public function getFileurl(String $files) {
		$fileurls = new Fileurl($this->url, $files);
		$fileurls->setCookieFile($this->cookiefile);
		$fileurls->setLogger($this->logger);
		$queryResult = $fileurls->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			yield explode(":", (String)$page["title"])[1] => (String)$page->imageinfo->ii["url"];
		}
	}
	
	/**
	* generator for all pages using images
	*
	* @param Array $files       the files that will be checked
	* @param String $limit      the maximum amount of results
	* @param String $namespace  the namespace for filtering queries
	* @return Generator|Array   an array for each file, containing the list of all pages transcluding the file. May be empty
	* @access public
	*/
	public function getFileusage(Array $files, String $namespace = "", String $limit = "max") {
		$fileTransclusions = array();
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		
		do {
			$continue = "";
			$queryFiles = array_slice($files, 0, $max);
			$files = array_splice($files, $max);
			
			do {
				$fileusages = new Fileusage($this->url, $queryFiles, $limit, $namespace, $continue);
				$fileusages->setCookieFile($this->cookiefile);
				$fileusages->setLogger($this->logger);
				$queryResult = $fileusages->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($fileTransclusions[(String)$page["title"]])) $fileTransclusions[(String)$page["title"]] = array();
					if(isset($page->fileusage)) {
						foreach($page->fileusage->fu as $fileusage) {
							array_push($fileTransclusions[(String)$page["title"]], (String)$fileusage["title"]);
						}
					}
				}
				
				$continue = (String)$queryResult->continue["fucontinue"];
			} while(isset($queryResult->continue));
		} while(!empty($files));
		
		foreach($fileTransclusions as $file => $transclusions) {
			yield $file => $transclusions;
		}
	}
	
	/**
	* generator for all langlinks on pages
	*
	* @param String $titles    the titles of the pages for which the langlinks should be queried
	* @param String $lang      a filter for querying only one language
	* @param String $limit     the maximum amount of langlinks that should be queried
	* @return Generator|Array  an array containing the page title and the langlinks of this page
	* @access public
	*/
	public function getLanglinks(String $titles, String $lang = "", String $limit = "max") {
		$langlinks = new Langlinks($this->url, $titles, $lang, $limit);
		$langlinks->setCookieFile($this->cookiefile);
		$langlinks->setLogger($this->logger);
		$queryResult = $langlinks->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			if(isset($page->langlinks)) {
				foreach($page->langlinks->ll as $langlink) {
					$arr[(String)$langlink["lang"]] = (String)$langlink;
				}
				yield ["title" => (String)$page["title"], "langlinks" => $arr];
			} else {
				yield ["title" => (String)$page["title"], "langlinks" => []];
			}
		}
	}
	
	/**
	* generator for all pages in the linklist of a given page
	*
	* @param String $link       the page for which the linklist should be loaded
	* @param String $limit      the maximum amount of pages to query
	* @param String $continue   value for additional queries
	* @return Generator|String  all page titles in the linklist of the page
	* @access public
	*/
	public function getLinklist(String $link, String $limit = "max") {
		yield from $this->getBacklinks($link, $limit);
		yield from $this->getTransclusions($link, $limit);
	}
	
	/**
	* generator for the content of all pages in the linklist of a given page
	*
	* @param String $link      the page for which the linklist should be loaded
	* @param String $limit     the maximum amount of pages to query
	* @return Generator|Array  an array containing title and content of all pages in the linklist of the page
	* @access public
	*/
	public function getLinklistContents(String $link, String $limit = "max") {
		yield from $this->getBacklinksContents($link, $limit);
		yield from $this->getTransclusionsContents($link, $limit);
	}
	
	/**
	* generator for all links a page links to
	*
	* @param String $titles     the pages for which the links should be queried
	* @param String $targets    filter for which link targets the links should be queried
	* @param String $namespace  filter for which namespace the links are pointing to
	* @param String $limit      the maximum amount of links queried
	* @param String $continue   continue for additional queries
	* @return Generator|Array   an array containing the title of the page that is linking and the title of the page that is linked to
	* @access public
	*/
	public function getLinks(String $titles, String $targets = "", String $namespace = "*", String $limit = "max", String $continue = "") {
		$links = new Links($this->url, $titles, $targets, $namespace, $limit, $continue);
		$links->setCookieFile($this->cookiefile);
		$links->setLogger($this->logger);
		$queryResult = $links->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			if(isset($page->links)) {
				foreach($page->links->pl as $link) {
					yield ["from" => (String)$page["title"], "to" => (String)$link["title"]];
				}
			}
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getLinks($titles, $targets, $namespace, $limit, $queryResult->continue["plcontinue"]);
		}
	}
	
	/**
	* generator for all logevents
	*
	* @param String $action     the action that should be queried
	* @param String $user       a filter for the user responsible for the event
	* @param String $namespace  a filter for the effected namespace
	* @param String $limit      the maximum amount of logevents to be queried
	* @param String $continue   continue for additional queries
	* @return Generator|Array   an array containing the user responsible for the logevent and the target of the event
	* @access public
	*/
	public function getLogevents(String $action, String $user = "", String $namespace = "", String $limit = "max", String $continue = "") {
		$logevents = new Logevents($this->url, $action, $user, $namespace, $limit, $continue);
		$logevents->setCookieFile($this->cookiefile);
		$logevents->setLogger($this->logger);
		$queryResult = $logevents->execute();
		
		foreach($queryResult->query->logevents->item as $item) {
			yield ["user" => (String)$item["user"], "title" => (String)$item["title"]];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getLogevents($action, $user, $namespace, $limit, $queryResult->continue["lecontinue"]);
		}
	}
	
	/**
	* getter for the request of a page move
	*
	* @param String $from        the old name of the page
	* @param String $to          the new name of the page
	* @param String $reason      the reason displayed why the page was moved
	* @param String $noredirect  whether the page should be moved without redirect or not
	* @param String $movetalk    whether the talk page of the page should be move as well or not
	* @return CurlHandle         a reference to the request handle
	* @access public
	*/
	public function &getMoveRequest(String $from, String $to, String $reason = "", String $noredirect = "1", String $movetalk = "1") {
		$move = new Move($this->url, $from, $to, $reason, $noredirect, $movetalk);
		$move->setCookieFile($this->cookiefile);
		return $move->getRequest($this->getToken("csrf"));
	}
	
	/**
	* generator for information on revisions
	*
	* @param String $revids    the revids that should be queried
	* @return Generator|Array  an array containing the page title, the user and the parentid of the revision
	* @access public
	*/
	public function getRevisions(String $revids) {
		$revisions = new Revisions($this->url, $revids);
		$revisions->setCookieFile($this->cookiefile);
		$revisions->setLogger($this->logger);
		$queryResult = $revisions->execute();
		
		if(isset($queryResult->query->pages)) {
			foreach($queryResult->query->pages->page as $page) {
				foreach($page->revisions->rev as $revision) {
					yield ["page" => (String)$page["title"], "user" => (String)$revision["user"], "parentid" => (String)$revision["parentid"]];
				}
			}
		}
	}
	
	/**
	* generator for users of revisions of a page
	*
	* @param String $page       the page for which the revisions should be queried
	* @param String $limit      the maximum amount of users to query
	* @param String $continue   value for additional queries
	* @return Generator|String  the name of the user who edited a revision
	* @access public
	*/
	public function getRevisionUsers(String $page, String $limit = "max", String $continue = "") {
		$revisionUsers = new RevisionUsers($this->url, $page, $limit, $continue);
		$revisionUsers->setCookieFile($this->cookiefile);
		$revisionUsers->setLogger($this->logger);
		$queryResult = $revisionUsers->execute();
		
		foreach($queryResult->query->pages->page->revisions->rev as $revision) {
			yield ["user" => (String)$revision["user"], "parentid" => (String)$revision["parentid"], "revid" => (String)$revision["revid"]];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getRevisionUsers($page, $limit, $queryResult->continue["rvcontinue"]);
		}
	}
	
	/**
	* generator for the editcount calculated by MediaWiki
	*
	* @param String $users      the users for which the editcount should be queried
	* @return Generator|String  the editcount for each user
	* @access public
	*/
	public function getSystemEditCount(String $users) {
		$editcount = new Systemeditcount($this->url, $users);
		$editcount->setCookieFile($this->cookiefile);
		$editcount->setLogger($this->logger);
		$queryResult = $editcount->execute();
		
		foreach($queryResult->query->users->user as $user) {
			yield (String)$user["editcount"];
		}
	}
	
	/**
	* getter for all the parameters of a template
	*
	* @param String $template  the name of the template for which the parameters should be queried
	* @return Array            all the parameters of a template
	* @access public
	*/
	public function getTemplateParameters(String $content) {
		preg_match_all("/\{\{\{([^\|\}\{]+)/", preg_replace("/<noinclude>(?<=\<noinclude\>)(.*?)(?=\<\/noinclude\>)<\/noinclude>/", "", $content), $matches);
		
		$parameters = array_unique($matches[1]);
		asort($parameters);
		return $parameters;
	}
	
	/**
	* getter for a token of a given type
	*
	* @param String $type  the type of token that should be queried
	* @access public
	*/
	public function getToken(String $type) {
		if(!isset($this->tokens[$type])) {
			$token = new Token($this->url, $type);
			$token->setCookieFile($this->cookiefile);
			$token->setLogger($this->logger);
			$this->tokens[$type] = $token->execute();
		}
		return $this->tokens[$type];
	}
	
	/**
	* generator for all pages transcluding a given page
	*
	* @param String $link       the transcluded page
	* @param String $limit      the maximum amount of pages to query
	* @param String $continue   value for additional queries
	* @return Generator|String  all page titles transcluding a given page
	* @access public
	*/
	public function getTransclusions(String $link, String $limit = "max", String $continue = "") {
		$transclusions = new Transclusions($this->url, $link, $limit, $continue);
		$transclusions->setCookieFile($this->cookiefile);
		$transclusions->setLogger($this->logger);
		$queryResult = $transclusions->execute();
		
		foreach($queryResult->query->embeddedin->ei as $transclusion) {
			yield (String)$transclusion["title"];
		}
		
		if(isset($queryResult->continue["eicontinue"])) {
			yield from $this->getTransclusions($link, $limit, $queryResult->continue["eicontinue"]);
		}
	}
	
	/**
	* generator for the content of all pages transcluding a given page
	*
	* @param String $link       the transcluded page
	* @param String $limit      the maximum amount of pages to query
	* @return Generator|String  title and content of a page in the list of transclusions
	* @access public
	*/
	public function getTransclusionsContents(String $link, String $limit = "max") {
		$pages = iterator_to_array($this->getTransclusions($link, $limit), false);
		yield from $this->getContent($pages);
	}
	
	/**
	* generator for all contributions of a user
	*
	* @param String $user      the user for which to query the contributions for
	* @param String $limit     limit for the maximum amount of contributions requested
	* @param String $continue  continue for additional queries
	* @return Generator|Array  an array containing the user, the title, the timestamp, the namespace and the sizediff of the contribution
	* @access public
	*/
	public function getUsercontribs(String $user, String $limit = "max", String $continue = "") {
		$usercontribs = new Usercontribs($this->url, $user, $limit, $continue);
		$usercontribs->setCookieFile($this->cookiefile);
		$usercontribs->setLogger($this->logger);
		$queryResult = $usercontribs->execute();
		
		foreach($queryResult->query->usercontribs->item as $usercontrib) {
			yield [
				"user"  => (String)$usercontrib["user"],
				"title"     => (String)$usercontrib["title"],
				"timestamp" => (String)$usercontrib["timestamp"],
				"namespace" => (String)$usercontrib["ns"],
				"sizediff"  => (String)$usercontrib["sizediff"]
			];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getUsercontribs($user, $limit, $queryResult->continue["uccontinue"]);
		}
	}
	
	/**
	* check whether the current user has a given right
	*
	* @param String $right  the right to check
	* @return bool          true if the user has the right, false if not
	* @access public
	*/
	public function hasRight(String $right) {
		if(empty($this->userrights)) {
			$userrights = new Userrights($this->url);
			$userrights->setCookieFile($this->cookiefile);
			$userrights->setLogger($this->logger);
			$queryResult = $userrights->execute();
			
			foreach($queryResult->query->userinfo->rights->r as $userright) {
				$this->userrights[(String)$userright] = 1;
			}
		}
		return isset($this->userrights[trim($right)]);
	}
	
	/**
	* generator for checking if a page is a redirect
	*
	* @param String $titles    the titles that should be checked
	* @return Generator|Array  an array containing the title and a boolean if the page is a redirect
	* @access public
	*/
	public function isRedirect(String $titles) {
		$redirects = new Redirect($this->url, $titles);
		$redirects->setCookieFile($this->cookiefile);
		$redirects->setLogger($this->logger);
		$queryResult = $redirects->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			if(isset($queryResult->query->redirects)) {
				yield (String)$queryResult->query->redirects->r["from"] => true;
			} else {
				yield (String)$page["title"] => false;
			}
		}
	}
	
	/**
	* loggin in to a wiki with an account
	*
	* @param String $username  the username of the account that tries to log in
	* @param String $password  the password of the account that tries to log in
	* @return mixed            true on success, an error message on failure
	* @access public
	*/
	public function login(String $username, String $password) {
		$login = new Login($this->url, $username, $password);
		$login->setCookieFile($this->cookiefile);
		$login->setLogger($this->logger);
		$queryResult = $login->execute($this->getToken("login"));
		if($queryResult === true) {
			$this->loggedIn = true;
			return true;
		} else {
			return $queryResult;
		}
	}
	
	/**
	* logging out of a wiki
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function logout() {
		$logout = new Logout($this->url);
		$logout->setCookieFile($this->cookiefile);
		$logout->setLogger($this->logger);
		$this->loggedIn = false;
		return $logout->execute($this->getToken("csrf"));
	}
	
	/**
	* moving a page on a wiki
	*
	* @param String $from        the old name of the page
	* @param String $to          the new name of the page
	* @param String $reason      the reason displayed why the page was moved
	* @param String $noredirect  whether the page should be moved without redirect or not
	* @param String $movetalk    whether the talk page of the page should be move as well or not
	* @return SimpleXMLElement   the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function move(String $from, String $to, String $reason = "", String $noredirect = "1", String $movetalk = "1") {
		$move = new Move($this->url, $from, $to, $reason, $noredirect, $movetalk);
		$move->setCookieFile($this->cookiefile);
		$move->setLogger($this->logger);
		return $move->execute($this->getToken("csrf"));
	}
	
	/**
	* parsing the text of an article or free text
	* can be used to get the parsed HTML, get the sections of an article or get a section from a name
	*
	* @param String $title    a title of a page that should be parsed
	* @param String $text     free text using wiki markup that can be parsed
	* @return Wikitextparser  a Wikitextparser object that can be used for further evaluation
	* @access public
	*/
	public function parse(String $title = "", String $text = "") {
		$wikitextparser = new Wikitextparser($this->url, $title, $text);
		$wikitextparser->setCookieFile($this->cookiefile);
		$wikitextparser->setLogger($this->logger);
		return $wikitextparser;
	}
	
	/**
	* revert an edit
	*
	* @param String $page       the page on which the revision will be reverted
	* @param String $revision   the revision id for the edit that should be reverted
	* @param String $summary    the summary that will be added when reverting
	* @param String $isbot      whether the edit should be marked as done by a bot or not
	* @param String $isminor    whether the edit should be marked as minor or not
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function undo(String $page, String $revision, String $summary = "", String $isbot = "1", String $isminor = "1") {
		$undo = new Undo($this->url, $page, $revision, $summary, $isbot, $isminor);
		$undo->setCookieFile($this->cookiefile);
		$undo->setLogger($this->logger);
		return $undo->execute($this->getToken("csrf"));
	}
	
	/**
	* uploading a file from a local system
	*
	* @param String $filepath        path to the file on the local system
	* @param String $filename        name of the file on the wiki
	* @param String $text            initial text of the file page
	* @param String $comment         comment displayed when uploading the file
	* @param String $ignorewarnings  whether warnings should be ignored when uploading a file or not
	* @return SimpleXMLElement       the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function upload(String $filepath, String $filename, String $text, String $comment = "", String $ignorewarnings = "1") {
		$upload = new Upload($this->url, $filepath, $filename, $text, $comment, $ignorewarnings);
		$upload->setCookieFile($this->cookiefile);
		$upload->setLogger($this->logger);
		return $upload->execute($this->getToken("csrf"));
	}
	
	/**
	* uploading a file from a url
	*
	* @param String $url             url to the wiki
	* @param String $fileurl         url to the file that should be uploaded
	* @param String $filename        name of the file on the wiki
	* @param String $text            initial text of the file page
	* @param String $comment         comment displayed when uploading the file
	* @param String $ignorewarnings  whether warnings should be ignored when uploading a file or not
	* @return SimpleXMLElement       the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function uploadbyurl(String $fileurl, String $filename, String $text, String $comment = "", String $ignorewarnings = "1") {
		$uploadbyurl = new Uploadbyurl($this->url, $fileurl, $filename, $text, $comment, $ignorewarnings);
		$uploadbyurl->setCookieFile($this->cookiefile);
		$uploadbyurl->setLogger($this->logger);
		return $uploadbyurl->execute($this->getToken("csrf"));
	}
}
?>
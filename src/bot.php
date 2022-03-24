<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

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
* @method SimpleXMLElement delete(String $title, String $reason)
* @method SimpleXMLElement edit(String $page, String $content, String $summary, String $isbot, String $isminor)
* @method Parsetree expandTemplates(String $content)
* @method String expandWikitext(String $text, String $title)
* @method Generator|String getAllpages(String $namespace, String $filter, String $limit, String $continue)
* @method Generator|Array getAllpagesContents(String $namespace, String $filter, String $limit)
* @method Generator|Array getAllusers(String $limit, String $continue)
* @method Generator|String getBacklinks(String $link, String $limit, String $continue)
* @method Generator|Array getBacklinksContents(String $link, String $limit)
* @method Generator|String getCategoryMembers(String $category, String $limit, Array $types, String $continue)
* @method Generator|Array getCategoryMembersContents(String $category, String $limit, Array $types)
* @method Generator|Array getContent(String $articles)
* @method SimpleXMLElement getContentRequest(String $articles)
* @method Generator|Array getFileusage(String $files, String $limit, String $namespace, String $continue)
* @method Generator|Array getLanglinks(String $titles, String $lang, String $limit)
* @method Generator|String getLinklist(String $link, String $limit)
* @method Generator|Array getLinklistContents(String $link, String $limit)
* @method Generator|Array getLinks(String $titles, String $targets, String $namespace, String $limit, String $continue)
* @method Generator|Array getLogevents(String $action, String $user, String $namespace, String $limit, String $continue)
* @method Generator|Array getRevisions(String $revids)
* @method Generator|String getRevisionUsers(String $page, String $limit, String $continue)
* @method Generator|String getSystemEditCount(String $users)
* @method Array getTemplateParameters(String $content)
* @method String getToken(String $token)
* @method Generator|String getTransclusions(String $link, String $limit, String $continue)
* @method Generator|Array getTransclusionsContents(String $link, String $limit)
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
	
	/**
	* constructor for class Bot
	*
	* @param String $url  the url to the wiki
	* @access public
	*/
	public function __construct(String $url) {
		$this->url = $url;
		$this->cookiefile = "cookies.txt";
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
		return $edit->execute($this->getToken("csrf"));
	}
	
	/**
	* handler for expanding templates
	*
	* @param String $content  the content that should be expanded
	* @return Parsetree       a Parsetree-object allowing the expansion of the template
	* @access public
	*/
	public function expandTemplates(String $content) {
		$parsetree = new Parsetree($this->url, $content);
		$parsetree->setCookieFile($this->cookiefile);
		return $parsetree;
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
		return $wikitext->execute();
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
	* @return Generator|Array   an array containing title and content of all pages
	* @access public
	*/
	public function getAllpagesContents(String $namespace, String $filter = "all", String $limit = "max") {
		$pages = "";
		$counter = 0;
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		
		$requester = new APIMultiRequest();
		
		foreach($this->getAllpages($namespace, $filter, $limit) as $page) {
			$pages .= "|".$page;
			$counter++;
			
			if($counter === $max) {
				$requester->addRequest($this->getContentRequest(trim($pages, "|")));
				$pages = "";
				$counter = 0;
			}
		}
		
		$requester->addRequest($this->getContentRequest(trim($pages, "|")));
		
		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query)) {
				foreach($queryResult->query->pages->page as $content) {
					if(!isset($content->revisions->rev)) {
						yield from $this->getContent($content["title"]);
					} else {
						yield ["title" => (String)$content["title"], "content" => (String)$content->revisions->rev->slots->slot];
					}
				}
			}
		}
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
	* @param String $link      the page linking to
	* @param String $limit     the maximum amount of pages to query
	* @return Generator|Array  an array containing title and content of all pages
	* @access public
	*/
	public function getBacklinksContents(String $link, String $limit = "max") {
		$pages = "";
		$counter = 0;
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
	
		$requester = new APIMultiRequest();
		
		foreach($this->getBacklinks($link, $limit) as $backlink) {
			$pages .= "|".$backlink;
			$counter++;
			
			if($counter === $max) {
				$requester->addRequest($this->getContentRequest(trim($pages, "|")));
				$pages = "";
				$counter = 0;
			}
		}
		
		$requester->addRequest($this->getContentRequest(trim($pages, "|")));
		
		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query)) {
				foreach($queryResult->query->pages->page as $content) {
					if(!isset($content->revisions->rev)) {
						yield from $this->getContent($content["title"]);
					} else {
						yield ["title" => (String)$content["title"], "content" => (String)$content->revisions->rev->slots->slot];
					}
				}
			}
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
	* @param String $category  the category to get pages from
	* @param String $limit     the maximum amount of pages to query
	* @param Array $types      an array containing the types of the query. May contain any but at least one of "page", "subcat" or "file"
	* @return Generator|Array  an array containing page title and content of all pages
	* @access public
	*/
	public function getCategoryMembersContents(String $category, String $limit = "max", Array $types = array("page", "subcat", "file")) {
		$pages = "";
		$counter = 0;
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		
		$requester = new APIMultiRequest();
		
		foreach($this->getCategoryMembers($category, $limit, $types) as $categorymember) {
			$pages .= "|".$categorymember;
			$counter++;
			
			if($counter === $max) {
				$requester->addRequest($this->getContentRequest(trim($pages, "|")));
				$pages = "";
				$counter = 0;
			}
		}
		
		$requester->addRequest($this->getContentRequest(trim($pages, "|")));
		
		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query)) {
				foreach($queryResult->query->pages->page as $content) {
					if(!isset($content->revisions->rev)) {
						yield from $this->getContent($content["title"]);
					} else {
						yield ["title" => (String)$content["title"], "content" => (String)$content->revisions->rev->slots->slot];
					}
				}
			}
		}
	}
	
	/**
	* getter for the content of a page
	*
	* @param String $titles    the titles of the pages for which the content should be queried
	* @return Generator|Array  an array containing page title and content of this page
	* @access public
	*/
	public function getContent(String $articles) {
		$content = new Content($this->url, $articles);
		$content->setCookieFile($this->cookiefile);
		$queryResult = $content->execute();
		
		if(isset($queryResult->query)) {
			foreach($queryResult->query->pages->page as $content) {
				if(!isset($content->revisions->rev)) {
					yield from $this->getContent($content["title"]);
				} else {
					yield ["title" => (String)$content["title"], "content" => (String)$content->revisions->rev->slots->slot];
				}
			}
		}
	}
	
	/**
	* getter for the request to the content of a page
	*
	* @param String $titles    the titles of the pages for which the content should be queried
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getContentRequest(String $articles) {
		$content = new Content($this->url, $articles);
		$content->setCookieFile($this->cookiefile);
		return $content->getRequest();
	}
	
	/**
	* generator for all pages using images
	*
	* @param String $files      the files that will be checked
	* @param String $limit      the maximum amount of results
	* @param String $namespace  the namespace for filtering queries
	* @param String $continue   continue for additional queries
	* @return Generator|Array   an array containing the filename and the page it is used on
	* @access public
	*/
	public function getFileusage(String $files, String $limit = "max", String $namespace = "", String $continue = "") {
		$fileusages = new Fileusage($this->url, $files, $limit, $namespace, $continue);
		$fileusages->setCookieFile($this->cookiefile);
		$queryResult = $fileusages->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			if(isset($page->fileusage)) {
				foreach($page->fileusage->fu as $fileusage) {
					yield ["title" => (String)$fileusage["title"], "file" => (String)$page["title"]];
				}
			}
		}
		
		if(isset($queryResult->continue["fucontinue"])) {
			yield from $this->getFileusage($files, $limit, $namespace, $queryResult->continue["fucontinue"]);
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
		$queryResult = $logevents->execute();
		
		foreach($queryResult->query->logevents->item as $item) {
			yield ["user" => (String)$item["user"], "title" => (String)$item["title"]];
		}
		
		if(isset($queryResult->continue)) {
			yield from $this->getLogevents($action, $user, $namespace, $limit, $queryResult->continue["lecontinue"]);
		}
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
		$token = new Token($this->url, $type);
		$token->setCookieFile($this->cookiefile);
		return $token->execute();
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
	* @param String $link      the transcluded page
	* @param String $limit     the maximum amount of pages to query
	* @return Generator|Array  an array containing title and content of all pages
	* @access public
	*/
	public function getTransclusionsContents(String $link, String $limit = "max") {
		$pages = "";
		$counter = 0;
		$max = $this->hasRight("apihighlimits") ? 500 : 50;
		
		$requester = new APIMultiRequest();
		
		foreach($this->getTransclusions($link, $limit) as $transclusion) {
			$pages .= "|".$transclusion;
			$counter++;
			
			if($counter === $max) {
				$requester->addRequest($this->getContentRequest(trim($pages, "|")));
				$pages = "";
				$counter = 0;
			}
		}
		
		$requester->addRequest($this->getContentRequest(trim($pages, "|")));
		
		foreach($requester->execute() as $queryResult) {
			if(isset($queryResult->query)) {
				foreach($queryResult->query->pages->page as $content) {
					if(!isset($content->revisions->rev)) {
						yield from $this->getContent($content["title"]);
					} else {
						yield ["title" => (String)$content["title"], "content" => (String)$content->revisions->rev->slots->slot];
					}
				}
			}
		}
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
		$userrights = new Userrights($this->url);
		$userrights->setCookieFile($this->cookiefile);
		$queryResult = $userrights->execute();
		
		foreach($queryResult->query->userinfo->rights->r as $userright) {
			if((String)$userright === $right) {
				return true;
			}
		}
		return false;
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
		$queryResult = $redirects->execute();
		
		foreach($queryResult->query->pages->page as $page) {
			if(isset($queryResult->query->redirects)) {
				yield ["title" => (String)$queryResult->query->redirects->r["from"], "redirect" => true];
			} else {
				yield ["title" => (String)$page["title"], "redirect" => false];
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
		return $uploadbyurl->execute($this->getToken("csrf"));
	}
}
?>
<?php
/**
* A class for representing Categories a page is in
*
* The class CategoryList represents a list of all categories a page is in
*
* @method Generator|Page getCategories()
*/
class CategoryList {
	private Bot $bot;
	private array $pages;
	private string $limit;
	private array $filter;
	
	/**
	* constructor for class CategoryList
	*
	* @param Bot $bot             a reference to the bot object
	* @param array|string $pages  a string or array of pages. Multiple pages as a string must be divided by "|"
	* @param string $limit        the maximum amount of pages to query
	* @param string $filter       an array of categories for which to filter for. Will only return these pages if applies
	* @access public
	*/
	public function __construct(Bot &$bot, array|string $pages, string $limit = "max", array $filter = array()) {
		if(gettype($pages) === "string") { $pages = explode("|", $pages); }
		$this->bot = $bot;
		$this->pages = $pages;
		$this->limit = $limit;
		$this->filter = $filter;
	}
	
	/**
	* executor for category list generation
	* will evaluate all requests before yielding categories to make sure all lists are complete
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  list of pages or a page if only categories for one page were requested
	* @access public
	*/
	public function getCategories($generator = false) : Generator|Page {
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		if(count($this->filter) > ($this->bot->hasRight("apihighlimits") ? 500 : 50)) {
			throw new Error("Filter array is too large.");
		}
		
		$categoriesList = array();
		
		while(!empty($this->pages)) {
			$toQuery = array_slice($this->pages, 0, $max);
			$this->pages = array_splice($this->pages, $max);
			$this->continue = "";
			
			do {
				$categoriesQuery = new CategoriesRequest(
					$this->bot->getUrl(),
					$toQuery,
					$this->limit,
					$this->filter,
					$this->continue
				);
				$categoriesQuery->setCookieFile($this->bot->getCookieFile());
				$categoriesQuery->setLogger($this->bot->getLogger());
				$queryResult = $categoriesQuery->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($page->categories)) { continue; }
					
					if(!isset($categoriesList[(string)$page["title"]])) {
						$res = new Page((string)$page["title"]);
						$res->setId((int)$page["pageid"]);
						$res->setNamespace((int)$page["ns"]);
						$categoriesList[(string)$page["title"]] = $res;
					}
					
					foreach($page->categories->cl as $category) {
						$categoriesList[(string)$page["title"]]->addCategory((string)$category["title"]);
					}
				}
				
				if(isset($queryResult->continue["clcontinue"])) {
					$this->continue = $queryResult->continue["clcontinue"];
				}
			} while(isset($queryResult->continue["clcontinue"]));
		}
		
		if(count($categoriesList) === 1 && $generator === false) {
			foreach($categoriesList as $category) {
				return $category;
			}
		} else {
			return (function() use ($categoriesList) {
				foreach($categoriesList as $page) {
					yield $page;
				}
			})();
		}
	}
}
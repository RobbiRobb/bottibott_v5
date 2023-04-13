<?php
/**
* A class for representing lists of pages in a category
*
* The class Category represents lists of pages in a given category
* Subcategories are included as pages and are not automatically queried
*
* @method Generator|Page getMembers()
*/
class Category {
	private Bot $bot;
	private string $category;
	private string $limit;
	private array $types;
	
	/**
	* constructor for class Category
	*
	* @param Bot $bot          a reference to the bot object
	* @param string $category  the name of the category
	* @param string $limit     the maximum amount of pages to query
	* @param array $types      types that should be queried. May contain any but at least one of "page", "subcat" or "file"
	* @access public
	*/
	public function __construct(
		Bot &$bot,
		string $category,
		string $limit = "max",
		array $types = array("page", "subcat", "file")
	) {
		$this->bot = $bot;
		$this->category = $category;
		$this->limit = $limit;
		$this->types = $types;
	}
	
	/**
	* executor for list generation
	*
	* @return Generator|Page  list of pages
	* @access public
	*/
	public function getMembers() : Generator|Page {
		$continue = "";
		
		do {
			$categorymembers = new CategorymembersRequest(
				$this->bot->getUrl(),
				$this->category,
				$this->limit,
				$this->types,
				$continue
			);
			$categorymembers->setCookieFile($this->bot->getCookieFile());
			$categorymembers->setLogger($this->bot->getLogger());
			$queryResult = $categorymembers->execute();
			
			foreach($queryResult->query->categorymembers->cm as $categorymember) {
				$res = new Page((string)$categorymember["title"]);
				$res->setId((int)$categorymember["pageid"]);
				$res->setNamespace((int)$categorymember["ns"]);
				yield $res;
			}
			
			if(isset($queryResult->continue["cmcontinue"])) {
				$continue = $queryResult->continue["cmcontinue"];
			}
		} while(isset($queryResult->continue["cmcontinue"]));
	}
}
<?php
/**
* A class for representing usages of a file
*
* The class Fileusage represents a list of all pages a file is used on
*
* @method Generator|File getUsage()
*/
class Fileusage {
	private Bot $bot;
	private array $files;
	private string $namespace;
	private string $limit;
	private string $continue;
	
	/**
	* constructor for class Fileusage
	*
	* @param Bot $bot             a reference to the bot object
	* @param string|array $files  a string or array of files. Multiple files as a string must be divided by "|"
	* @param string $namespace    a namespace filter to only list fileusages in this namespace
	* @param string $limit        the maximum amount of fileusages to query
	* @access public
	*/
	public function __construct(Bot &$bot, string|array $files, string $namespace = "", string $limit = "max") {
		if(gettype($files) === "string") { $files = explode("|", $files); }
		$this->bot = $bot;
		$this->files = $files;
		$this->namespace = $namespace;
		$this->limit = $limit;
	}
	
	/**
	* executor for fileusage list generation
	* will evaluate all requests before yielding files to make sure all lists are complete
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|Page  list of files or a file if only usages for one file were requested
	* @access public
	*/
	public function getUsage($generator = false) : Generator|File {
		$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
		
		$fileTransclusions = array();
		
		do {
			$queryFiles = array_slice($this->files, 0, $max);
			$this->files = array_splice($this->files, $max);
			$this->continue = "";
			
			do {
				$fileusages = new FileusageRequest(
					$this->bot->getUrl(),
					$queryFiles,
					$this->limit,
					$this->namespace,
					$this->continue
				);
				$fileusages->setCookieFile($this->bot->getCookieFile());
				$fileusages->setLogger($this->bot->getLogger());
				$queryResult = $fileusages->execute();
				
				foreach($queryResult->query->pages->page as $page) {
					if(!isset($fileTransclusions[(string)$page["title"]])) {
						$file = new File((string)$page["title"]);
						if(isset($page["missing"])) {
							$file->setExists(false);
							$fileTransclusions[(string)$page["title"]] = $file;
							continue;
						}
						$file->setId((int)$page["pageid"]);
						$file->setNamespace((int)$page["ns"]);
						$fileTransclusions[(string)$page["title"]] = $file;
					}
					if(isset($page->fileusage)) {
						foreach($page->fileusage->fu as $fileusage) {
							$res = new Page((string)$fileusage["title"]);
							$res->setId((int)$fileusage["pageid"]);
							$res->setNamespace((int)$fileusage["ns"]);
							$fileTransclusions[(string)$page["title"]]->addUsage($res);
						}
					}
				}
				
				if(isset($queryResult->continue["fucontinue"])) {
					$this->continue = (string)$queryResult->continue["fucontinue"];
				}
			} while(isset($queryResult->continue["fucontinue"]));
		} while(!empty($this->files));
		
		if(count($fileTransclusions) === 1 && $generator === false) {
			foreach($fileTransclusions as $file) {
				return $file;
			}
		} else {
			return (function() use ($fileTransclusions) {
				foreach($fileTransclusions as $file) {
					yield $file;
				}
			})();
		}
	}
}
<?php
/**
* A class for representing the content of pages
*
* The class Content represents the content of one or multiple pages
*
* @method Generator|File getUrl(bool $generator)
* @method CurlHandle getRequest(string $files)
*/
class Fileurl {
	private Bot $bot;
	private array $files;
	
	/**
	* constructor for class Fileurl
	*
	* @param Bot $bot      a reference to the bot object
	* @param string|array  a string or array of files. Multiple files as a string must be divided by "|"
	* @access public
	*/
	public function __construct(Bot &$bot, String|array $files) {
		if(gettype($files) === "string") { $files = explode("|", $files); }
		$this->bot = $bot;
		$this->files = $files;
	}
	
	/**
	* executor for fileurl
	* returns a file object if only a single file is requested
	* or a generator if multiple files are requested
	*
	* @param bool $generator  will always return a generator if set to true
	* @return Generator|File  a generator for all files for which the url was requested
	* @access public
	*/
	public function getUrl(bool $generator = false) : Generator|File {
		if(count($this->files) === 1 && !$generator) {
			$fileurls = new FileurlRequest($this->bot->getUrl(), implode($this->files));
			$fileurls->setCookieFile($this->bot->getCookieFile());
			$fileurls->setLogger($this->bot->getLogger());
			$queryResult = $fileurls->execute();
			
			foreach($queryResult->query->pages->page as $pageContent) {
				$file = new File(implode($this->files));
				if(isset($pageContent["missing"])) {
					$file->setExists(false);
					return $file;
				}
				$file->setUrl((string)$pageContent->imageinfo->ii["url"]);
				return $file;
			}
		} else {
			$redo = array();
			
			$max = $this->bot->hasRight("apihighlimits") ? 500 : 50;
			$requester = new APIMultiRequest();
			$context = &$this;
			
			return (function() use (&$redo, &$max, &$requester, &$context) {
				while(count($context->files) > 0) {
					$requester->addRequest($context->getRequest(implode("|", array_slice($context->files, 0, $max))));
					$context->files = array_splice($context->files, $max);
				}
				
				foreach($requester->execute() as $queryResult) {
					if(isset($queryResult->query)) {
						foreach($queryResult->query->pages->page as $pageContent) {
							if(isset($pageContent["missing"])) {
								$file = new File((string)$pageContent["title"]);
								$file->setExists(false);
								yield $file;
								continue;
							}
							if(!isset($pageContent->imageinfo->ii)) {
								array_push($redo, (string)($pageContent["title"]));
							} else {
								$file = new File((string)$pageContent["title"]);
								$file->setUrl((string)$pageContent->imageinfo->ii["url"]);
								yield $file;
							}
						}
					}
				}
				
				if(!empty($redo)) {
					$context->pages = $redo;
					yield from $context->getUrl(true);
				}
			})();
		}
	}
	
	/**
	* getter for the request to the url of a file
	*
	* @param string $files  the files of the files for which the urls should be queried
	* @return CurlHandle     a reference to the request handle
	* @access public
	*/
	public function &getRequest(string $files) : CurlHandle {
		$fileurl = new FileurlRequest($this->bot->getUrl(), $files);
		$fileurl->setCookieFile($this->bot->getCookieFile());
		$fileurl->setLogger($this->bot->getLogger());
		return $fileurl->getRequest();
	}
}
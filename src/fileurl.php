<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class for representing API-requests of getting the url of files
*
* The class Fileurl represents API-requests for getting the url of one or multiple files
*
*/
class Fileurl extends Request {
	private String $files;
	
	/**
	* constructor for class Fileurl
	*
	* @param String $url    the url to the wiki
	* @param String $files  files for which the urls should be queried
	* @access public
	*/
	public function __construct(String $url, String $files) {
		$this->url = $url;
		$this->files = $files;
		$this->cookiefile = "cookies.txt";
	}
	
	/**
	* setter for the files
	*
	* @param String $files  the files that should be set
	* @access public
	*/
	public function setFiles(String $files) {
		$this->files = $files;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$fileurl = new APIRequest($this->url);
		$fileurl->setCookieFile($this->cookiefile);
		$fileurl->addToGetFields("action", "query");
		$fileurl->addToGetFields("format", "xml");
		$fileurl->addToGetFields("prop", "imageinfo");
		$fileurl->addToGetFields("iiprop", "url");
		$fileurl->addToPostFields("titles", $this->files);
		return $fileurl->execute();
	}
}
?>
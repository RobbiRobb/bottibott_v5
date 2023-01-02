<?php
spl_autoload_register(function($class) {require(strtolower($class).".php");});

/**
* A class representing API-requests for fileusage
*
* The class Fileusage allows the creation of API-requests for fileusages
*
* @method void setFiles(Array $files)
* @method void setLimit(String $limit)
* @method void setNamespace(String $namespace)
* @method void setContinue(String $continue)
* @method SimpleXMLElement execute()
*/
class Fileusage extends Request {
	private Array $files;
	private String $limit;
	private String $namespace;
	private String $continue;
	
	/**
	* constructor for class Fileusage
	*
	* @param String $url        the url to the wiki
	* @param Array $files       the files that will be checked
	* @param String $limit      the maximum amount of results
	* @param String $namespace  namespace for filtering queries
	* @param String $continue   continue for additional queries
	* @access public
	*/
	public function __construct(String $url, Array $files, String $limit = "max", String $namespace = "", String $continue = "") {
		$this->url = $url;
		$this->files = $files;
		$this->limit = $limit;
		$this->namespace = $namespace;
		$this->continue = $continue;
		$this->cookiefile = "cookiefile.txt";
	}
	
	/**
	* setter for the file names
	*
	* @param Array $files  the files that should be set
	* @access public
	*/
	public function setFiles(Array $files) {
		$this->files = $files;
	}
	
	/**
	* setter for the limit
	*
	* @param String limit  the limit that should be set
	* @access public
	*/
	public function setLimit(String $limit) {
		$this->limit = $limit;
	}
	
	/**
	* setter for the namespace
	*
	* @param Strong $namespace  the namespace that should be set
	* @access public
	*/
	public function setNamespace(String $namespace) {
		$this->namespace = $namespace;
	}
	
	/**
	* setter for the continue value
	*
	* @param Strong $continue  the continue value that should be set
	* @access public
	*/
	public function setContinue(String $continue) {
		$this->continue = $continue;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() {
		$fileusage = new APIRequest($this->url);
		$fileusage->setCookieFile($this->cookiefile);
		$fileusage->setLogger($this->logger);
		$fileusage->addToGetFields("action", "query");
		$fileusage->addToGetFields("prop", "fileusage");
		$fileusage->addToGetFields("format", "xml");
		$fileusage->addToGetFields("fulimit", $this->limit);
		if(!empty($this->namespace) || $this->namespace === "0") { $fileusage->addToGetFields("funamespace", $this->namespace); } // MediaWiki doesn't like an empty namespace
		if(!empty($this->continue)) { $fileusage->addToGetFields("fucontinue", $this->continue); } // MediaWiki doesn't like an empty continue
		$fileusage->addToPostFields("titles", implode("|", $this->files));
		return $fileusage->execute();
	}
}
?>
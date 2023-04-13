<?php
/**
* A class representing API-requests for fileusage
*
* The class FileusageRequest allows the creation of API-requests for fileusages
*
* @method void setFiles(array $files)
* @method void setLimit(string $limit)
* @method void setNamespace(string $namespace)
* @method void setContinue(string $continue)
* @method SimpleXMLElement execute()
*/
class FileusageRequest extends Request {
	private array $files;
	private string $limit;
	private string $namespace;
	private string $continue;
	
	/**
	* constructor for class FileusageRequest
	*
	* @param string $url        the url to the wiki
	* @param array $files       the files that will be checked
	* @param string $limit      the maximum amount of results
	* @param string $namespace  namespace for filtering queries
	* @param string $continue   continue for additional queries
	* @access public
	*/
	public function __construct(
		string $url,
		array $files,
		string $limit = "max",
		string $namespace = "",
		string $continue = ""
	) {
		$this->url = $url;
		$this->files = $files;
		$this->limit = $limit;
		$this->namespace = $namespace;
		$this->continue = $continue;
	}
	
	/**
	* setter for the file names
	*
	* @param array $files  the files that should be set
	* @access public
	*/
	public function setFiles(array $files) : void {
		$this->files = $files;
	}
	
	/**
	* setter for the limit
	*
	* @param string limit  the limit that should be set
	* @access public
	*/
	public function setLimit(string $limit) : void {
		$this->limit = $limit;
	}
	
	/**
	* setter for the namespace
	*
	* @param Strong $namespace  the namespace that should be set
	* @access public
	*/
	public function setNamespace(string $namespace) : void {
		$this->namespace = $namespace;
	}
	
	/**
	* setter for the continue value
	*
	* @param Strong $continue  the continue value that should be set
	* @access public
	*/
	public function setContinue(string $continue) : void {
		$this->continue = $continue;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "query");
		$request->addToGetFields("prop", "fileusage");
		$request->addToGetFields("format", "xml");
		$request->addToGetFields("fulimit", $this->limit);
		// MediaWiki doesn't like set but empty namespace
		if(!empty($this->namespace) || $this->namespace === "0") { $request->addToGetFields("funamespace", $this->namespace); }
		// MediaWiki doesn't like set but empty continue
		if(!empty($this->continue)) { $request->addToGetFields("fucontinue", $this->continue); }
		$request->addToPostFields("titles", implode("|", $this->files));
		return $request->execute();
	}
}
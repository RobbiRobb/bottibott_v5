<?php
/**
* A class for representing files
*
* The class File represents files on a wiki, adding additional properties to pages
*
* @method void setUrl(string $url)
* @method string getUrl()
* @method void addUsage(Page $page)
* @method Generator|Page getUsage()
*/
class File extends Page {
	private readonly string $url;
	private array $usage;
	
	/**
	* setter for the url
	*
	* @param string $url  the url to be set
	* @access public
	*/
	public function setUrl(string $url) : void {
		$this->url = $url;
	}
	
	/**
	* getter for the url
	*
	* @return string  the url
	*/
	public function getUrl() : string {
		if(!isset($this->url)) { throw new Exception("Url is not set"); }
		return $this->url;
	}
	
	/**
	* adder for the usage
	*
	* @param Page $usage  the page the file is used on
	* @access public
	*/
	public function addUsage(Page &$usage) : void {
		if(!isset($this->usage)) { $this->usage = array(); }
		if(isset($this->usage[$usage->getTitle()])) { return; }
		$this->usage[$usage->getTitle()] = $usage;
	}
	
	/**
	* getter for all pages the file is used on
	*
	* @return Generator|Page  the page the file is used on
	* @access public
	*/
	public function &getUsage() : Generator|Page {
		if(!isset($this->usage)) { throw new Exception("Usage is not set"); }
		foreach($this->usage as $usage) {
			yield $usage;
		}
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = parent::__debugInfo();
		if(isset($this->url)) { $info["url"] = $this->url; }
		if(isset($this->usage)) { $info["usage"] = $this->usage; }
		return $info;
	}
}
<?php
/**
* A class representing the result of a page deletion
*
* The class DeleteResult reprsents results of a page deletion
*
* @method string getTitle()
* @method string getReason()
* @method int getLogid()
*/
class DeleteResult {
	private readonly string $title;
	private readonly string $reason;
	private readonly int $logid;
	
	/**
	* constructor for class DeleteResult
	*
	* @param string $title   the title of the deleted page
	* @param string $reason  the reason why the page was deleted
	* @param int $logid      the id associated with the log entry
	* @access public
	*/
	public function __construct(string $title, string $reason, int $logid) {
		$this->title = $title;
		$this->reason = $reason;
		$this->logid = $logid;
	}
	
	/**
	* getter for the title
	*
	* @return string  the title
	* @access public
	*/
	public function getTitle() : string {
		return $this->title;
	}
	
	/**
	* getter for the reason
	*
	* @return string  the reason
	* @access public
	*/
	public function getReason() : string {
		return $this->reason;
	}
	
	/**
	* getter for the logid
	*
	* @return int  the logid
	* @access public
	*/
	public function getLogid() : int {
		return $this->logid;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		$info["title"] = $this->title;
		$info["reason"] = $this->reason;
		$info["logid"] = $this->logid;
		return $info;
	}
}
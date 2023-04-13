<?php
/**
* A class representing results of moving a page
*
* The class MoveResult represents the result of a page moved on a wiki
*
* @method string getFrom()
* @method string getTo()
* @method string getComment()
*/
class MoveResult {
	private readonly string $from;
	private readonly string $to;
	private readonly string $reason;
	
	/**
	* constructor fro class MoveResult
	*
	* @param string $from    where the page was moved from
	* @param string $to      where the page was moved to
	* @param string $reason  the reason of the move
	* @access public
	*/
	public function __construct(string $from, string $to, string $reason) {
		$this->from = $from;
		$this->to = $to;
		$this->reason = $reason;
	}
	
	/**
	* getter for the from
	*
	* @return string  the from
	* @access public
	*/
	public function getFrom() : string {
		return $this->from;
	}
	
	/**
	* getter for the to
	*
	* @return string  the to
	* @access public
	*/
	public function getTo() : string {
		return $this->to;
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
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		$info["from"] = $this->from;
		$info["to"] = $this->to;
		$info["reason"] = $this->reason;
		return $info;
	}
}
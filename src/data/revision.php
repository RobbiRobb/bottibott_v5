<?php
/**
* A class for representing Revisions
*
* The class Revision represents a revision performed by a user on a page
*
* @method void setId(int $id)
* @method int getId()
* @method void setParentId(int $parentId)
* @method int getParentId()
* @method void setTimestamp()
* @method int getTimestamp()
* @method void setSizediff()
* @method int getSizediff()
* @method void setIsBad(bool $isBad)
* @method bool isBad()
* @method void setPage(Page $page)
* @method Page getPage()
* @method void setUser(User $user)
* @method User getUser()
*/
class Revision {
	private readonly int $id;
	private readonly int $parentId;
	private readonly int $timestamp;
	private readonly int $sizediff;
	private readonly bool $isBad;
	private readonly Page $page;
	private readonly User $user;
	
	/**
	* constructor for class Revision
	*
	* @param int $id      the id of the revision
	* @param bool $isBad  true if the revision is considered bad, false if otherwise
	* @access public
	*/
	public function __construct(int $id, bool $isBad = false) {
		$this->id = $id;
		$this->isBad = $isBad;
	}
	
	/**
	* getter for the id
	*
	* @return int  the id
	* @access public
	*/
	public function getId() : int {
		return $this->id;
	}
	
	/**
	* setter for the parentId
	*
	* @param int $parentId  the parentId to set
	* @access public
	*/
	public function setParentId(int $parentId) : void {
		$this->parentId = $parentId;
	}
	
	/**
	* getter for the parentId
	*
	* @return int  the parentId
	* @access public
	*/
	public function getParentId() : int {
		if(!isset($this->parentId)) { throw new Exception("Parentid is not set"); }
		return $this->parentId;
	}
	
	/**
	* setter for the timestamp
	*
	* @param int $timestamp  the timestamp to set
	* @access public
	*/
	public function setTimestamp(int $timestamp) : void {
		$this->timestamp = $timestamp;
	}
	
	/**
	* getter for the timestamp
	*
	* @return int  the timestamp
	* @access public
	*/
	public function getTimestamp() : int {
		if(!isset($this->timestamp)) { throw new Exception("Timestamp is not set"); }
		return $this->timestamp;
	}
	
	/**
	* setter for the sizediff
	*
	* @param int $sizediff  the sizediff to set
	* @access public
	*/
	public function setSizediff(int $sizediff) : void {
		$this->sizediff = $sizediff;
	}
	
	/**
	* getter for the sizediff
	*
	* @return int  the sizediff
	* @access public
	*/
	public function getSizediff() : int {
		if(!isset($this->sizediff)) { throw new Exception("Sizediff is not set"); }
		return $this->sizediff;
	}
	
	/**
	* check if the revision is considered bad
	*
	* @return bool  true if the revision is bad, false otherwise
	* @access public
	*/
	public function isBad() : bool {
		return $this->isBad;
	}
	
	/**
	* setter for the page
	*
	* @param Page $page  the page to set
	* @access public
	*/
	public function setPage(Page &$page) : void {
		$this->page = $page;
	}
	
	/**
	* getter for the page
	*
	* @return Page  the page
	* @access public
	*/
	public function &getPage() : Page {
		if(!isset($this->page)) { throw new Exception("Page is not set"); }
		return $this->page;
	}
	
	/**
	* setter for the user
	*
	* @param User $user  the user to set
	* @access public
	*/
	public function setUser(User &$user) : void {
		$this->user = $user;
	}
	
	/**
	* getter for the user
	*
	* @return User  the user
	* @access public
	*/
	public function &getUser() : User {
		if(!isset($this->user)) { throw new Exception("User is not set"); }
		return $this->user;
	}
	
	/**
	* debug function
	*
	* @return array  debug info about this object
	* @access public
	*/
	public function __debugInfo() : array {
		$info = array();
		if(isset($this->id)) { $info["id"] = $this->id; }
		if(isset($this->parentId)) { $info["parentId"] = $this->parentId; }
		if(isset($this->timestamp)) { $info["timestamp"] = $this->timestamp; }
		if(isset($this->sizediff)) { $info ["sizediff"] = $this->sizediff; }
		if(isset($this->isBad)) { $info["isBad"] = $this->isBad; }
		if(isset($this->page)) { $info["page"] = $this->page; }
		if(isset($this->user)) { $info["user"] = $this->user; }
		return $info;
	}
}
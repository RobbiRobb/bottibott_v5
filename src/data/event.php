<?php
/**
* A class for representing events
*
* The class Event represents events that were performed by users and logged by the system
*
* @method string getType()
* @method string getAction()
* @method string getTitle()
* @method void setNamespace(int $namespace)
* @method int getNamespace()
* @method void setTimestamp(int $timestamp)
* @method int getTimestamp()
* @method void setUser(User $user)
* @method User getUser()
*/
class Event {
	private readonly string $type;
	private readonly string $action;
	private readonly string $title;
	private readonly int $namespace;
	private readonly int $timestamp;
	private readonly User $user;
	
	/**
	* constructor for class Event
	*
	* @param string $type    the type of the event
	* @param string $action  the action of the event that was performed
	* @param string $title   the target of the event
	* @access public
	*/
	public function __construct(string $type, string $action, string $title) {
		$this->type = $type;
		$this->action = $action;
		$this->title = $title;
	}
	
	/**
	* getter for the type
	*
	* @return string  the type
	* @access public
	*/
	public function getType() : string {
		return $this->type;
	}
	
	/**
	* getter for the action
	*
	* @return string  the action
	* @access public
	*/
	public function getAction() : string {
		return $this->action;
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
	* setter for the namespace
	*
	* @param int $namespace  the namespace to set
	* @access public
	*/
	public function setNamespace(int $namespace) : void {
		$this->namespace = $namespace;
	}
	
	/**
	* getter for the namespace
	*
	* @return int  the namespace
	* @access public
	*/
	public function getNamespace() : int {
		if(!isset($this->namespace)) { throw new Exception("Namespace is not set"); }
		return $this->namespace;
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
	* @return user  the user
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
		if(isset($this->type)) { $info["type"] = $this->type; }
		if(isset($this->action)) { $info["action"] = $this->action; }
		if(isset($this->title)) { $info["title"] = $this->title; }
		if(isset($this->namespace)) { $info["namespace"] = $this->namespace; }
		if(isset($this->timestamp)) { $info["timestamp"] = $this->timestamp; }
		if(isset($this->user)) { $info["user"] = $this->user; }
		return $info;
	}
}
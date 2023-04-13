<?php
/**
* A class representing the successful result of an upload
*
* The class UploadResult represents the successful result of an upload,
* storing all the important data returned by the API
*
* @method void setTimestamp(int $timestamp)
* @method int getTimestamp()
* @method void setSize(int $size)
* @method int getSize()
* @method void setWidth(int $width)
* @method int getWidth()
* @method void setHeight(int $height)
* @method int getHeight()
* @method void setComment(string $comment)
* @method string getComment()
* @method void setUrl(string $url)
* @method string getUrl()
* @method void setDescriptionurl(string $descriptionurl)
* @method string getDescriptionurl()
* @method void setsha1(string $sha1)
* @method string getsha1()
* @method void setMime(string $mime)
* @method string getMime()
* @method void setUser()
* @method User getUser()
*/
class UploadResult {
	private readonly string $filename;
	private readonly int $timestamp;
	private readonly int $size;
	private readonly int $width;
	private readonly int $height;
	private readonly string $comment;
	private readonly string $url;
	private readonly string $descriptionurl;
	private readonly string $sha1;
	private readonly string $mime;
	private readonly User $user;
	
	/**
	* constructor for class UploadResult
	*
	* @param string $filename  the name of the file that was uploaded
	* @access public
	*/
	public function __construct(string $filename) {
		$this->filename = $filename;
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
	* setter for the size of the file
	*
	* @param int $size  the size of the file to set
	* @access public
	*/
	public function setSize(int $size) : void {
		$this->size = $size;
	}
	
	/**
	* getter for the size of the file
	*
	* @return int  the size of the file
	* @access public
	*/
	public function getSize() : int {
		if(!isset($this->size)) { throw new Exception("Size is not set"); }
		return $this->size;
	}
	
	/**
	* setter for the width of the file
	*
	* @param int $width  the width of the file to set
	* @access public
	*/
	public function setWidth(int $width) : void {
		$this->width = $width;
	}
	
	/**
	* getter for the width of the file
	*
	* @return int  the width of the file
	* @access public
	*/
	public function getWidth() : int {
		if(!isset($this->width)) { throw new Exception("Width is not set"); }
		return $this->width;
	}
	
	/**
	* setter for the height of the file
	*
	* @param int $height  the height of the file to set
	* @access public
	*/
	public function setHeight(int $height) : void {
		$this->height = $height;
	}
	
	/**
	* getter for the height of the file
	*
	* @return int  the height of the file
	* @access public
	*/
	public function getHeight() : int {
		if(!isset($this->height)) { throw new Exception("Height is not set"); }
		return $this->height;
	}
	
	/**
	* setter for the comment
	*
	* @param string $comment  the comment to set
	* @access public
	*/
	public function setComment(string $comment) : void {
		$this->comment = $comment;
	}
	
	/**
	* getter for the comment
	*
	* @return string  the comment
	* @access public
	*/
	public function getComment() : string {
		if(!isset($this->comment)) { throw new Exception("Comment is not set"); }
		return $this->comment;
	}
	
	/**
	* setter for the url
	*
	* @param string $url  the url to set
	* @access public
	*/
	public function setUrl(string $url) : void {
		$this->url = $url;
	}
	
	/**
	* getter for the url
	*
	* @return string  the url
	* @access public
	*/
	public function getUrl() : string {
		if(!isset($this->url)) { throw new Exception("Url is not set"); }
		return $this->url;
	}
	
	/**
	* setter for the descriptionurl
	*
	* @param string $descriptionurl  the descriptionurl to set
	* @access public
	*/
	public function setDescriptionurl(string $descriptionurl) : void {
		$this->descriptionurl = $descriptionurl;
	}
	
	/**
	* getter for the descriptionurl
	*
	* @return string  the descriptionurl
	* @access public
	*/
	public function getDescriptionurl() : string {
		if(!isset($this->descriptionurl)) { throw new Exception("Descriptionurl is not set"); }
		return $this->descriptionurl;
	}
	
	/**
	* setter for the sha1 hash of the file
	*
	* @param string $sha1  the sha1 hash to set
	* @access public
	*/
	public function setsha1(string $sha1) : void {
		$this->sha1 = $sha1;
	}
	
	/**
	* getter for the sha1 hash of the file
	*
	* @return string  the sha1 hash of the file
	* @access public
	*/
	public function getsha1() : string {
		if(!isset($this->sha1)) { throw new Exception("Sha1 is not set"); }
		return $this->sha1;
	}
	
	/**
	* setter for the mime type
	*
	* @param string $mime  the mime type to set
	* @access public
	*/
	public function setMime(string $mime) : void {
		$this->mime = $mime;
	}
	
	/**
	* getter for the mime type
	*
	* @return string  the mime type
	* @access public
	*/
	public function getMime() : string {
		if(!isset($this->mime)) { throw new Exception("Mime is not set"); }
		return $this->mime;
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
		$info["filename"] = $this->filename;
		if(isset($this->timestamp)) { $info["timestamp"] = $this->timestamp; }
		if(isset($this->size)) { $info["size"] = $this->size; }
		if(isset($this->width)) { $info["width"] = $this->width; }
		if(isset($this->height)) { $info["height"] = $this->height; }
		if(isset($this->comment)) { $info["comment"] = $this->comment; }
		if(isset($this->url)) { $info["url"] = $this->url; }
		if(isset($this->descriptionurl)) { $info["descriptionurl"] = $this->descriptionurl; }
		if(isset($this->sha1)) { $info["sha1"] = $this->sha1; }
		if(isset($this->mime)) { $info["mime"] = $this->mime; }
		if(isset($this->user)) { $info["user"] = $this->user; }
		return $info;
	}
}
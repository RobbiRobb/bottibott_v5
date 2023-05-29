<?php
/**
* A class for representing API-requests for uploading
*
* The class UploadRequest allows the representation of API-requests for uploading files from a local system to a wiki
*
* @method void setFilepath(string $filepath)
* @method void setFilename(string $filename)
* @method void setText(string $text)
* @method void setComment(string $comment)
* @method void setIgnorewarnings(bool $ignorewarnings)
* @method SimpleXMLElement execute()
*/
class UploadRequest extends Request {
	private string $filepath;
	private string $fileurl;
	private string $filename;
	private string $token;
	private string $text;
	private string $comment;
	private bool $ignorewarnings;
	
	/**
	* constructor for class UploadRequest
	*
	* @param string $url             url to the wiki
	* @param string $filename        name of the file on the wiki
	* @param string $token           a csrf token required for uploading
	* @param string $text            initial text of the file page
	* @param string $comment         comment displayed when uploading the file
	* @param bool $ignorewarnings    whether warnings should be ignored when uploading a file or not
	* @access public
	*/
	public function __construct(
		string $url,
		string $filename,
		string $token,
		string $text = "",
		string $comment = "",
		bool $ignorewarnings = true
	) {
		$this->url = $url;
		$this->filename = $filename;
		$this->token = $token;
		$this->text = $text;
		$this->comment = $comment;
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* setter for the filepath
	*
	* @param string $filepath  the file path to be set
	* @access public
	*/
	public function setFilepath(string $filepath) : void {
		$this->filepath = $filepath;
	}
	
	/**
	* setter for the fileurl
	*
	* @param string $fileurl  the url to the file that should be set
	* @access public
	*/
	public function setFileurl(string $fileurl) : void {
		$this->fileurl = $fileurl;
	}
	
	/**
	* setter for the text
	*
	* @param string $text  the text to be set
	* @access public
	*/
	public function setText(string $text) : void {
		$this->text = $text;
	}
	
	/**
	* setter for the comment
	*
	* @param string $comment  the comment to be set
	* @access public
	*/
	public function setComment(string $comment) : void {
		$this->comment = $comment;
	}
	
	/**
	* setter for ignorewarnings
	*
	* @param bool $ignorewarnings  the new value to be set
	* @access public
	*/
	public function setIgnorewarnings(bool $ignorewarnings) : void {
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* executor for the API-request
	*
	* @return SimpleXMLElement  the SimpleXMLElement-representation of the query result
	* @access public
	*/
	public function execute() : SimpleXMLElement {
		if(isset($this->filepath) && isset($this->fileurl)) { throw new Error("Can not set both filepath and fileurl"); }
		if(!isset($this->filepath) && !isset($this->fileurl)) { throw new Error("Either filepath or fileurl has to be set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->setLogger($this->logger);
		$request->addToGetFields("action", "upload");
		$request->addToGetFields("format", "xml");
		// only set file if filepath is set
		if(isset($this->filepath)) { $request->addToPostFields("file", curl_file_create($this->filepath)); }
		// only set url if fileurl is set
		if(isset($this->fileurl)) { $request->addToPostFields("url", $this->fileurl); }
		$request->addToPostFields("filename", $this->filename);
		$request->addToPostFields("text", $this->text);
		$request->addToPostFields("comment", $this->comment);
		$request->addToPostFields("ignorewarnings", ($this->ignorewarnings ? "1" : "0"));
		$request->addToPostFields("token", $this->token);
		return $request->execute();
	}
	
	/**
	* getter for an API-request of an upload
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		if(isset($this->filepath) && isset($this->fileurl)) { throw new Error("Can not set both filepath and fileurl"); }
		if(!isset($this->filepath) && !isset($this->fileurl)) { throw new Error("Either filepath or fileurl has to be set"); }
		$request = new APIRequest($this->url);
		$request->setCookieFile($this->cookiefile);
		$request->addToGetFields("action", "upload");
		$request->addToGetFields("format", "xml");
		// only set file if filepath is set
		if(isset($this->filepath)) { $request->addToPostFields("file", curl_file_create($this->filepath)); }
		// only set url if fileurl is set
		if(isset($this->fileurl)) { $request->addToPostFields("url", $this->fileurl); }
		$request->addToPostFields("filename", $this->filename);
		$request->addToPostFields("text", $this->text);
		$request->addToPostFields("comment", $this->comment);
		$request->addToPostFields("ignorewarnings", ($this->ignorewarnings ? "1" : "0"));
		$request->addToPostFields("token", $this->token);
		return $request->getRequest();
	}
}
<?php
/**
* A class for uploading files to a wiki
*
* The class Upload allows a user to upload a local file or a file from a url to a wiki
*
* @method void setFilepath(string $filepath)
* @method void setFileurl(string $fileurl)
* @method void setText(string $text)
* @method void setComment(string $comment)
* @method void setIgnorewarnings(bool $ignorewarnings)
* @method UploadResult execute()
* @method CurlHandle getRequest()
* @method UploadResult parseResult(SimpleXMLElement $queryResult)
*/
class Upload {
	private Bot $bot;
	private string $filename;
	private string $filepath;
	private string $fileurl;
	private string $text;
	private string $comment;
	private bool $ignorewarnings;
	
	/**
	* constructor for class Upload
	*
	* @param Bot $bot              a reference to the bot object
	* @param string $filename      the name of the file after uploading
	* @param string $text          the text of the file description page
	* @param string $comment       the comment for the upload
	* @param bool $ignorewarnings  true if warnings should be ignored, false otherwise
	* @access public
	*/
	public function __construct(
		Bot &$bot,
		string $filename,
		string $text = "",
		string $comment = "",
		bool $ignorewarnings = true
	) {
		$this->bot = $bot;
		$this->filename = $filename;
		$this->text = $text;
		$this->comment = $comment;
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* setter for the filepath
	*
	* @param string $filepath  the filepath to set
	* @access public
	*/
	public function setFilepath(string $filepath) : void {
		$this->filepath = $filepath;
	}
	
	/**
	* setter for the fileurl
	*
	* @param string $fileurl  the fileurl to set
	* @access public
	*/
	public function setFileurl(string $fileurl) : void {
		$this->fileurl = $fileurl;
	}
	
	/**
	* setter for the text
	*
	* @param string $text  the text to set
	* @access public
	*/
	public function setText(string $text) : void {
		$this->text = $text;
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
	* setter for ignoring warnings
	*
	* @param bool $ignorewarnings  true if warnings should be ignored, false otherwise
	* @access public
	*/
	public function setIgnorewarnings(bool $ignorewarnings) : void {
		$this->ignorewarnings = $ignorewarnings;
	}
	
	/**
	* executor for uploading a file
	*
	* @return UploadResult  a UploadResult representing the upload
	* @access public
	*/
	public function execute() : UploadResult {
		$request = new UploadRequest(
			$this->bot->getUrl(),
			$this->filename,
			$this->bot->getToken("csrf"),
			$this->text,
			$this->comment,
			$this->ignorewarnings
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->filepath)) { $request->setFilepath($this->filepath); }
		if(isset($this->fileurl)) { $request->setFileurl($this->fileurl); }
		return $this->parseResult($request->execute());
	}
	
	/**
	* getter for the request of an upload
	*
	* @return CurlHandle  a reference to the request handle
	* @access public
	*/
	public function &getRequest() : CurlHandle {
		$request = new UploadRequest(
			$this->bot->getUrl(),
			$this->filename,
			$this->bot->getToken("csrf"),
			$this->text,
			$this->comment,
			$this->ignorewarnings
		);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		if(isset($this->filepath)) { $request->setFilepath($this->filepath); }
		if(isset($this->fileurl)) { $request->setFileurl($this->fileurl); }
		return $request->getRequest();
	}
	
	/**
	* parse result into UploadResult object
	* called automatically on execution of request
	* can be called manually when using multirequest for execution
	*
	* @param SimpleXMLElement $queryResult  the result of the upload returned by the api
	* @return UploadResult                  a uploadresult object representing the result
	* @access public
	*/
	public function parseResult(SimpleXMLElement $queryResult) : UploadResult {
		if((string)$queryResult->upload["result"] !== "Success") { throw new Exception("Error on upload"); }
		
		$imageinfo = $queryResult->upload->imageinfo;
		
		$result = new UploadResult((string)$queryResult->upload["filename"]);
		if(isset($imageinfo["timestamp"])) { $result->setTimestamp(strtotime((string)$imageinfo["timestamp"])); }
		if(isset($imageinfo["size"])) { $result->setSize((int)$imageinfo["size"]); }
		if(isset($imageinfo["width"])) { $result->setWidth((int)$imageinfo["width"]); }
		if(isset($imageinfo["height"])) { $result->setHeight((int)$imageinfo["height"]); }
		if(isset($imageinfo["comment"])) { $result->setComment((string)$imageinfo["comment"]); }
		if(isset($imageinfo["url"])) { $result->setUrl((string)$imageinfo["url"]); }
		if(isset($imageinfo["descriptionurl"])) { $result->setDescriptionurl((string)$imageinfo["descriptionurl"]); }
		if(isset($imageinfo["sha1"])) { $result->setsha1((string)$imageinfo["sha1"]); }
		if(isset($imageinfo["mime"])) { $result->setMime((string)$imageinfo["mime"]); }
		if(isset($imageinfo["user"])) {
			$user = new User((string)$imageinfo["user"], (int)$imageinfo["userid"]);
			$result->setUser($user);
		}
		return $result;
	}
}
<?php
/**
* A class for representing token loading
*
* @method string execute()
*/
class Token {
	private Bot $bot;
	private string $type;
	
	/**
	* constructor for class Token
	*
	* @param Bot $bot  a reference to the bot object
	* @access public
	*/
	public function __construct(Bot &$bot, string $type) {
		$this->bot = $bot;
		$this->type = $type;
	}
	
	/**
	* executor for token query
	*
	* @return string  the token of the requested type
	* @access public
	*/
	public function execute() : string {
		$request = new TokenRequest($this->bot->getUrl(), $this->type);
		$request->setCookieFile($this->bot->getCookieFile());
		$request->setLogger($this->bot->getLogger());
		$queryResult = $request->execute();
		return (string)$queryResult->query->tokens[$this->type . "token"];
	}
}
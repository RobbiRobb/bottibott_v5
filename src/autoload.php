<?php
/**
* Autoloader for the BottiBott v5 MediaWiki bot
* Will automatically include all relevant files to guarantee classes are loaded
*/
spl_autoload_register(function($class) {
	if(file_exists(__DIR__ . "/" . strtolower($class) . ".php")) {
		require(strtolower($class) . ".php");
	}
});
?>
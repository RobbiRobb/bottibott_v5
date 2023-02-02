## Introduction
This project is a MediaWiki-bot intended for querying and manipulating data. It has a wide range of functions that come in handy when using the bot on a day to day basis.

## Installation
Download all files and save them in a place where PHP can access them. To then use the bot, create a new file and include the autoload file.

	require_once("src/autoload.php");

You can now create a new instance of the bot to work with.

	$bot = new Bot("<url to the api>");

The correct url to the api can be found on special:version, it should end with `/api.php`

## Examples

### Logging in
This basic example consists of a simple login and logout.

	require_once("src/autoload.php");
	
	$bot = new Bot(<url to the api>);
	var_dump($bot->login(<username>, <password>));
	$bot->logout();

This successfully logs the bot in and out of a wiki, provided the url, username and password are correct. For querying data it is usually not necessary to log in, but some actions may require you to log in. It is generally recommended to log in to get access to higher limits to go easy on the servers of the wiki you are working on.

### Logging requests
If you are experiencing bugs or have trouble with requests failing, it is possible to log requests. To start the logger, just call

	$bot->startLogging();

After this call all requests and responses will be logged in a log file. To stop logging just call

	$bot->stopLogging();

The log entries contain timestamps, HTTP response codes, requested URLs, body sizes and time needed to execute the requests.

### Generating a list of all articles on a wiki
This example makes use of one of the provided generator methods to generate a list of all articles in a wiki.

	require_once("src/autoload.php");
	
	$bot = new Bot(<url to the api>);
	$bot->login(<username>, <password>);
	
	foreach($bot->getAllpages("0") as $page) {
		var_dump($page);
	}
	
	$bot->logout();

The method getAllpages returns a generator allowing to iterate over all pages in a given namespace.

### Expanding templates
This is a more advanced example, using the bot to expand the templates on a page, securely remove a parameter and then change the content of the article on the wiki.

	require_once("src/autoload.php");
	
	$bot = new Bot(<url to the api>);
	$bot->login(<username>, <password>);
	
	foreach($bot->expandTemplates($bot->getContent("<article>")->current()["content"])->expand() as $parsetree) {
		if($parsetree->getTitle() === "<title of the template you want to look for>") {
			$oldContent = $parsetree->parse()->rebuild();
			$newContent = $parsetree->parse()->removeParam("<name of the parameter to remove>")->rebuild();
			$bot->edit($parsetree->getTitle(), str_replace($oldContent, $newContent, $content));
		}
	}
	
	$bot->logout();

This code fetches the source code of a given article, expands all the templates, checks which to parse, parses that one and removes the parameter before rebuilding both templates, replacing them in the article and editing the page to make sure the changes are save. Even this is obviously pretty basic and can be expanded by a lot, for example by adding another loop which makes it work on all pages transcluding a specific template.
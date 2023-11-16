## Introduction
This project is a MediaWiki-bot intended for querying and manipulating data. It has a wide range of functions that come in handy when using the bot on a day to day basis.

## Installation
Download all files and save them in a place where PHP can access them. To then use the bot, create a new file and include the autoload file.

```php
require_once("src/autoload.php");
```

You can now create a new instance of the bot to work with.

```php
$bot = new Bot(URL);
```

The correct url to the api can be found on special:version, it should end with `/api.php`

## Logging
The bot provides logging capabilities for debugging purposes, logging requests and responses. When turned on, it will automatically log transmissions to a log file. When executing multiple requests in parallel using the `ApiMultirequest`, requests will not be logged.

To start logging, call

```php
$bot->startLogging();
```

To stop logging, call

```php
$bot->stopLogging();
```

By default, all log entries will be written to `latest.log`, this can be changed while instantiating the bot.

## apihighlimits
If you have access to an account with `apihighlimits`, it is recommended to always log in to said account using the bot. That way the program will execute faster and reduce loads on the server of the wiki.

To log in, simply call

```php
$bot->login(USERNAME, PASSWORD);
```

## Controllers
All data is provided by controllers. Those controllers take the bot object to know which wiki to connect to and which session to use. A range of controllers is available to query and modify data. This list is meant to give a general overview of all controllers but does not go into detail on all of them. When in doubt, check the documentation of the controller.

### Page list controllers

#### Backlinks
The backlinks controller provides a list of all pages linking to a page.

```php
$controller = new Backlinks($bot, "Main Page");
foreach($controller->getBacklinks() as $page) {
	var_dump($page->getTitle());
}
```

#### Category
The category controller provides a list of all pages in a category. The `category:`-prefix is not required.

```php
$controller = new Category($bot, "Main Category");
foreach($controller->getMembers() as $page) {
	var_dump($page->getTitle());
}
```

Providing an array of types to the controller allows you to filter the category for specific members.

#### Namespacelist
The namespacelist controller provides a list of all pages in a namespace.

```php
$controller = new Namespacelist($bot, 0);
foreach($controller->getAllPages() as $page) {
	var_dump($page->getTitle());
}
```

It is possible to set a filter on what types of pages are queried, excluding redirects or nonredirects.

#### Transclusionlist
The transclusionlist controller provides a list of all pages transcluding a page.

```php
$controller = new Transclusionlist($bot, "Template:Template");
foreach($controller->getTransclusions() as $page) {
	var_dump($page->getTitle());
}
```

### Page property controllers

#### Categorylist
The categorylist controller provides a list of all categories a page is in.

```php
$controller = new CategoryList($bot, array("Main Page", "Main Page2"));
foreach($controller->getCategories() as $page) {
	foreach($page->getCategories() as $category) {
		var_dump($page->getTitle(), $category);
	}
}
```

The function will return a generator of pages, which in turn can provide a generator of strings that contain all the categories a page is in. If only a single page is requested, it will instead return a single page object with all categories. If required, the function will always return a generator.

#### Content
The content controller provides the content of a page.

```php
$controller = new Content($bot, array("Main Page", "Main Page2"));
foreach($controller->get() as $page) {
	var_dump($page->getContent());
}
```

The generator behaves the same as for the categorylist. Additionally, it is possible to directly load the content of a page list. To do that, simply call one of the provides functions.

```php
$controller = new Content($bot);
foreach($controller->fromNamespace(0) as $page) {
	var_dump($page->getContent());
}
```

Other lists require other parameters but work in the same way.

#### Langlinks
The langlinks controller provides a list of all langlinks on a page.

```php
$controller = new Langlinks($bot, array("Main Page", "Main Page2"));
foreach($controller->getLinks() as $page) {
	foreach($page->getLanglinks() as $lang => $link) {
		var_dump($page->getTitle(), $lang, $link);
	}
}
```
The generator behaves the same as for categorylist.

#### Links
The links controller provides a list of all links on a page.

```php
$controller = new Links($bot, array("Main Page", "Main Page2"));
foreach($controller->getLinks() as $page) {
	foreach($page->getLinks() as $link) {
		var_dump($page->getTitle(), $link->getTitle());
	}
}
```

The generator behaves the same as for categorylist. Additionally it is possible to filter for specific pages as well as only include certain namespaces.

#### Parser
The parser controller can be used to parse wikitext, either expanding all templates and magic words or even parse it down to the final HTML.

To parse to HTML call

```php
$controller = new Parser($bot);
$controller->setPage("Main Page");
var_dump($controller->parseText()->getExpandedText());
```

To expand all templates call

```php
$controller = new Parser($bot);
$controller->setPage("Main Page");
var_dump($controller->expandText());
```

Instead of setting a page it is also possible to set content directly. That way you can expand text that is not currently on a wiki page.

Additionally it is possible to get the sections of a page.

```php
$controller = new Parser($bot);
$controller->setPage("Main Page");
foreach($controller->getSections() as $section) {
	var_dump($section);
}
```

#### Parsetree
The parsetree controller is used to expand templates to Template objects, where they then can be modified before getting returned to their initial wikitext. That way it is possible to change parameters of templates without having to worry about affecting other parts of a page.

```php
$controller = new Parsetree($bot);
$controller->setPages(array("Main Page", "Main Page2"));
foreach($controller->expand() as $page) {
	foreach($page->getTemplates() as $template) {
		if(strtolower(trim($template->getTitle())) === SEARCH_TEMPLATE) {
			// modify the template however you like
		}
	}
	var_dump($page->getContent());
}
```

The templates will be automatically be parsed to the full text of the page and all changes will be applied.

The controller also provides generators the same way the content controller provides the, for easy access to templates from lists. Similar to the parser it is also possible to directly expand content provided by the user.

#### Revisions
The revisions controller provides revisions of pages or data on a single revision from its id.

To query data on a revision from its id use

```php
$controller = new Revisions($bot);
$controller->setIds(array("1", "2", "3"));
foreach($controller->getRevisionsFromRevids() as $revision) {
	var_dump($revision);
}
```

The generator behaves the same as for categorylist.

To query revision of a page use

```php
$controller = new Revisions($bot);
$controller->setPages(array("Main Page", "Main Page2"));
foreach($controller->getRevisionsFromPages() as $page) {
	foreach($page->getRevisions() as $revision) {
		var_dump($revision);
	}
}
```

The generator behaves the same as for categorylist.

### File controllers

##### Fileurl
The fileurl controller provides the url of a file from its name.

```php
$controller = new Fileurl($bot, array("File1.png", "File2.jpg"));
foreach($controller->getUrl() as $file) {
	var_dump($file->getUrl());
}
```

The generator behaves the same as for categorylist.

#### Fileusage
The fileusage controller provides a list of all pages using a file.

```php
$controller = new Fileusage($bot, array("File1.png", "File2.jpg"));
foreach($controller->getUsage() as $file) {
	foreach($file->getUsage() as $usage) {
		var_dump($usage->getTitle());
	}
}
```

Filtering by namespace is possible.

### User controllers

#### Usercontribs
The usercontribs controller provides all contributions a user has done on a wiki.

```php
$controller = new Usercontribs($bot, array("User1", "User2"));
foreach($controller->getContribs() as $user) {
	foreach($user->getContributions() as $contrib) {
		var_dump($contrib);
	}
}
```

The generator behaves the same as for categorylist.

#### Userinfo
The userinfo controller provides information on one or multiple users.

```php
$controller = new Userinfo($bot, array("User1", "User2"));
foreach($controller->execute() as $user) {
	var_dump($user);
}
```

#### Userlist
The userlist controller provides a list of all users registered on a wiki.

```php
$controller = new Userlist($bot);
foreach($controller->getAllUsers() as $user) {
	var_dump($user);
}
```

Querying for only active users is possible, this will then include their recentactions.

#### Userrights
The userrights controller provides a list of all rights the user associated with the bot has.

```php
$controller = new Userrights();
foreach($controller->getRights() as $right) {
	var_dump($right);
}
```

To get all rights for a user that is not the bot use the Userinfo controller.

### Event controllers

#### Logevents
The logevents controller provides a list of all events logged on a wiki. It only provides events for a specified action.

```php
$controller = new Logevents($bot, "delete/delete");
foreach($controller->getEvents() as $event) {
	var_dump($event);
}
```

### Meta controllers

#### Siteinfo
The siteinfo controller loads meta data about the wiki.

```php
$controller = new Siteinfo($bot, ["namespaces"]);
$siteinfo = $controller->execute();
var_dump($siteinfo->getNamespaces());
```

Not all properties are supported, make sure to check the class when trying to query meta data about the wiki.

### Writing controllers

#### Delete
The delete controller allows a user to delete a page.

```php
$controller = new Delete($bot, "Main Page", "Deleting Main Page");
var_dump($controller->execute());
```

#### Edit
The edit controller allows a user to edit a page or revert an edit on a page.

```php
$controller = new Edit($bot, "Main Page");
$controller->setContent("Your content here");
var_dump($controller->execute());
```

Note that you will have to send the entire content of the page.

To revert a revision provide the id of the revision you wish to revert.

```php
$controller = new Edit($bot, "Main Page");
$controller->setRevision(3);
var_dump($controller->execute());
```

#### Move
The move controller allows a user to move a page to a new name.

```php
$controller = new Move($bot, "From", "To", "Reason");
var_dump($controller->execute());
```

Moving without redirect as well as moving the talk page are turned on by default but can be disabled.

#### Upload
The upload controller allows a user to upload a file. Using both local files as well as files from a different website is supported.

To upload a file from your local device use

```php
$controller = new Upload($bot, "file.png");
$controller->setFilepath("Path\to\the\file\on\your\device.png");
var_dump($controller->execute());
```

To upload a file from a different website use

```php
$controller = new Upload($bot, "file.png");
$controller->setFileurl("https://www.test.org/file.png");
var_dump($controller->execute());
```

Make sure to set a text and a comment before uploading a file.

### Miscellaneous

#### Templateparameters
The templateparameters controller provides a list of all parameters on a template. It can get parameters from both, a page or content.

To get parameters from a page use

```php
$controller = new Templateparameters($bot);
$controller->setPage("Template:Template");
foreach($controller->getParameters() as $param) {
	var_dump($param);
}
```

To get parameters from content use

```php
$controller = new Templateparameters($bot);
$controller->setContent(CONTENT);
foreach($controller->getParameters() as $param) {
	var_dump($param);
}
```

#### Login
This controller is used by the bot directly. There is no need for you to call it.

#### Logout
This controller is used by the bot directly. There is no need for you to call it.

#### Token
This controller is used by the bot directly. There is no need for you to call it.
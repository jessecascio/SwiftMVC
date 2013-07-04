<?php

/**
 * This is the starting point for the SwiftMCV framework, all http requests should be 
 * routed through this file.  This file should only name constants that are changing during development, 
 * all other constants should be placed in the .ini file or in the Swift->ConfigureApplication() function,
 * which is off the web root folder.  If changes are mode to the project folder structure, verify that constants in
 * Swift->ConfigureApplication() do not need to be updated.  This file also enables caching, starts the session, 
 * and runs the application. There are helper functions for autoloading and fatal error handeling,  
 * and all Swift exceptions are to be caught and dealt with in this file for proper error display.
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @copyright  jessesnet.com
 * @version    1.0
 * @todo 	   Get the PHP header caching to work, update better loggin constantss
 */

/******************  U P D A T A B L E   V A R S ******************************/

// turns caching on / off
define ('CACHING', FALSE);

// whether to echo PHP errors in the dev environment
define ('PHP_ERRORS', TRUE);

// whether to echo exception messages in the dev environment
define ('SWIFT_ERRORS', TRUE);

/*******************************************************************************/    

// absolute path to the application folder from this file
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// set include path to shared library, any sub directory of the include path
// will need to be namespaced, import ns into a file with 'use' keyword
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../libraries'),
	get_include_path()
)));

// set up auto loading functionality
spl_autoload_register( '_autoload' );

// catches any fatal errors and displays the standard error page
register_shutdown_function( '_catchFatal' );

// start session
session_start(); 

// configures the application, must do before caching
require_once(APPLICATION_PATH . '/Swift.php');
Swift::ConfigureApplication();

/***********************   C H E C K    C A C H E   *******************************/

// if cache file found execution is terminated, application is not run
$Cache = new core\RequestCache(REQUEST_PATH);
$Cache->load();

/************************  R U N    A P P L I C A T I O N  ************************/

use exceptions\SwiftException;

// ALL exceptions are to be caught here, dont catch inside application
// unless you have to catch and cast an exception to a SwiftException
try {

	$Application = new Swift();
	$Application->bootstrap();
	$Application->run();

} catch (SwiftException $e) {
	// helpful with debugging 
	echo DEV_ENVIRONMENT && SWIFT_ERRORS ? $e->getMessage() : '';
	displayErrorPage();
}

/**********************  H E L P E R   F U N C T I O N S  ************************/

/**
 * Displays the error view file: error/index.phtml
 */	
function displayErrorPage()
{
	// only want to display the error page once
	if (!isset($GLOBALS['error_diplay'])) {
		$GLOBALS['error_diplay'] = TRUE;
		// create a new view object with the uri path to the error message
		// can display whatever view file for the error message
		$View = new core\View('error/index');
		$View->render();
	}
}

/**
 * Log when fatal error caught
 */	
function _catchFatal() 
{	
	if (error_get_last() !== NULL) {
		$error_info = error_get_last();
		$message = date("Y-m-d H:i:s") . " - FATAL ERROR : (file) " . basename($error_info['file']) . " #" . $error_info['line'] . 
		      	   " (message) " . $error_info['message'];
		utilities\Log::write($message);
		displayErrorPage();
	}
}

/**
 * Auto loading functionality
 * @param string - the class name
 */
function _autoload($class)
{
	$include_paths = explode(PATH_SEPARATOR, get_include_path());

	// check all the include paths
	foreach ($include_paths as $path) {
		// need to update prefixed class namespace
		$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
		if (file_exists($path . DIRECTORY_SEPARATOR . $class . '.php')) {
			include_once($class .'.php');
			return;
		}
	}

	// file not found
	$message = date("Y-m-d H:i:s") . " - LOAD ERROR  : Unable to load class: " . $class;
	utilities\Log::write($message);
	displayErrorPage();
}

?>
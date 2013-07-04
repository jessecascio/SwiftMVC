<?php

use utilities\Db;
use utilities\Config;

/**
 * This is the main application file, typically call a ‘Bootstrap’ file.  This initializes and defines 
 * all functionality that is needed on every request. This file should be kept to a minimum as it is
 * run every request.  Only place things that are essential on a global level in here.
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage application  
 * @copyright  jessesnet.com     
 * @todo       Update config.ini for development to inherit from production
 */

class Swift
{
	/**
	 * @var Router
	 */	
	private $_Router = null;

	/**
	 * Runs all the _init() functions in top down order
	 */	
	public function bootstrap()
	{
		foreach (get_class_methods($this) as $function) {
			if (strpos($function,'_init') !== FALSE) {
				call_user_func(array($this,$function));
			}	
		}
	}

	/**
	 * Dispatches the request
	 */
	public function run()
	{
		$this->_Router->dispatch();
	}
	
	/**
	 * Set up the environment variables
	 */
	private function _initEnvironment()
	{
		// any other environment settings that are need for the application
	}

	/**
	 * Creates the router object
	 */
	private function _initRouter()
	{	
		$Request = new core\Request(REQUEST_PATH);
		$this->_Router = new core\Router($Request);
	}

	/**
	 * Tests db connection, if not essential on every request, move to appropriate controller _init()
	 */
	private function _initDb()
	{	
		// grab .ini config options from .ini file
		$config_data = parse_ini_file(APPLICATION_PATH . '/configs/config.ini', TRUE);
		$config_data = DEV_ENVIRONMENT ? $config_data['development'] : $config_data['production'];

		// pass the .ini array to the config object
		Config::setConfigData($config_data);
		$Config = Config::getData();

		$dbHost = 'mysql:dbname=' . $Config->db->dbname . ';host=' . $Config->db->host;
		$dbUser = $Config->db->user;
		$dbPwd  = $Config->db->pwd;

		if (Db::connect($dbHost, $dbUser, $dbPwd) === FALSE) {
			// stops application execution on a failded db login attempt
			throw new exceptions\DbException(__FILE__, __LINE__, "Unable to connect to database: " . Db::getErrorMessage());
		}
	}
	
	
	/**
	 * Configures the application before execution
	 */
	public static function ConfigureApplication()
	{	
		// var is from .htaccess, dev and staging are considerd DEV_ENVIRONMENT
		getenv('APPLICATION_ENV') === 'development' ? define('DEV_ENVIRONMENT', TRUE) : define('DEV_ENVIRONMENT', FALSE);

		// PHP error display
		DEV_ENVIRONMENT && PHP_ERRORS ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

		// typically done during bootstrapping but needed it early for the cache
		date_default_timezone_set('America/Phoenix');

		// folder name of the web root i.e. 'public' or 'public_html'
		define('WEB_ROOT', 'public');

		// set cache dir path
		define('CACHE_PATH', realpath(APPLICATION_PATH . '/data/cache'));

		// set log dir path
		define('LOG_PATH', realpath(APPLICATION_PATH . '/logs'));

	    // BASE_URL - base href used in layout html for links
	    // VIEW_PATH - path to the view files
	    // LAYOUT_FILE - the layout to use
	    
	    if (DEV_ENVIRONMENT) {
	    	$basePath = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], WEB_ROOT) + strlen(WEB_ROOT));
	    	define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . $basePath . "/");	    	
	    } else {
	    	$basePath = '/';
	    	define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . $basePath);
	    }

	    define('VIEW_PATH' , realpath(APPLICATION_PATH . '/views/scripts'));

	    define('LAYOUT_FILE' , realpath(APPLICATION_PATH . '/layouts/layout.phtml'));

	    // a "cleaned" version of the uri request i.e. contorller/action/value
    	// no leading or trailing slashes, no ? parameters, no unexpected characters
    	define('REQUEST_PATH', utilities\Uri::getRequestPath( $_SERVER['REQUEST_URI'] , WEB_ROOT, DEV_ENVIRONMENT));
	}
}	

?>

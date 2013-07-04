<?php

namespace core;
use exceptions\RouteException;

/**
 * The purpose of the Router class is to accept an incoming Request and based off of the URI 
 * determine the appropriate controller/action.  The Router determines the controller to instantiate and 
 * which method to call.  The Router also determines if this is a valid request and prevents 
 * execution for invalid requests.
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage core  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       Next version will allow for custom routes via routes.ini.  Allow for diff default
 *             controller, don't require an indexAction in every controller.  When redirect currently
 *			   losing all data in the Request object.  If no action used no need for a controller
 */

class Router
{
	/**
	 * @var string - Default controller
	 */
	private $_controller = 'IndexController';

	/**
	 * @var string - Default action
	 */ 
	private $_action = 'indexAction';

	/**
	 * @var Request 
	 */
	private $_Request = null;
	
	/**
	 * @var string - Uri of current reqeust
	 */
	private $_uri = '';


	/**
	 * Creates the current route base off the Request Uri
	 * @param Request 
	 */	
	public function __construct(Request $Request)
	{
		$this->_Request = $Request;
		$this->_uri = $Request->getUri();
		$this->_setRoute();
	}

	/**
	 * Executes the route by calling the appropriate action of the controller
	 * @throws RouteException - If cannot find controller or action 
	 */
	public function dispatch()
	{
		//make sure that the controller exists
		if (file_exists(APPLICATION_PATH . "/controllers/" . $this->_controller . ".php")) {

			// create the controller so we can verify the action
			require (APPLICATION_PATH . "/controllers/" . $this->_controller . ".php");
			$Controller = new $this->_controller();

			// make sure that the controller is an instance of the parent class
			if (!($Controller instanceof Controller)) {
				throw new RouteException(__FILE__, __LINE__, "Controller does not extend Controller: " . $this->_controller);
			}

			// make sure the selected action exists
			if (!method_exists($Controller, $this->_action)) {
				throw new RouteException(__FILE__, __LINE__, "Cannot find the following action: " . $this->_action);
			}
			
			// create the View object with the current uri
			// the View is used to render html output and is coupled to the uri
			$View = new View($this->_uri);

			// give the controllers access to the view and request objects
			$Controller->setView($View);
			$Controller->setRequest($this->_Request);

			// initilize the controller
			$Controller->_init();
			// call the action
			$Controller->{$this->_action}();

		} else {
			throw new RouteException(__FILE__, __LINE__, "Cannot find the following controller: " . $this->_controller);
		}
	}

	/**
	 * At times you may need an instance of the router for handeling redirects or other tasks
	 * created with a generic Request object, will need to reinstantiated once the view is known (see redirect())
	 * @return Router 
	 */
	public static function getInstance()
	{
		return new Router(new Request());
	}

	/**
	 * Moves application control to a new controller/action, if you try and use this for moving
	 * around in the same controller a fatal error will occur, only use to swtich controllers
	 * @param string - controller/action
	 */
	public function redirect($uri)
	{	
		$this->_uri = $uri;
		$this->_Request = new Request($uri);
		$this->_setRoute();
		$this->dispatch();
	}

	/**
	 * Determine the controller and action from uri
	 */
	private function _setRoute()
	{
		// default route naming from $uri:
		//   [0] : controller-name -> ControllerNameController
		//   [1] : action-name -> actionNameAction
		//    the rest are value pairs of data

		$paths = explode('/',$this->_uri);

		// first uri value is controller
		if (isset($paths[0]) && trim($paths[0])) {	
			// create the formated controller name
			$this->_setControllerName(array_shift($paths));

			//second uri value is method
			if (isset($paths[0]) && trim($paths[0])) {	
				// create the formated action name
				$this->_setActionName(array_shift($paths));
			}
		}
	}
	
	/**
	 * Formats a controller name
	 * @param string
	 */
	private function _setControllerName($controller)
	{
		//remove any - chars, and capitalize every word
		$controller = ucwords(str_replace('-', ' ', $controller));
		$controller = str_replace(' ', '', $controller);

		$this->_controller = $controller . "Controller";
	}

	/**
	 * Formats an action name
	 * @param string
	 */
	private function _setActionName($action)
	{
		//remove any - chars, and capitalize every word
		$action = ucwords(str_replace('-', ' ', $action));
		$action = str_replace(' ', '', $action);
		//lowercase the first character
		$action[0] = strtolower($action[0]);

		$this->_action = $action . "Action";	
	}
}	

?>


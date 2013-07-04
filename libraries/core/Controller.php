<?php

namespace core;

/**
 * This is the parent class for all controllers.  Setters for the needed objects, custom __constructor
 * functionality, auto rendering of view on controller termination
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage core  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

abstract class Controller
{
	/**
	 * @var View
	 */
	protected $view = null;

	/**
	 * @var Request
	 */
	protected $request = null;

	/**
	 * Controllers cannot have their own constructors.  The controllers 
	 * are instantiated before they are ready for initialization
	 */
	final public function __construct(){}

	/**
	 * Used instead of __construct() for controler initilization
	 * dont implement here, only in children controllers
	 */
	public function _init(){}

	/**
	 * @param View
	 */
	final public function setView(View $view)
	{
		// this gives all controller children access to their own view object
		$this->view = $view;
	}

	/**
	 * @param Request
	 */
	final public function setRequest(Request $request)
	{
		// this gives all controller children access to their own request object
		$this->request = $request;
	}

	/**
	 * Once the controller is done executing the default view is rendered
	 */
	final public function __destruct()
    {
    	// if an error occured then the view may not be an object
    	if (is_object($this->view))
    	{
    		$this->view->autoRender();
    	}
    }
}	

?>
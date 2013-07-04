<?php

namespace core;
use exceptions\ViewException;

/**
 * The view object is responsible for displaying output back to the user.  The object is couple to a uri 
 * meaning that the instance of this view object is created specifically for the current uri.
 * Since the layout files are included in this page they have access to all vars/functions via $this
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage core  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo       
 */

class View
{
	/**
	 * @var string
	 */
	private $_default_view = 'index/index.phtml';

	/**
	 * @var string - page content
	 */
	public $content = '';

	/**
	 * @var array - js scripts to append to head
	 */
	private $_scripts = array();

	/**
	 * @var array - additional data to be stored in the view obj
	 */
	private $_data = array();

	/**
	 * @var bool - whether to auto render on controller destruction
	 */
	private $_no_auto_render = FALSE;

	/**
	 * @param string - current uri
	 */
	public function __construct($uri)
	{	
		$this->_setDefaultView($uri);
	}

	/**
	 * @param string - var name
	 * @param string - var value
	 */
	public function __set($var, $value)
	{
		$this->_data[$var] = $value;
	}

	/**
	 * @param string - var name
	 * @param string - var value
	 */
	public function __get($var)
	{
		return isset($this->_data[$var]) ? $this->_data[$var] : '';
	}

	/**
	 * Add a script to be appended to the HTML <head>
	 * @param string - path of js script relative to index.php
	 */
	public function addScript($scriptPath)
	{
		$this->_scripts[] = $scriptPath;
	}

	/**
	 * Automatically renders the default view when the controller is destructed
	 */
	public function autoRender()
	{
		if (!$this->_no_auto_render) {
			$this->render();
		}
	}

	/**
	 * Disables the automatic rendering of the view, used with ajax calls
	 */
	public function noAutoRender()
	{	
		$this->_no_auto_render = TRUE;
	}

	/**
	 * Echos the view embedded in the layout
	 * @param string - Override the view to render for this uri
	 * @throws ViewException - Unbale to find the view file
	 */
	public function render($view_path = '')
	{
		$view_file = trim($view_path) ? $view_path : $this->_default_view;
		$view_path = VIEW_PATH . "/" . $view_file;

		if (file_exists($view_path)) {
			// store the view into a var to be injected into the layout
			$this->content = $this->_getViewContents($view_path);

			ob_start();
			include (LAYOUT_FILE);
			$page = ob_get_clean();
			// displays layout with content emebedded within
			echo $page;
			
			// save the view file into the cache based on the request path
			$RequestCache = new RequestCache(REQUEST_PATH);
			$RequestCache->cacheView($page);
			
		} else {
			throw new ViewException(__FILE__, __LINE__, "Unable to locate view: " . $view_path);
		}
	}

	/**
	 * All php embedded in view will be executed
	 * @param string - complete view path
	 * @return string - contents of a view
	 */
	private function _getViewContents($view_file)
	{
		ob_start();
		include($view_file);
		return ob_get_clean();
	}

	/**
	 * Returns a view w/o the layout
	 * @param string - view name: index/index.phtml
	 * @return string - contents of a view
	 * @throws ViewExeption - cant find the view file
	 */
	public function getView($view)
	{
		$view_file = VIEW_PATH . "/" . $view;
	
		if (file_exists($view_file)) {
			ob_start();
			include($view_file);
			$content = ob_get_clean();
			return $content;
		} else {
			throw new ViewException(__FILE__, __LINE__, "Unable to locate view: " . $view_file);
		}	
	}

	/**
	 * Creates the default view based off the uri i.e. controller/action -> controller/action.phtml
	 * @param string - current uri
	 */
	private function _setDefaultView($uri)
	{
		$paths = explode('/', $uri);
		
		if (isset($paths[0]) && trim($paths[0])) {
			if (isset($paths[1]) && trim($paths[1])) {
				$this->_default_view = strtolower($paths[0] . "/" . $paths[1] . ".phtml");
			} else {
				$this->_default_view = strtolower($paths[0] . "/index.phtml");
			}
		}
	}

	/**
	 * Creates all the head script includes
	 * @return string
	 */
	private function _headScripts()
	{
		$scripts = '';

		foreach ($this->_scripts as $src) {
			$scripts .= '<script type="text/javascript" src="'.$src.'"></script>';
		}

		return $scripts;
	}
}	

?>


<?php
   
namespace utilities;

/**
 * This class is responsible for turning the .ini config into a useable object
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage utilities  
 * @copyright  jessesnet.com
 * @version    1.0
 * @link       
 * @todo 	   Update so development inherits production values if none exist
 */

class Config
{
	/**
	 * @var stdClass
	 */
	private static $_Data = null;

	/**
	 * Creates the config data object
	 * @param array - of config data
	 */
	public static function setConfigData($config_data)
	{	
		self::$_Data = new \stdClass();

		foreach ($config_data as $key => $value) {

			list($obj,$index) = explode('.',$key);

			if (!isset(self::$_Data->$obj)) {
				self::$_Data->$obj = new \stdClass();
			}

			self::$_Data->$obj->$index = $value; 
		}
	}

	/**
	 * @return stdObject - config data
	 */
	public static function getData()
	{
		return self::$_Data;
	}
}
       
?>
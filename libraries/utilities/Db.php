<?php

namespace utilities;
use exceptions\DbException;
use \PDO;

/**
 * This class is responsible for interacting with the database
 * A wrapper class for PHP PDO
 *
 * @author     Jesse Cascio
 * @package    SwiftMVC
 * @subpackage utilities  
 * @copyright  jessesnet.com
 * @todo 	   Look into extending PDO
 */

class Db
{
	//static instances are set for the lifetime of the script

	/**
	 * @var Db
	 */
	private static $dbClass = null;

	/**
	 * @var PDO
	 */
	private static $dbConnection = null;

	/**
	 * @var bool - strict mode terminates application of sql error
	 */
	public static $strict = FALSE;
	
	/**
	 * @var string - connection data
	 */
	private static $dsn = '';

	/**
	 * @var string - connection data
	 */
	private static $user = '';

	/**
	 * @var string - connection data
	 */
	private static $password = '';

	/**
	 * @var bool - if connection exists
	 */
	private static $_is_connected = FALSE;

	/**
	 * @var string
	 */
	private static $error_message = '';

	/**
	 * Singleton cannot be instantiated
	 */
	private function __construct()
	{	
		self::$_is_connected = FALSE;

		//defaults to localhost
		$dsn = isset(self::$dsn) ? self::$dsn : "mysql:host=127.0.0.1";
		$user = isset(self::$user) ? self::$user : "root";
		$password = isset(self::$password) ? self::$password : "";
		
		try {

			// connect to db
			self::$dbConnection = new PDO($dsn, $user, $password);
			self::$dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
			self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			self::$_is_connected = TRUE;

		} catch(\PDOException $e) {
			 self::$error_message = $e->getMessage();
		}
	}
	
	/**
	 * Prevents cloning of this class
	 */
	public function __clone(){}

	/**
	 * Creates db objects
	 * @param string - dsn to connect with
	 * @param string - user to connect with
	 * @param string - password to connect with
	 * @return bool - if connection was successful
	 */
	public static function connect($dsn = null, $user = null, $password = null)
	{
		// if db object has not been instantiated
		if (!self::$dbClass) {
			self::$dsn = $dsn;
			self::$user = $user;
			self::$password = $password;
			self::$dbClass = new Db();
		}

		return self::$_is_connected;
	}

	/**
	 * Returns the PDO object if caller needs access to it
	 * @param string - dsn to connect with
	 * @param string - user to connect with
	 * @param string - password to connect with
	 * @return PDO
	 */
	public static function getInstance($dsn = null, $user = null, $password = null)
	{
		self::connect($dsn,$user,$password);
        return self::$dbConnection;
	}
	
	/**
	 * Deletes db objects
	 */
	public static function delete()
	{
		unset(self::$dbConnection);
		unset(self::$dbClass);
	}
	
	/**
	 * If a connection currently exists
	 * @return bool
	 */
	public static function isConnected()
	{
		return isset(self::$dbConnection) && self::$_is_connected ? TRUE : FALSE;
	}

	/**
	 * @return string
	 */
	public static function getErrorMessage()
	{
		return self::$error_message;
	}

	/**
	 * Runs a single sql query, must be in valid PDO form
	 *   $sql = SELECT * FROM table WHERE field=:value
	 *   $data[':value'] = 'true value'
	 * @param string - pdo sql statement
	 * @param array - pdo array of data
	 * @param bool - whether to throw exception on error
	 * @return array - result set
	 * @throws DbException - error querying table
	 */
	public static function query($sql, $data = array(), $strict = FALSE)
	{
		// make sure we have a connection
		self::connect();
		$success = FALSE; 

		try {
			$sql_stmt = self::$dbConnection->prepare($sql);
			$success = $sql_stmt->execute($data);
		} catch(\PDOException $e) {
			self::$error_message = $e->getMessage();
		}
		
		if (!$success) {
			$error_message  = PHP_EOL . "SQL Error: " . self::$error_message . PHP_EOL ;
			$error_message .= "SQL Stmt: " . $sql;
			//if we want to terminate on unsuccessful SQL
			if (self::$strict || $strict) {
				throw new DbException(__FILE__, __LINE__, $error_message);
			} else {
				// log all failed SQL calls
				try {
					// all logging is handled by exceptions
					throw new DbException(__FILE__, __LINE__, $error_message);
				} catch (DbException $e) {}
			}
		}
		
		return $success ? $sql_stmt->fetchAll() : array();
	}
	
	/**
	 * Inserts an array of data to a given db.table
	 * @param string - db.table
	 * @param array - pdo array of data, key must be column name, value is value
	 * @param bool - whether to throw exception on error
	 */
	public static function insertArray($table, $data = array(), $strict = FALSE)
	{
		$sql = self::_buildInsert($table, $data);
		self::_prepData($data);		
		self::query($sql, $data, $strict);
	}
	
	/**
	 * Builds a PDO SQL INSERT statement based on an array of values i.e. userName=:userName
	 * @param string - db.table
	 * @param array - pdo array of data
	 * @return string - pdo select statment
	 */
	private static function _buildInsert($table, $data = array())
	{

		if (is_array($data) && count($data) > 0 ) {
			$sql = "INSERT INTO " . $table . " SET";
			foreach ($data as $key => $value) {
				$sql .= " ".$key."=:".$key.",";
			}
			return substr($sql,0,-1);
		}

		return '';		
	}
	
	/**
	 * Updates a data array preparing it for PDO execution by appending : to the keys i.e. array(':userName'=>$userName);
	 * @param array - insert data
	 */
	private static function _prepData(&$data)
	{
		$temp_data = array();

		foreach ($data as $key => $value) {
			$temp_data[':'.$key] = $value;
		}

		$data = $temp_data;
	}
}

?>
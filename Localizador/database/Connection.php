<?php
/**
* Author: @Data-excepcion-dot-com
*/
class Connection
{
    /**
     * Host Name
     * @var string
     */
    private $_hostName = "localhost";
    /**
     * DataBase Name
     * @var string
     */
    private $_dataBaseName = "buscadordb";
    /**
     * User Name
     * @var string
     */
    private $_userName = "root";
    /**
     * Password
     * @var string
     */
    private $_password = "";
    /**
     * Connection
     * @var Object
     */
    private static $_connection;
    /**
     * Result Set
     * @var Object
     */
    private $_resultSet;
	
	/**
	* Create a new Connection
	*/
	public function __construct()
	{
		// Use Singleton Pattern.
		if(!isset(self::$_connection))
		{
			// Path To Connection
			$path = 'mysql:host='.$this->_hostName.';dbname='.$this->_dataBaseName.';charset=utf8';
			// Search for Exception
			try{
				// Create Connection
				self::$_connection = new PDO($path, $this->_userName, $this->_password);
				// Set Error's Mode
				self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e)
			{
				// Print Error
				echo "Error: " . $e->getMessage();
			}	
		}
	}
	/**
	* Return an existence Connection
	*/
	protected function getConnection(){
		return self::$_connection;
	}
	/**
	* Destructor
	*/
	function __destruct() {
		
	}
}
?>
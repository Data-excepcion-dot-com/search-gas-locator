<?php
/**
* Author: @Data-excepcion-dot-com
*/

// Include Connection's File
include_once "../database/Connection.php";

class Municipio extends Connection
{
    /**
     * Connection's Instance
     * @var Object
     */
    private $_connectionInstance;
	/**
	* Constructor's Class.
	*/
	public function __construct()
	{
		// Create Connection's Constructor.
		$this->_connectionInstance = new Connection();
    }

	/**
	 * Function to get Municipios by State's Code.
	*/
	public function getMunicipioByCode($stateCode){
		// Response's Array
		$response = array();
		// Object's Array
		$objeto = array();
		// Set Success (Default)
		$objeto['success'] = false; 
		// Set Results (Default)
		$objeto['results'] = array();

		// Set & Get Connection
		$conn = Connection::getConnection();

		// Is there some error?
		try
		{
			// Query's Result
			$result = null;
			// Initialize SQL Statement
			$sql = "";
			
			// Set Query
			$sql = "SELECT c_munpio, nombre_munpio
                    FROM tabla_municipio
					WHERE c_estado = ?
					ORDER BY nombre_munpio";
			// Set Query into Connection's Object
			$pstm = $conn->prepare($sql);
			// Set Param (Code's State)
			$pstm->bindParam(1, $stateCode, PDO::PARAM_INT);
			// Execute Query
			$pstm->execute();
			// Set & Get Query's Result
			$result = $pstm->fetchAll();
			// Check Query's Result
			if(count($result) > 0 )
			{
				// Collection's Array
				$collection = array();
				// Iterate Resulset
				foreach($result as $key => $row)
				{
                    // Set Temporal's Array
					$obj = array();
					// Add Values ...
					$obj['val'] = $row["c_munpio"];
                    $obj['text'] = $row["nombre_munpio"];
                    // Add Object to Array
					array_push($collection, $obj);
				}
				// Set Success
				$objeto['success'] = true;
				// Set Result into Object
				$objeto['results'] = $collection;
				// Add Object to Response
				array_push($response, $objeto);
			}
			else{
				// Add Default's Values to Response
				array_push($response, $objeto);      
			}
		}
		catch(Exception $e){
			// Add Default's Values to Response
			array_push($response, $objeto);
		}
		// Return Municipio's Data
		return $response[0];
	}

	/**
	 * Destructor
	*/
	function __destruct(){
		// Destroy Connection's Instance.
	    unset($this->_connectionInstance);
	}
}
?>
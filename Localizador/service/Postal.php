<?php
/**
* Author: @Data-excepcion-dot-com
*/

// Include Connection's File
include_once "../database/Connection.php";

class Postal extends Connection
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
	 * Function to get Postal's Code
      * @param codeEstado
      * @param codeMunicipio
	*/
	protected function getPostalCode($codeEstado, $codeMun){
		// Query's Result
		$result = null;
		// Collection's Array
		$collection = array();
		
		// Is there some error?
		try
		{
			// Set & Get Connection
			$conn = Connection::getConnection();
			// Set Query
			$sql = "SELECT c_postal
                    FROM tabla_codigo_postal
                    WHERE c_estado = ? AND c_munpio = ?
                    ORDER BY c_postal ASC";
			// Set Query into Connection's Object
			$pstm = $conn->prepare($sql);
			// Set Param (Code's State)
            $pstm->bindParam(1, $codeEstado, PDO::PARAM_INT);
			// Set Param (Code's Municipio)
			$pstm->bindParam(2, $codeMun, PDO::PARAM_INT);
			// Execute Query
			$pstm->execute();
			// Set & Get Query's Result
			$result = $pstm->fetchAll();
			// Check Query's Result
			if(count($result) > 0 )
			{
				// Iterate Resulset
				foreach($result as $key => $row)
				{
                    // Set Temporal's Array
					$obj = array();
                    $obj['postal_code'] = $row["c_postal"];
                    // Add Object to Array
                    array_push($collection, $obj);
				}	
			}
		}
		catch(Exception $e){
			$result = null;
		}
		// Return Codes's Data
		return $collection;
	}

	/**
	 * Function to get Name's State & Name's Municipio
      * @param codeEstado
      * @param codeMunicipio
	*/
	protected function getStateMunNames($codeEstado, $codeMun){
		// Query's Result
		$result = null;
		// Name's Array
		$names = array();

		// Is there some error?
		try
		{
			// Set & Get Connection
			$conn = Connection::getConnection();

			// Set Query
			$sql = "SELECT A.nombre_estado, B.nombre_munpio
					FROM tabla_estado A
					LEFT JOIN tabla_municipio B ON A.c_estado = B.c_estado
					WHERE A.c_estado = ? AND B.c_munpio = ?";
			// Set Query into Connection's Object
			$pstm = $conn->prepare($sql);
			// Set Param (Code's State)
            $pstm->bindParam(1, $codeEstado, PDO::PARAM_INT);
			// Set Param (Code's Municipio)
			$pstm->bindParam(2, $codeMun, PDO::PARAM_INT);
			// Execute Query
			$pstm->execute();
			// Set & Get Query's Result
			$result = $pstm->fetchAll();
			// Check Query's Result
			if(count($result) > 0 )
			{
				// Iterate Resulset
				foreach($result as $key => $row)
				{
					// Object's Array
					$objeto = array();
					// Set State
					$objeto['estado'] = $row["nombre_estado"];
					// Set Municipio
					$objeto['municipio'] = $row["nombre_munpio"];
					// Add Object to Array
					array_push($names, $objeto);
				}	
			}
		}
		catch(Exception $e){
			$result = null;
		}
		// Return Data
		return $names;
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
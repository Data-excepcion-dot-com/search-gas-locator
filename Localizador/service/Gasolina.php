<?php
/**
* Author: @Data-excepcion-dot-com
*/

// Include Postal's File
include_once "Postal.php";

class Gasolina extends Postal
{
    /**
     * Class's Instance
     * @var Object
     */
    private $_instance;
	/**
	* Constructor's Class.
	*/
	public function __construct()
	{
		// Create Class's Constructor.
		$this->_instance = new Postal();
    }

	/**
	 * Function to get Gasoline's Stations
      * @param codeEstado
      * @param codeMunicipio
	*/
	public function getGasolineStation($codeEstado, $codeMun, $order){
		// Response's Array
		$response = array();
		// Object's Array
		$objeto = array();
		// Set Success (Default)
		$objeto['success'] = false; 
		// Set Results (Default)
		$objeto['results'] = array();

        // Set URL's Base
		$base = "https://api.datos.gob.mx/v2/precio.gasolina.publico?";
		// Set Sort
		$sort = intval($order) !== 0 ? "sort=regular&" : "";

		// Set & Get Postal Codes
        $codes = Postal::getPostalCode($codeEstado, $codeMun);
        // Convert to Params
		$params = count($codes) == 0 ? "" : "codigopostal=".$this->arrToStr($codes,'postal_code');

        // Set URL
		$url = $base.$sort.$params;
		
		// Is there some error?
		try
		{
            // Init Curl
            $handle = curl_init();
            // Set URL
			curl_setopt($handle, CURLOPT_URL, $url);
			// Set Header
			curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
            // Set the result output to be a string.
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            // Receive's Response
			$result = curl_exec($handle);
			// Get Status's (HTTP)
			$status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			// It's OK?
			if($status == 200){
				// Decode's Response
				$plainObj = json_decode($result);

				// Set & Get Coincidences
				$total = $plainObj->pagination->total;

				// Set & Get Names
				$names = Postal::getStateMunNames($codeEstado, $codeMun);

				// Did it find something?
				if($total > 0 && count($names) > 0){
					// Set & Get Results
					$results = $plainObj->results;
					
					// Add New Attributes
					foreach($results as $ind => $obj){
						// Add Attr (Estado)
						$obj->estado = $names[0]['estado'];
						// Add Attr (Municipio)
						$obj->municipio = $names[0]['municipio'];
					}
					// Set Success
					$objeto['success'] = true;
					// Set Result into Object
					$objeto['results'] = $results;
					// Add Object to Response
					array_push($response, $objeto);
				}
				// Nothing was found
				else{
					// Add Default's Values to Response
					array_push($response, $objeto);
				}
			}
			// Nothin
			else{
				// Add Default's Values to Response
				array_push($response, $objeto);
			}
		}
		catch(Exception $e){
			// Add Default's Values to Response
			array_push($response, $objeto);
		}
		// Return Gasoline's Data
		return $response[0];
    }

	/**
	 * Function to convert an array into a String
	 * Separated by Commas (element0, element1, ... , elementN).
	 * ('firstElement','secondElement',...,'lastElement')
	*/
	function arrToStr($ar, $name){
		// Declare Empty String
		$str = "";
		// Set & Get Array's Size
		$size = count($ar) - 1;
		// Iterate over Array
		foreach($ar as $key => $obj)
		{
			// Set & Get New Format.
			$str .= $key === $size ? $obj[$name] : $obj[$name].",";
		}
		// Return String
		return $str;
	}

	/**
	 * Destructor
	*/
	function __destruct(){
		// Destroy Class's Instance.
	    unset($this->_instance);
	}
}
?>
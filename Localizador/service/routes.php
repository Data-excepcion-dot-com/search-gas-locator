<?php 
/**
* Author: @Data-excepcion-dot-com
*/
// Archivos Requeridos
require 'Municipio.php';
require 'Gasolina.php';

/* Servicio getMunicipios
  ** Para obtener el nombre de los municipios
*/
Flight::route('POST /getMunicipios/@id:[0-9]{1,2}', function($id){
  // Create Response's Constructor.
	$_instance = new Municipio();
	// Get & Set Names (Municipios)
  $data = $_instance->getMunicipioByCode($id);
  // Send Response
  Flight::json($data);
});

/* Servicio getGasolineData
  * Para obtener información de las gasolineras
  * Correspondientes a un municipio
*/
Flight::route('POST /getGasolineData/@idEstado:[0-9]{1,2}/@idMunpio:[0-9]{1,3}/@filter:[0-1]{1}', function($idEstado, $idMunpio,$filter){
  // Set & Get Token
  $token = Flight::request()->data['token'];
  // Create Response's Constructor.
	$_instance = new Gasolina();
	// Get & Set Connection's Instance.
  $response = $_instance->getGasolineStation($idEstado, $idMunpio, $filter);
  // Send Response
  Flight::json($response);

});
?>
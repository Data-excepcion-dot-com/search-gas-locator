/**
* Author: @Data-excepcion-dot-com
*/

/** Global Variables **/
// Map's Object
var map;
// Marker's Array
var markers = [];
// Global Response
var responseGbl = [];
// Mexico's LatLng
var centerOfMexico = {lat: 23.634501, lng: -102.55278399999997};

// Sender Function
function sendRequest(serviceName, params, funct, passed){
	// Set PathToResource
	var path = "service/"+serviceName;
	// AJAX Request
	$.ajax({
		url: path,
		//data: params,
		type: "POST",
		dataType : "json",
		// If it's asynchronous (TRUE) or not synchronous (FALSE).
		async: true,
		timeout: 1000
	})
	.done(function(response){
		if(passed)
			return callSelfFunction([response], funct);
		else{
			return callSelfFunction([response, params], funct);
		}
	})
	.fail(function( xhr, status, errorThrown ) {
		alert("Server Internal Error.");
	});
}
// Self Calling Function
function callSelfFunction(parameters, callback){
	callback.apply(this, parameters);
}
// Fill up Municipio's Select
function setMunicipios(response){
	// Set Default's Option
	var defaulOption = '<option value="none" selected="selected" disabled="disabled">-- Selecciona Municipio --</option>';
	// Clear Previous Values
	$("#municipio").empty();
	// Set Default's
	$("#municipio").append(defaulOption);
	// Set Names in Select
	$.each(response.results, function (i, item) {
		$('#municipio').append($('<option>', { 
			value: item.val,
			text : item.text
		}));
	});
}
// Method to set Map's Configuration.
function initMap() {
	// Create a map object and specify the DOM element for display.
	map = new google.maps.Map(document.getElementById('map'), {
		// Center Point
		center: centerOfMexico,
		// Zoom
		zoom: 5,
		// Type ID Map
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		// Scroll Wheel
		scrollwheel: true,
		// Scale Control
		scaleControl: true,
		// Type Control
		mapTypeControl: true,
		mapTypeControlOptions: {
			mapTypeIds: [google.maps.MapTypeId.SATELLITE, google.maps.MapTypeId.TERRAIN, google.maps.MapTypeId.HYBRID],
			style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
			position: google.maps.ControlPosition.TOP_CENTER
		},
		// Zoom Control
		zoomControl: true,
		zoomControlOptions: {
			position: google.maps.ControlPosition.LEFT_CENTER
		},
		// Street View
		streetViewControl: true,
		streetViewControlOptions: {
			position: google.maps.ControlPosition.LEFT_BOTTOM
		}
	});
}
// Response's Interpreter
function interpreter(results){
	try{
		// Get & Set Sites
		responseGbl = results;
		// Making dynamic memory
		setPointsOnMap(responseGbl);
	}
	catch(exception)
	{
		alert('Intenta más tarde.');
	}
}
// Method to fill up Map with Site Information.
function setPointsOnMap(sites) {
	// Delete Markers on Map
	deleteMarkers();
	// Create Object (InfoWindow)
	var infoWindow = new google.maps.InfoWindow();
	// Init Marker 
	var marker;
	// Rediret to Market
	map.panTo(new google.maps.LatLng(sites[0].latitude, sites[0].longitude));
	// Iterate over Results ...
	$.each(sites, function (key, site){
		// Creating Marker.
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(site.latitude, site.longitude),
			map: map
		});
		// Assign marker to Array
		markers[key] = marker;
		// Add Listener (MouseOver)
		google.maps.event.addListener(marker, 'mouseover', (function(key) {
			return function() {
				infoWindow.setContent('<p style="margin: 0px; padding: 0px;"><b>Estación: </b>'+site.razonsocial+'</br>'+
				'<b>Colonia: </b>'+site.colonia+'</br>'+
				'<b>Calle: </b>'+site.calle+'</br>'+
				'<b>Regular: </b>'+site.regular+'</br>'+
				'<b>Premium: </b>'+site.premium+'</br>'+
				'<b>Disel: </b>'+site.dieasel+'</br>'+
				'<b>RFC: </b>'+site.rfc+'</br>');
				infoWindow.open(map, markers[key]);
			}
		})(key));
		// Add Listener (MouseOut)
		google.maps.event.addListener(marker, 'mouseout', (function() {
			return function() {
				infoWindow.close();
			}
		})(marker));
	});
	// Set Zoom
	map.setZoom(9);
	// Show Table
	showResults(sites);
}
// Show Results in table
function showResults(results){
	// Get Access to Table
	var table = $('#tblResults');
	// Set Titles of Thead
	var titles = ['ID','Gasolinera','Dirección','Precios','RFC','Permiso ID','Número de Permiso','Ubicación'];
	// Table's Head
	var thead = $("<thead />");
	// Table's Body
	var tbody = $("<tbody />");
	// Init String
	var cadena = '';
	// Iterate over Array
	$.each(results, function (ind, obj){
		var permiso = 0;
		var i = 0;
		// Init TR
		cadena = '<tr>';
		// Iterate over Object
		$.each(obj, function (key, value){
			// Set ID
			if(i == 0){
				cadena +='<th scope="row">'+value+'</th>';
				cadena += '<td>'+obj.razonsocial+'</td>';
			}
			// Get Permiso ID
			else if(i == 8){
				permiso = value;
				return false;
			}
			// Increment
			i++;
		});
		// Set & Get Direction
		var dir = obj.calle+' '+obj.colonia+"</br>C.P: "+obj.codigopostal+"</br>"+obj.municipio;
		// Set & Get Prices
		var prices = 'Regular: '+obj.regular+' Premium: '+obj.premium+' Dieasel: '+obj.dieasel;
		// Set & Get Link to Google Maps
		var linkMap = '<a href="https://www.google.com/maps/place/'+obj.latitude+','+obj.longitude+'" target="_blank">Ver Mapa</a>';
		// Build ...
		cadena +='<td>'+dir+'</td>';
		cadena +='<td>'+prices+'</td>';
		cadena +='<td>'+obj.rfc+'</td>';
		cadena +='<td>'+permiso+'</td>';
		cadena +='<td>'+obj.numeropermiso+'</td>';
		cadena +='<td>'+linkMap+'</td></tr>';
		// Append TR
		tbody.append(cadena);
	});
	// Is it new?
	if ( table.children().length == 0 ){
		// Set Caption
		table.append('<caption>Lista de Resultados</caption>');
		// Clean String
		cadena = '';
		// Creates Header's Row
		$.each(titles, function (key, value){
			cadena += '<th scope="col">'+value+'</th>';
		});
		// Append Row to Table's Header
		thead.append('<tr>'+cadena+'</tr>');
		// Append Header & Body to Table
		table.append(thead).append(tbody);		
	}
	else{
		// Reset Text's Caption
		table.find("caption").text("Lista de Resultados");
		// Replace Body
		table.find("tbody").replaceWith(tbody);
	}
}
// Set map on all markers in the array
function setMapOnAll(map) {
	$.each(markers, function (key, site){
	    markers[key].setMap(map);
	});
}
// Remove markers from the map, but keeps them in the array
function clearMarkers() {
  setMapOnAll(null);
}
// Shows any markers currently in the array
function showMarkers() {
  setMapOnAll(map);
}
// Delete markers in the array by removing references to them
function deleteMarkers() {
  clearMarkers();
  markers = [];
}
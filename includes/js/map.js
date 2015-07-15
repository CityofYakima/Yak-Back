// map.js - 08/31/2012 
//          GIS functionality for the citizen request project

//initialize dojo
dojo.require("dijit.dijit"); // optimize: load dijit layer
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("esri.map");
dojo.require("esri.layers.FeatureLayer");
dojo.require("esri.dijit.PopupMobile");
dojo.require("esri.dijit.Popup");
dojo.require("esri.geometry");
dojo.require("esri.toolbars.edit");
dojo.require("esri.tasks.locator");

dojo.addOnLoad(init);

// map variables
var map;
var popup;
var popupTemplate;
var editToolbar;
var locator;
var gcr;  // locator last result
var reqdata;
var reqLayer;
var evtID;
var userID;
var requestType;
var initExtent;
var yakimaCL;
var pageAddrReturn;

function init() {
	//hookup jquery
	$(document).ready(jQueryReady);
}

function jQueryReady() {
	$(document).trigger('pageshow');
	$.fn.placeholder();
	
	//resize the map content section
	$('#mapcontent').height($(window).height());
	
	//onorientationchange doesn't always fire in a timely manner in Android so check for both orientationchange and resize
	var supportsOrientationChange = "onorientationchange" in window,
	orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
	
	window.addEventListener(orientationEvent, function () {
		orientationChanged();
	}, false);
	
	// check to see if there is a specific EventID or UserID being requested
	evtID = GetURLParameter('eventid');
	userID = GetURLParameter('userid');
	// create a popup div
	popup = new esri.dijit.PopupMobile({
		fillSymbol: new esri.symbol.SimpleFillSymbol(esri.symbol.SimpleFillSymbol.STYLE_SOLID, new esri.symbol.SimpleLineSymbol(esri.symbol.SimpleLineSymbol.STYLE_SOLID, new dojo.Color([255,0,0]), 2), new dojo.Color([255,255,0,0.25]))
	},dojo.create("div"));
   //define a popup template
   var popupTemplate = new esri.dijit.PopupTemplate({
	  title: "{Name}",
	  fieldInfos: [
	  {fieldName: "id", visible: true, label:"Request ID:"},
	  {fieldName: "Name", visible: true, label:"Type:"},
	  {fieldName: "description", visible: true, label:"Description:"},
	  {fieldName: "dateOpened", visible: true, label:"Date Opened:"},
	  {fieldName: "dateAssigned", visible: true, label:"Date Assigned:"},
	  {fieldName: "dateClosed", visible: true, label:"Date Closed:"},
	  {fieldName: "status", visible: true, label: "Status"},
	  {fieldName: "latitude", visible: true, label:"Latitude:"},
	  {fieldName: "longitude", visible: true, label:"Longitude:"}
	  ],
		mediaInfos: [{
			"title": "{Name} - {location}",
			"caption": "{description}",
			"type": "image",
			"value": {"sourceURL": "/yak-back/uimages/{photo}"}
		}]

	});
	
	//define an initial extent (Yakima)
	initExtent = new esri.geometry.Extent({"xmin":-13433193,"ymin":5867988,"xmax":-13409264,"ymax":5881438,"spatialReference":{"wkid":102100}});
	map = new esri.Map("map", {extent : initExtent, infoWindow:popup});
	var basemap = new esri.layers.ArcGISTiledMapServiceLayer("http://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer");	
	map.addLayer(basemap);
	dojo.place(popup.domNode,map.root);
	dojo.addClass(map.infoWindow.domNode, "myTheme");
	
	// add graphic layer for display citizen requests
	var layerDefinition = {
		"geometryType": "esriGeometryPoint",
		"objectIdField": "",
		"fields": [
			{"name": "id", "type": "esriFieldTypeInteger", "alias": "ID"},
			{"name": "location", "type": "esriFieldTypeString", "alias": "Location"},
			{"name": "photo", "type": "esriFieldTypeString", "alias": "Photo"},
			{"name": "description", "type": "esriFieldTypeString", "alias": "Description"},
			{"name": "dateOpened", "type": "esriFieldTypeString", "alias": "Date Opened"},
			{"name": "dateClosed", "type": "esriFieldTypeString", "alias": "Date Closed"},
			{"name": "status", "type": "esriFieldTypeString", "alias": "Status"},
			{"name": "latitude", "type": "esriFieldTypeString", "alias": "Latitude"},
			{"name": "longitude", "type": "esriFieldTypeString", "alias": "Longitude"},
			{"name": "assignedTo", "type": "esriFieldTypeString", "alias": "Assigned To"},
			{"name": "dateAssigned", "type": "esriFieldTypeString", "alias": "Date Assigned"},
			{"name": "Name", "type": "esriFieldTypeString", "alias": "Request Type"},
			{"name": "typeID", "type": "esriFieldTypeString", "alias": "Type ID"}
		]
	} 
	var featureCollection = {
		"layerDefinition": layerDefinition,
		"featureSet": {
		"features": [],
		"geometryType": "esriGeometryPoint"
		}
	};

	reqLayer = new esri.layers.FeatureLayer(featureCollection, {id: 'reqLayer', infoTemplate: popupTemplate, mode: esri.layers.FeatureLayer.MODE_SNAPSHOT, outFields:["*"]});
	dojo.connect(reqLayer, "onClick", function(evt) {
		//map.infoWindow.setFeatures([evt.graphic]);
		var query = new esri.tasks.Query();
		query.geometry = pointToExtent(map,evt.graphic.geometry,10);
		var selRequests = reqLayer.selectFeatures(query);	
		map.infoWindow.setFeatures([selRequests]);
		map.infoWindow.show(evt.graphic.geometry);
	});
	map.addLayer(reqLayer);
	
	// Add City Boundary layer.
	yakimaCL = new esri.layers.FeatureLayer("http://gis.yakimawa.gov/arcgis101/rest/services/General/YakimaLayers/MapServer/9", {
          mode: esri.layers.FeatureLayer.MODE_SNAPSHOT,
          outFields: ["*"],
		  opacity: .4
        });
	yakimaCL.setDefinitionExpression("IO = 'I'");
	map.addLayer(yakimaCL);
	
	$('#mapPage').bind('pageshow', function(event, ui) {
		// this is a total hack because IE will not respect the proper triggers for pageshow event.  It is supposed to wait until the page is shown
		// for the map to be displayed.
		if (navigator.userAgent.indexOf('MSIE') > 0) {
			var t1=setTimeout(function(){stupidIEzoom()},500);
		} else {
			resizeMap();
		}
		pageAddrReturn = 'map';	
	});
	
	$('#reportForm').bind('pageshow', function(event, ui) {
		pageAddrReturn = 'form';	
	});

	//after map loads request citizen request data	
	dojo.connect(map, "onLoad", function() {
		
		// check to see if there is a specific EventID being requested
		evtID = GetURLParameter('eventid');
		requestType = GetURLParameter('request');
		if (requestType == 'recent') {
			var url = '/apps/yak-back/data.php';
			if (evtID != -1) url = 'data.php?eventid=' + evtID;
			if (userID != -1) url = 'data.php?userid=' + userID;
			$.get(url, function(data) {
				reqdata = data;
				mapRequests();
			});
		} else {
			// change to the new report page
			$.mobile.changePage("#reportForm");
			$("#mapMessages").html("<center>Click on map or<br>Use the GPS or address tools above.");		}
	});
	
	// the edit toolbar allows the user to move the request point to a specific location on the map
	editToolbar = new esri.toolbars.Edit(map);	
	
	//deactivate the toolbar when you click outside a graphic
	dojo.connect(map, "onClick", function(evt) {
		editToolbar.deactivate();
		dropBluePin(evt);
	});
	
	// add address locator for reverse geolocating and address matching.
	locator = new esri.tasks.Locator("YOUR GEOCODE SERVICE"); // http://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer
	locator.outSpatialReference = new esri.SpatialReference(4326);
	//Create geocoder  
	geocoder = new esri.tasks.Locator("YOUR GEOCODE SERVICE");
	geocoder.outSpatialReference = new esri.SpatialReference(4326);
}


function pointToExtent(map, point, toleranceInPixel) {
	var pixelWidth = map.extent.getWidth() / map.width;
	var toleraceInMapCoords = toleranceInPixel * pixelWidth;
	return new esri.geometry.Extent( point.x - toleraceInMapCoords,
	point.y - toleraceInMapCoords,
	point.x + toleraceInMapCoords,
	point.y + toleraceInMapCoords,
	map.spatialReference );                          
}

  
function mapRequests() {
	var features = [];
	var pt;
	for (var i=0; i < reqdata.length; i++) {
		var att = reqdata[i];
		pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point(att.longitude, att.latitude));
		var reqSymbol = new esri.symbol.PictureMarkerSymbol({
			"angle": 0,
			"xoffset": 0,
			"yoffset": 0,
			"url": "/apps/yak-back/images/pin_blue.png",
			"contentType": "image/png",
			"width": 24,
			"height": 24
			});
		if (att.dateAssigned != null) {
			reqSymbol.url = "/apps/yak-back/images/pin_green.png";
		}
		if (att.dateClosed != null) {
			reqSymbol.url = "/apps/yak-back/images/pin_red.png";
		}
		var gra = new esri.Graphic(pt, reqSymbol, att);
		features.push(gra);
	}
	reqLayer.applyEdits(features,null,null);
	// set map extent
	if (evtID == -1) {
		map.setExtent(esri.graphicsExtent(reqLayer.graphics).expand(1.5));
	} else {
		map.centerAndZoom(pt,17);
	}
	// label the type of request that was made
	if (evtID != -1) {
		$("#mapMessages").html("Mapped Event ID: " + evtID);
	} else if (userID != -1) {
		$("#mapMessages").html("Mapped " + reqdata.length + " of your requests");
	} else {
		$("#mapMessages").html("Mapped " + reqdata.length + " recent requests");
	}
		
}

function GetURLParameter(sParam) {
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++) 
	{
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] == sParam) 
		{
			return sParameterName[1];
		}
	}
	return -1;
}	

function orientationChanged() {
	resizeMap();
}

function resizeMap() {
	if(map) {
		$('#map').css("height", screen.height - 80);
		$('#map').css("width","auto");
		
		map.reposition();
		map.resize();
	}
}

function dropBluePin(evt) {
		//clear existing graphics
	map.graphics.clear();
	//$.mobile.pageLoading(true); //true hides the dialog
	var pt = evt.mapPoint;
	var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
	if (isYakima == false) {
		$("#mapMessages").html("<center>Drag blue dot or Click Report<br><font color='#990000'>Device location is not in Yakima<br>Drag the blue pin to right spot in Yakima.</font></center>");
	}
	map.centerAndZoom(pt, 17);
	var blueDot = new esri.symbol.PictureMarkerSymbol({
		"angle": 0,
		"xoffset": 0,
		"yoffset": 0,
		"url": "images/bluedot.png",
		"contentType": "image/png",
		"width": 40,
		"height": 40
		});
	var gra = new esri.Graphic(pt, blueDot);
	map.graphics.add(gra);
	
	editToolbar.activate(esri.toolbars.Edit.MOVE | esri.toolbars.Edit.SCALE | esri.toolbars.Edit.ROTATE | esri.toolbars.Edit.EDIT_VERTICES, gra);

	dojo.connect(map.graphics, "onClick", function(evt) {
		dojo.stopEvent(evt);
		editToolbar.activate(esri.toolbars.Edit.MOVE | esri.toolbars.Edit.SCALE | esri.toolbars.Edit.ROTATE | esri.toolbars.Edit.EDIT_VERTICES, evt.graphic);
	});
	dojo.connect(editToolbar, "onGraphicMoveStart", function(graphic) {
		var blueDot = new esri.symbol.PictureMarkerSymbol({
			"angle": 0,
			"xoffset": 0,
			"yoffset": 0,
			"url": "images/bluedot.png",
			"contentType": "image/png",
			"width": 80,
			"height": 80
			});
		graphic.setSymbol(blueDot);
	});
	dojo.connect(editToolbar, "onGraphicMoveStop", function(graphic) {
		var blueDot = new esri.symbol.PictureMarkerSymbol({
			"angle": 0,
			"xoffset": 0,
			"yoffset": 0,
			"url": "images/bluedot.png",
			"contentType": "image/png",
			"width": 40,
			"height": 40
			});
		graphic.setSymbol(blueDot);
		var pt = esri.geometry.webMercatorToGeographic(graphic.geometry);
		$("#mapMessages").html("lat:" + pt.y.toFixed(6) + " lon:" + pt.x.toFixed(6));
		//console.log("lat:" + pt.y.toFixed(6) + " lon:" + pt.x.toFixed(6));
		locator.locationToAddress(pt, 100, addressForPoint);
	});
	
	// once the point has been found by the device GPS, find the approximate address
	locator.locationToAddress(pt, 100, addressForPoint);
}

function getLocation() {
	if (navigator.geolocation) {
		// we are just getting the device position on demand
		navigator.geolocation.getCurrentPosition(zoomToLocation, locationError);
	}
}
	
function locationError(error) {
	switch (error.code) {
		case error.PERMISSION_DENIED:
			console.log("Location not provided");
			checkGPSmessage();		
			break;
		case error.POSITION_UNAVAILABLE:
			console.log("Current location not available");
			checkGPSmessage();		
			break;
		case error.TIMEOUT:
			console.log("Timeout");
			break;
		default:
			console.log("unknown error");
			break;
	}
}

function checkGPSmessage() {
	var locationMessage = window.localStorage.getItem("locationMessage");
	if (locationMessage != "NO!") $.mobile.changePage("#locationServicesPage", { transition: "fade"} );
}

function noGPSmessage() {
	window.localStorage.setItem("locationMessage", "NO!");
}

function resetGPSmessage() {
	window.localStorage.removeItem("locationMessage");
}

function zoomToLocation(l) {
	// chech the quality of the device location.
	var accuracy = l.coords.accuracy;  // in meters
	if (accuracy > 500) {
		$("#deviceErrorHeader").html("Device GPS Quality");
		$("#deviceErrorMessage").html("Your devices GPS has low quality accuracy (" + accuracy + " meters).  You may want to move the blue pin to a suitable location or enter an address.");
		$.mobile.changePage("#deviceErrorPage");
	}
	$("#locMessage").html("GPS Accuracy: " + l.coords.accuracy + "m");
	//clear existing graphics
	map.graphics.clear();
	//$.mobile.pageLoading(true); //true hides the dialog
	var pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point(l.coords.longitude, l.coords.latitude));
	var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
	if (isYakima == false) {
		$("#mapMessages").html("<center>Drag blue dot or Click Report<br><font color='#990000'>Device location is not in Yakima<br>Drag the blue pin to right spot in Yakima.</font></center>");
	}
	map.centerAndZoom(pt, 17);
	var blueDot = new esri.symbol.PictureMarkerSymbol({
		"angle": 0,
		"xoffset": 0,
		"yoffset": 0,
		"url": "images/bluedot.png",
		"contentType": "image/png",
		"width": 40,
		"height": 40
		});
	var gra = new esri.Graphic(pt, blueDot);
	map.graphics.add(gra);
	
	editToolbar.activate(esri.toolbars.Edit.MOVE | esri.toolbars.Edit.SCALE | esri.toolbars.Edit.ROTATE | esri.toolbars.Edit.EDIT_VERTICES, gra);

	dojo.connect(map.graphics, "onClick", function(evt) {
		dojo.stopEvent(evt);
		editToolbar.activate(esri.toolbars.Edit.MOVE | esri.toolbars.Edit.SCALE | esri.toolbars.Edit.ROTATE | esri.toolbars.Edit.EDIT_VERTICES, evt.graphic);
	});
	dojo.connect(editToolbar, "onGraphicMoveStart", function(graphic) {
		var blueDot = new esri.symbol.PictureMarkerSymbol({
			"angle": 0,
			"xoffset": 0,
			"yoffset": 0,
			"url": "images/bluedot.png",
			"contentType": "image/png",
			"width": 80,
			"height": 80
			});
		graphic.setSymbol(blueDot);
	});
	dojo.connect(editToolbar, "onGraphicMoveStop", function(graphic) {
		var blueDot = new esri.symbol.PictureMarkerSymbol({
			"angle": 0,
			"xoffset": 0,
			"yoffset": 0,
			"url": "images/bluedot.png",
			"contentType": "image/png",
			"width": 40,
			"height": 40
			});
		graphic.setSymbol(blueDot);
		var pt = esri.geometry.webMercatorToGeographic(graphic.geometry);
		$("#mapMessages").html("lat:" + pt.y.toFixed(6) + " lon:" + pt.x.toFixed(6));
		locator.locationToAddress(pt, 100, addressForPoint);
	});
	
	// once the point has been found by the device GPS, find the approximate address
	var revGeoPT = new esri.geometry.Point(l.coords.longitude, l.coords.latitude);		
	locator.locationToAddress(revGeoPT, 100, addressForPoint);

}

function addressForPoint(candidate) { //RBJ
//console.log(candidate.address);

	if (candidate.address) {
		var cAddr = candidate.address.Street;
		if (cAddr == null) cAddr = "No Address Found!";
		$("#mapMessages").html("<center>Drag blue pin or Click Report<br>" + cAddr + "</center>");
		var pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point(candidate.location.x.toFixed(6),candidate.location.y.toFixed(6)));
		//console.log(pt);
		// Check to see if the point is in the Yakima City Limits
		var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
		//console.log(isYakima);
		if (isYakima == false) {
			$("#mapMessages").html("<center>Drag blue pin or Click Report<br>" + cAddr + "<br><font color='#990000'>This location is not in Yakima<br>Please contact the local jurisdiction</font></center>");
		}
		$("#latitude").val(candidate.location.y.toFixed(6));
		$("#longitude").val(candidate.location.x.toFixed(6));
		$("#location").html(candidate.address.Street);
		$("#locationaddr").val(candidate.address.Street);
	}
}

function zoomAll(){
	map.setExtent(initExtent);
}

// Address Locator for finding stops near an address
function searchAddresses() {
	var address = {"Street": $("#addrSearch").val()};
	if (address.Street != "") {
		var params = {address: address, outFields:["*"]};	
		geocoder.addressToLocations(params, buildAddressList, errAddress);
	} else {
		$('#addressList li').remove();
		selectedAddress = '';
	}
}

function buildAddressList(geocodeResults) {
	gcr = geocodeResults;
	$('#addressList li').remove();
	var titleLI = $('<li data-role="list-divider" data-inset="true" />');
	titleLI.append("<h3 id='addListHeader'>&nbsp;&nbsp;Address List</h3>");
	$('#addressList').append(titleLI);
	var addCount = 0;
	$.each(gcr, function(i, a) {
		// we only want to display addresses that are in Yakima.
		var pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point(a.location.x, a.location.y));
		// Check to see if the point is in the Yakima City Limits
		var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
		if (isYakima == true) {
			var li = $("<li />");
			if (a.attributes.Addr_type == "Address") {
				var content = "<a href='#' onClick='useAddress(" + i + ")'><h3>" + a.address + "</h3><p>" + a.attributes.User_fld + "</p><span class='ui-li-count'>" + a.score + "</span></a>";
			} else {
				var addr = a.address.split(",");
				var content = "<a href='#' onClick='useAddress(" + i + ")'>" + addr[0] + "<span class='ui-li-count'>" + a.score + "</span></a>";
			}
			li.append(content);
			//add the list item to the feature type list
			$('#addressList').append(li);
			addCount += 1;
		}
	});	
	//refresh the featurelist so the jquery mobile style is applied
	$('#addressList').listview('refresh');
	$("#addListHeader").html("&nbsp;&nbsp;" + addCount + " found in Yakima");

}

function useAddress(i) {
	a = gcr[i];
	// check to see if a place name was used, if so, provide an actual address and the location name.
	var addr = "";
	if (a.attributes.Addr_type == "Address") {
		$("#location").html(a.attributes.User_fld + "; " + a.address);
		$("#locationaddr").val(a.attributes.User_fld + "; " + a.address);
		addr = a.attributes.User_fld + "; " + a.address
	} else {
		addr = a.address
		$("#location").html(a.address);
		$("#locationaddr").val(a.address);
	}
	$("#mapMessages").html("<center>Click Report<br>" + addr + "</center>");
	$("#latitude").val(a.location.y.toFixed(6));
	$("#longitude").val(a.location.x.toFixed(6));
	$("#locMessage").html("Address used from list");
	//clear existing graphics
	map.graphics.clear();
	var pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point(a.location.x, a.location.y));
	// Check to see if the point is in the Yakima City Limits
	var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
	if (isYakima == false) {
		$("#mapMessages").html("<center>Click Report<br>" + addr + "<br><font color='#990000'>This location is not in Yakima<br>Please contact the local jurisdiction</font></center>");
	}
	map.centerAndZoom(pt, 17);
	var blueDot = new esri.symbol.PictureMarkerSymbol({
		"angle": 0,
		"xoffset": 0,
		"yoffset": 0,
		"url": "images/bluedot.png",
		"contentType": "image/png",
		"width": 40,
		"height": 40
		});
	var gra = new esri.Graphic(pt, blueDot);
	map.graphics.add(gra);
	if (pageAddrReturn == 'map') {
		$.mobile.changePage("#mapPage");
	} else {
		$.mobile.changePage("#reportForm");
	}
}

function checkAddress() {

	if ($("#location").html() == "No location provided") {
		alert("You haven't selected a location for a new report yet.  Please use the Address, GPS buttons or tap on map to choose a location");
	} else {
		var pt = esri.geometry.geographicToWebMercator(new esri.geometry.Point($("#longitude").val(), $("#latitude").val()));
		// Check to see if the point is in the Yakima City Limits
		var isYakima = yakimaCL.graphics[0].geometry.contains(pt);
		if (isYakima) {
			$.mobile.changePage("#reportForm");
		} else {
			alert("This location is not in Yakima, please contact the local jurisdiction.");
		}
	}
}

function errAddress(geocodeResults) {
	gcr = geocodeResults;
	a = gcr[0];
	$("#latitude").val(a.location.y.toFixed(6));
	$("#longitude").val(a.location.x.toFixed(6));
	b = 1;
}

function deviceLocation() {
	if (navigator.geolocation) {
		// we are just getting the device position on demand
		navigator.geolocation.getCurrentPosition(returnLocation, locationError);
	}
}

function returnLocation(location) {	
	// once the point has been found by the device GPS, find the approximate address
	var revGeoPT = new esri.geometry.Point(location.coords.longitude, location.coords.latitude);		
	locator.locationToAddress(revGeoPT, 100, addressForPoint);

}

function addClusters() {
	var citreqInfo = {};
	var wgs = new esri.SpatialReference({ "wkid": 4326 });
	citreqInfo.data = dojo.map(reqdata, function(p) {
		var latlng = new esri.geometry.Point(parseFloat(p.longitude), parseFloat(p.latitude), wgs);
		var webMercator = esri.geometry.geographicToWebMercator(latlng);
		var attributes = p;
		return { "x": webMercator.x, "y": webMercator.y, "attributes": attributes };
	});
	
	// popupTemplate to work with attributes specific to this dataset
	var popupTemplate = esri.dijit.PopupTemplate({
		"title": "",
		"fieldInfos": [
			{ "fieldName": "name", "label": "Request Type", visible: true },
			{ "fieldName": "location", "label": "Location",  visible: true },
			{ "fieldName": "description", "label": "Description", visible: true }
		],
		"mediaInfos": [{
			"title": "{name}",
			"caption": "{description}",
			"type": "image",
			"value": {
				"sourceURL": "{photo}",
				"linkURL": "{photo}"
			}
		}]
	});
	
	// cluster layer that uses OpenLayers style clustering
	clusterLayer = new extras.ClusterLayer({ 
		"data": citreqInfo.data,
		"distance": 100,
		"id": "clusters", 
		"labelColor": "#fff",
		"labelOffset": 10,
		"resolution": map.extent.getWidth() / map.width,
		"singleColor": "#888",
		"singleTemplate": popupTemplate
	});
	var defaultSym = new esri.symbol.SimpleMarkerSymbol().setSize(4);
	var renderer = new esri.renderer.ClassBreaksRenderer(defaultSym, "clusterCount");
	var blue = new esri.symbol.PictureMarkerSymbol("images/BluePin1LargeB.png", 32, 32).setOffset(0, 15);
	var green = new esri.symbol.PictureMarkerSymbol("images/GreenPin1LargeB.png", 64, 64).setOffset(0, 15);
	var red = new esri.symbol.PictureMarkerSymbol("images/RedPin1LargeB.png", 72, 72).setOffset(0, 15);
	renderer.addBreak(0, 4, blue);
	renderer.addBreak(4, 20, green);
	renderer.addBreak(20, 1001, red);
	
	clusterLayer.setRenderer(renderer);
	map.addLayer(clusterLayer);
	
	// close the info window when the map is clicked
	// dojo.connect(map, "onClick", cleanUp);
	// close the info window when esc is pressed
	dojo.connect(map, "onKeyDown", function(e) {
		if ( e.keyCode == 27 ) { 
			cleanUp();
		}
	});
}

function cleanUp() {
	map.infoWindow.hide();
	clusterLayer.clearSingles();
}

function error(err) {
	console.log("something failed: ", err);
}

// show cluster extents 
function showExtents() {
	var extents = new esri.layers.GraphicsLayer();
	var sym = new esri.symbol.SimpleFillSymbol().setColor(new dojo.Color([205,193,197,0.5]));
	
	dojo.forEach(clusterLayer._clusters, function(c) {
		var e = c.attributes.extent;
		extents.add(new esri.Graphic(new esri.geometry.Extent(e[0], e[1], e[2], e[3]), sym));
	}, this);
	map.addLayer(extents, 0);
	console.log("added extents");
}

function stupidIEzoom() {
	// stupid IE doesn't trigger pageshow properly so...  we have to do a special zoom just for IE
	var gra = map.graphics.graphics[0];
	if (gra.geometry.x != 0) {
		map.centerAndZoom(gra.geometry,16);
	} else {
		map.setExtent(initExtent)
	}
}

(function($) {
	$.fn.placeholder = function() {
		if(typeof document.createElement("input").placeholder == 'undefined') {
		$('[placeholder]').focus(function() {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
		input.val('');
		input.removeClass('placeholder');
		}
		}).blur(function() {
		var input = $(this);
		if (input.val() == '' || input.val() == input.attr('placeholder')) {
		input.addClass('placeholder');
		input.val(input.attr('placeholder'));
		}
		}).blur().parents('form').submit(function() {
		$(this).find('[placeholder]').each(function() {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
		input.val('');
		}
		})
		});
		}
	}
})(jQuery);

function entsub() {
	if (window.event && window.event.keyCode == 13){
		searchAddresses();
	}
}

var Resample = (function (canvas) {

 // (C) WebReflection Mit Style License

 // Resample function, accepts an image
 // as url, base64 string, or Image/HTMLImgElement
 // optional width or height, and a callback
 // to invoke on operation complete
 function Resample(img, width, height, onresample) {
  var
   // check the image type
   load = typeof img == "string",
   // Image pointer
   i = load || img
  ;
  // if string, a new Image is needed
  if (load) {
   i = new Image;
   // with propers callbacks
   i.onload = onload;
   i.onerror = onerror;
  }
  // easy/cheap way to store info
  i._onresample = onresample;
  i._width = width;
  i._height = height;
  // if string, we trust the onload event
  // otherwise we call onload directly
  // with the image as callback context
  load ? (i.src = img) : onload.call(img);
 }
 
 // just in case something goes wrong
 function onerror() {
  throw ("not found: " + this.src);
 }
 
 // called when the Image is ready
 function onload() {
  var
   // minifier friendly
   img = this,
   // the desired width, if any
   width = img._width,
   // the desired height, if any
   height = img._height,
   // the callback
   onresample = img._onresample
  ;
  // if width and height are both specified
  // the resample uses these pixels
  // if width is specified but not the height
  // the resample respects proportions
  // accordingly with orginal size
  // same is if there is a height, but no width
  width == null && (width = round(img.width * height / img.height));
  height == null && (height = round(img.height * width / img.width));
  // remove (hopefully) stored info
  delete img._onresample;
  delete img._width;
  delete img._height;
  // when we reassign a canvas size
  // this clears automatically
  // the size should be exactly the same
  // of the final image
  // so that toDataURL ctx method
  // will return the whole canvas as png
  // without empty spaces or lines
  canvas.width = width;
  canvas.height = height;
  // drawImage has different overloads
  // in this case we need the following one ...
  context.drawImage(
   // original image
   img,
   // starting x point
   0,
   // starting y point
   0,
   // image width
   img.width,
   // image height
   img.height,
   // destination x point
   0,
   // destination y point
   0,
   // destination width
   width,
   // destination height
   height
  );
  // retrieve the canvas content as
  // base4 encoded PNG image
  // and pass the result to the callback
  onresample(canvas.toDataURL("image/png"));
 }
 
 var
  // point one, use every time ...
  context = canvas.getContext("2d"),
  // local scope shortcut
  round = Math.round
 ;
 
 return Resample;
 
}(
 // lucky us we don't even need to append
 // and render anything on the screen
 // let's keep this DOM node in RAM
 // for all resizes we want
 this.document.createElement("canvas"))
);

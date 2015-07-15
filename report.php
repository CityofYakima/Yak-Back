<?php require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php");session_start(); $_SESSION["token"] = base64_encode(openssl_random_pseudo_bytes(64, $strong)); ?>
<!DOCTYPE html> 
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Yak Back</title> 
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
    <link rel="stylesheet" type="text/css" href="http://serverapi.arcgisonline.com/jsapi/arcgis/3.1/js/esri/dijit/css/PopupMobile.css">
    <link rel="stylesheet" type="text/css" href="http://serverapi.arcgisonline.com/jsapi/arcgis/3.1/js/esri/dijit/css/Popup.css"/>
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/config.js"></script>
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />
	<script type="text/javascript" src="http://serverapi.arcgisonline.com/jsapi/arcgis/?v=3.1compact"></script>	
	<script type="text/javascript" src="includes/js/map.js"></script>	
</head> 
<body> 

    
    <!-- Start of first page: #mapPage -->
    <div data-role="page" id="mapPage" class="page-map" data-theme="b">
    
        <div data-role="header" id="mapheader">
            <div data-role="navbar" class="nav-glyphish">
                <ul>
                    <li><a href="#" data-rel="back" id="backmap" data-icon="custom">Back</a></li>
                    <li><a href="#" id="locationmap" data-icon="custom" onClick="getLocation()">GPS</a></li>
                    <li><a href="#findAddressPage" id="addressmap" data-icon="custom">Address</a></li>
                    <li><a href="#" onClick="checkAddress()" id="reportmap" data-icon="custom">Report</a></li>
                    <li><a href="#" onClick="zoomAll()" id="zoomdefault" data-icon="custom" >Yakima</a></li>
                </ul>
            </div>
        </div>
        
        <div dojotype="dijit.layout.BorderContainer" design="headline" gutters="false" style="width: 100%; height: 100%; margin: 0;">
            <div id="map" dojotype="dijit.layout.ContentPane" region="center">
            	<div id="mapMessages"></div>
                <div id="mapLegend"><img src="images/legend.png" width="100" height="73"></div>
            </div>
        </div>
    
    </div><!-- /mapPage -->
        
    <!-- Find by address Page -->
        <div id="findAddressPage" data-role="page" data-theme="a">
            <div data-role="header" data-theme="a">
                <a href="#" data-rel="back" data-inline="true" data-icon="back">Back</a>
                <h1>Addresses</h1>
            </div>
            <div data-role="fieldcontain">
                <label for="addrSearch">&nbsp;Find Address:</label>
                <input type="search" id="addrSearch" onChange="searchAddresses()" onKeyPress="entsub()" value="" placeholder="ex: 129 N 2nd St or City Hall" />
            </div>
            <div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
				<label class="ui-input-text" for=""> </label>
				<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" value="Search Addresses" type="submit" name="submit" id="submit" >
			</div>
            <br>
            <div data-role="content" >	
                <ul id="addressList" data-role="listview">
                </ul>
            </div>
            <div data-role="footer" data-theme="d" data-position="fixed">
                <h4>&copy; 2012 City of Yakima</h4>
            </div><!-- /footer -->
        </div>

    <!-- GPS Location Services Dialog -->
    <div id="locationServicesPage" data-role="dialog">          
        <div data-role="header" data-theme="d">
            <h1>Warning!</h1>    
        </div>
            
        <div data-role="content" data-theme="c">
            <h3>Location Services Disabled!</h3>
            <p>Your GPS location services are disabled.  You will not be able to use your device GPS until you allow location aware browsing.</p>
            <a href="#" onClick="noGPSmessage()" data-role="button" data-rel="back" data-theme="b">No more warnings!</a>       
            <a href="#" data-role="button" data-rel="back" data-theme="c">Done</a>    
            <a href="#" onClick="resetGPSmessage()" data-role="button" data-rel="back" data-theme="b">Reset</a>       
        </div>
    </div>

    <!-- Error Dialog -->
    <div id="deviceErrorPage" data-role="dialog">          
        <div data-role="header" data-theme="d">
            <h1>Warning!</h1>    
        </div>
            
        <div data-role="content" data-theme="c">
            <h3 id="deviceErrorHeader">Device Error</h3>
            <p id="deviceErrorMessage">Your devices GPS has low quality accuracy.  You may want to move the blue pin on the map to a suitable location or enter an address.</p>
            <a href="#" data-role="button" data-rel="back" data-theme="c">Done</a>    
        </div>
    </div>

<!-- Report entry form -->
<div data-role="page" id="reportForm" data-theme="a">
        <div data-role="header" id="formheader">
            <div data-role="navbar" class="nav-glyphish">
                <ul>
                    <li><a href="#" data-rel="back" id="backform" data-icon="custom">Back</a></li>
                    <li><a href="#findAddressPage" id="addressform" data-icon="custom">Address</a></li>
                    <li><a href="#" id="locationform" data-icon="custom" onClick="getLocation()">GPS</a></li>
                    <li><a href="#mapPage" id="mapform" data-icon="custom">Map</a></li>
                    <li><a href="index.php" id="homeform" data-icon="custom">Home</a></li>
                </ul>
            </div>
        </div><!-- /header -->

	<div data-role="content" >	
		<script>
			function validateForm(){
				var x=document.forms["reportform"]["locationaddr"].value;
				if (x==null || x==""){
				  alert("You must fill out a location to continue. Please use the Address, GPS or the Map tools above to enter the location ");
				  return false;
				}else{
					 $('input:submit').attr("disabled", true);
					 $.mobile.showPageLoadingMsg("a", "Submitting your request...", false);
					 //$.mobile.loading( 'show', {text: 'Submitting your request...',	textVisible: true,theme: 'a', html: ""});
				}
			}
		</script>
		<form action="form-process.php" method="POST" data-ajax="false" enctype="multipart/form-data" name="reportform" id="reportform" onsubmit="return validateForm()">
			<input type="hidden" name="userID" id="userID" value="<?php if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){echo $_COOKIE["ycprofile"];} ?>">
			<input type="hidden" name="token" id="token" value="<?php echo($_SESSION["token"]); ?>">
			<input type="hidden" name="locationaddr" id="locationaddr" value="">
			<input type="hidden" name="longitude" id="longitude" value="">
			<input type="hidden" name="latitude" id="latitude" value="">
			<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
                <label class="ui-input-text" for="location">Location:</label>
                <label class="ui-input-text" name="location" id="location">No location provided</label><br>
                <center><label class="ui-input-text" id="locMessage">Use the Address, GPS or the Map tools above to enter the location.</label></center>
	    	</div>
			<div data-role="fieldcontain">
				<label for="typeID" class="select">Request Type:</label>
   			<select name="typeID" id="typeID">
				<?php
					$link = ConnectToDBi('yakimaconnect');
			
					$sql = "SELECT *
									FROM `requestType`
									WHERE `source` = 'app'
									ORDER BY `order`
									";
					$result = mysqli_query($link, $sql);
				
					while($row = mysqli_fetch_assoc($result)){
						
						echo "<option value=\"$row[id]\">$row[Name]</option>";
					}
		
					/* close connection */
					mysqli_close($link);
				?>
				</select>
			</div>
			<?php if ((strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS 5') == false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'IEMobile')== false)){?>
			<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
				<label class="ui-input-text" for="photo">Add Photo:</label>
				<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" name="photo" id="photo" value="" type="file">
			</div>
			<?php }?>
			
			<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
				<label class="ui-input-text" for="textarea">Description:</label>
				<textarea class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" cols="40" rows="8" name="textarea" id="textarea"></textarea>
			</div>
			
			<?php 
				if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
			?>
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
		    	<label class="ui-input-text" for="reported">Reported By:</label>
		    	<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" name="reported" id="reported" value="<?php echo $_COOKIE["ycprofile"]; ?>" type="text" disabled>
				</div>
			<?php }else{ ?>
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
		    	<label class="ui-input-text" for="reported">Reported By:</label>
		    	<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" name="reported" id="reported" value="Anonymous" type="text" disabled>
				</div>
			<? } ?>
			
			<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
				<label class="ui-input-text" for=""> </label>
				<input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" value="Submit Report" type="submit" name="submit" id="submit" >
			</div>
		</form>
		
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page reportForm -->

</body>
</html>
<!DOCTYPE html> 
<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php"); 
	$id = filter_var($_GET["id"], FILTER_VALIDATE_INT);
	if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
		$useremail = $_COOKIE["ycprofile"];
	}else{
		$useremail = '';
	}
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Yak Back</title> 
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/config.js"></script>
	<script>
		$(document).bind('mobileinit',function(){
			$.mobile.page.prototype.options.keepNative = "input";
		});
		$(document).ready(function(){
			$('#commentposted').delay(5000).slideUp();
		});
	</script>
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />

</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" id="one" data-theme="a">
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		
		<h1>Details</h1>
	  <div data-type="horizontal" data-role="controlgroup" class="ui-btn-right ui-btn-header">  
	    <a class="ui-btn ui-btn-active ui-corner-left ui-btn-up-c" data-theme="c" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="<?php echo $_SERVER["REQUEST_URI"]; ?>" data-role="button"><span class="ui-btn-inner ui-corner-left"><span class="ui-btn-text">Detail</span></span></a>
			<a class="ui-btn ui-corner-right ui-controlgroup-last ui-btn-up-c" data-theme="c" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="report.php?request=recent&eventid=<?php echo $id; ?>" data-role="button" data-ajax="false"><span class="ui-btn-inner ui-corner-right ui-controlgroup-last"><span class="ui-btn-text">Map</span></span></a>
	  </div>

	</div><!-- /header -->

	<div data-role="content" class="centered-text">	
		<ul data-role="listview" class="ui-listview"  data-inset="true">
		<li>
		<?php
			$link = ConnectToDBi('yakimaconnect');
			if(isset($_POST["comment"]) && $_POST["comment"] != ''){
				if(isset($_POST["email"]) && $_POST["email"] != ''){
					$email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
				}else{
					$email = '';
				}
				$comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
				$ip 		 = filter_var($_POST["ip"],      FILTER_SANITIZE_STRING);
				$posted  = date("Y-m-d H:i:s");
				
				$sql  = "INSERT INTO `comments`
								 SET 
								 `email` = '$email',
								 `comment` = '$comment',
								 `requestID` = '$id',
								 `ip` = '$ip',
								 `posted` = '$posted'
								 ;
								";

				
				mysqli_query($link, $sql);
				$commentposted = 1;
				unset($sql);
			}else{
				$commentposted = '';
			}
			
	
			$sql = "SELECT r.id, name, dateOpened, dateClosed, photo, status, description, location, dateAssigned
							FROM `requests` r, `requestType` t
							WHERE r.typeID = t.ID
							AND r.id = $id
							ORDER BY dateOpened DESC
							LIMIT 0, 1
							";
			$result = mysqli_query($link, $sql);
		
			while($row = mysqli_fetch_assoc($result)){
				$openedDay = substr($row["dateOpened"],8,2);
				$openedMon = substr($row["dateOpened"],5,2);
				$openedMon = mktime(0,0,0,$openedMon+1,0,0);
				$openedMon = date("M", $openedMon);
				$openedYr  = substr($row["dateOpened"],0,4);
				$openedHr  = substr($row["dateOpened"],11,2);
				$openedMin = substr($row["dateOpened"],14,2);
				if ($openedHr > 12){
					$meridian = "pm";
					$openedHr = $openedHr-12;
				}else{
					$meridian = "am";	
				}
				
				$closedDay = substr($row["dateClosed"],8,2);
				$closedMon = substr($row["dateClosed"],5,2);
				$closedMon = mktime(0,0,0,$closedMon+1,0,0);
				$closedMon = date("M", $closedMon);
				$closedYr  = substr($row["dateClosed"],0,4);
				$closedHr  = substr($row["dateClosed"],11,2);
				$closedMin = substr($row["dateClosed"],14,2);
				if ($closedHr > 12){
					$cmeridian = "pm";
					$closedHr = $closedHr-12;
				}else{
					$cmeridian = "am";	
				}
				if ($row["dateClosed"] != ""){$isOpen = "Closed";}else if($row["dateAssigned"] != ""){$isOpen = "Assigned";}else{$isOpen = "Open";}
				echo "<p class=\"ui-li-aside\"><strong>Status: <span class=\"$isOpen\">$isOpen</span></strong></p>";
				echo "<h3>$row[location]</h3>";
				if ($row["photo"] != ""){
					echo "<p style=\"text-align: center\"><img src=\"./uimages/$row[photo]\" alt=\"report image\" style=\"width:250px;\"></p>";
				}
				echo "<p>$row[description]</p>";
				echo "<hr/>";
				echo "<h5>Details</h5>";
				echo "<p><strong>Opened on</strong>: $openedMon $openedDay, $openedYr at $openedHr:$openedMin$meridian</p>";
				
				if($isOpen == "Closed"){
					echo "<p><strong>Closed on</strong>: $closedMon $closedDay, $closedYr at $closedHr:$closedMin$cmeridian</p>";
					echo "<p><strong>Notes</strong>: $row[status]";
				}
			}
			
			

		?>
			<hr/>

			</li>
		</ul>
	
		
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="a" data-position="fixed">
		<div data-role="navbar">
		<ul>
			<li><a href="#" data-icon="back" data-rel="back" data-iconpos="notext"></a></li>
			<li><a href="tel:5095756027" data-icon="grid" data-iconpos="notext"></a></li>
			<li><a href="./" data-icon="home"  data-iconpos="notext"></a></li>
		</ul>
	</div><!-- /navbar -->
		
		
	</div><!-- /footer -->
</div><!-- /page one -->
<!-- Start of first page: #one -->

<?php 

	/* close connection */
	mysqli_close($link);
			
?>
</body>
</html>
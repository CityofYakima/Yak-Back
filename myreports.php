<!DOCTYPE html> 
<?php require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php"); ?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Yak Back</title> 
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/config.js"></script>	
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" id="one" data-theme="b">
	<?php
		if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
			$email = $_COOKIE["ycprofile"];
			$link = ConnectToDBi('yakimaconnect');
			$sql = "SELECT * FROM `user`
							WHERE email = '$email'
							";
			$result = mysqli_query($link, $sql);
			$row 		= mysqli_fetch_assoc($result);
			$userid = $row["id"];
			mysqli_close($link);
		}
	?>
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>My Reports</h1>
	  <div data-type="horizontal" data-role="controlgroup" class="ui-btn-right ui-btn-header">  
	    <a class="ui-btn ui-btn-active ui-corner-left ui-btn-up-c" data-theme="c" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="recent.php" data-role="button"><span class="ui-btn-inner ui-corner-left"><span class="ui-btn-text">List</span></span></a>
			<a class="ui-btn ui-corner-right ui-controlgroup-last ui-btn-up-c" data-theme="c" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" data-ajax="false" href="report.php?request=recent&userid=<?php echo $userid; ?>" data-role="button"><span class="ui-btn-inner ui-corner-right ui-controlgroup-last"><span class="ui-btn-text">Map</span></span></a>
	  </div>
	</div><!-- /header -->

	<div data-role="content" >	
		
		<?php
			//unset($row);
			if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
				$email = $_COOKIE["ycprofile"];
				$link = ConnectToDBi('yakimaconnect');
				$sql = "SELECT r.id, name, dateOpened, dateClosed, description, location, dateAssigned
								FROM `requests` r, `requestType` t, `user` u
								WHERE r.typeID = t.ID
								AND r.userID = u.ID
								AND r.source = 'app'
								AND u.email = '$email'
								ORDER BY dateOpened DESC
								";
								
				$result = mysqli_query($link, $sql);
				echo "<ul class=\"ui-listview\" data-role=\"listview\" data-theme=\"a\" data-divider-theme=\"a\">";
				while($row = mysqli_fetch_assoc($result)){
					$openedDay = substr($row["dateOpened"],8,2);
					$openedMon = substr($row["dateOpened"],5,2);
					$openedMon = mktime(0,0,0,$openedMon+1,0,0);
					$openedMon = date("M", $openedMon);
					$openedYr  = substr($row["dateOpened"],0,4);
					$openedHr  = substr($row["dateOpened"],11,2);
					$openedMin = substr($row["dateOpened"],14,2);
					if ($openedHr > 12){
						$meridian = "PM";
						$openedHr = $openedHr-12;
					}else{
						$meridian = "AM";	
					}
					if ($row["dateClosed"] != ""){$isOpen = "Closed";}else if($row["dateAssigned"] != ""){$isOpen = "Assigned";}else{$isOpen = "Open";}
					
					echo "<li class=\"ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-d\" data-theme=\"d\" data-iconpos=\"right\" data-icon=\"arrow-r\" data-wrapperels=\"div\" data-iconshadow=\"true\" data-shadow=\"false\" data-corners=\"false\">";
					echo "<div class=\"ui-btn-inner ui-li\">";
					echo "<div class=\"ui-btn-text\">";
					echo "<a class=\"ui-link-inherit\" href=\"details.php?id=$row[id]\" data-ajax=\"false\" >";
					echo "<p class=\"ui-li-aside ui-li-desc\"><small class=\"$isOpen\"><strong>Status: </strong>$isOpen </small> <strong>&nbsp;$openedMon $openedDay, $openedYr $openedHr:$openedMin</strong>$meridian</p>";
				echo "<h3 class=\"ui-li-heading\">$row[name]</h3>";
					echo "<p class=\"ui-li-desc\"><strong>$row[location]</strong></p>";
					echo "<p class=\"ui-li-desc\">$row[description]</p>";
					echo "</a>";
					echo "</div>";
					echo "<span class=\"ui-icon ui-icon-arrow-r ui-icon-shadow\">&nbsp;</span>";
					echo "</div>";
					echo "</li>";
				}
				echo "</ul>";
				/* close connection */
				mysqli_close($link);
			}else{
				
			?>
			<p>You must login to view reports that have been posted by you</p>
			<form data-ajax="false" method="POST" action="./login.php?returnTo=myreports.php" id="login">
				
				<div data-role="fieldcontain">
			  	<label class="ui-input-text" for="email">Email Address:</label>
			    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" name="email" id="email" value="" type="email">
				</div>
				<div data-role="fieldcontain">
			  	<label class="ui-input-text" for="phone">Phone:</label>
			    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset" name="phone" id="phone" value="" type="text">
				</div>
				<input type="submit" value="Submit">
			</form>
			
			
			<?php
			}
						
		?>
		
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->

</body>
</html>
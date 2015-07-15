<!DOCTYPE html> <?php require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php"); ?>
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
<div data-role="page" id="one" data-theme="a">

	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>Yak Back - New Report</h1>
	</div><!-- /header -->

	<div data-role="content" >	
		<ul data-role="listview" data-inset="true" data-theme="a">
					<?php
			$link = ConnectToDBi('yakimaconnect');
	
			$sql = "SELECT *
							FROM `requestType`
							ORDER BY Name
							";
			$result = mysqli_query($link, $sql);
		
			while($row = mysqli_fetch_assoc($result)){
				
				echo "<li><a href=\"report.php?type=$row[id]&name=$row[Name]\">$row[Name]</a></li>";
			}

			/* close connection */
			mysqli_close($link);
		?>

		</ul>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->
</body>
</html>
<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php"); 
?>
<!DOCTYPE html> 
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Yak Back</title> 
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/config.js"></script>
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<script type="text/javascript" src="/includes/js/jquery.validate.min.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" id="one" data-theme="a" data-dom-cache="false" >
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>My Profile</h1>
		<a href="./" data-icon="home" data-iconpos="notext"></a>
	</div><!-- /header -->

	<div data-role="content" >
		<?php 
			if (isset($_POST["first"]) && $_POST["first"] != '' && !isset($_COOKIE["ycprofile"])){
				//clean first - always clean variables
				$fname = filter_var($_POST["first"], FILTER_SANITIZE_STRING);
				$lname = filter_var($_POST["last"],  FILTER_SANITIZE_STRING);
				$phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
				$email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
				
				$link = ConnectToDBi('yakimaconnect');
				$sql = "INSERT INTO `user`
								SET
							 `firstName` 	= '$fname',
							 `lastName` 	= '$lname',
							 `phone`			= '$phone',
							 `email`			= '$email'		
				";
				//echo $sql;
				mysqli_query($link, $sql);
				mysqli_close($link);
				setcookielive('ycprofile',$email,time()+2629743,'/','yakimawa.gov'); //set the cookie with the email address
				setcookielive('ycname'	 ,$fname,time()+2629743,'/','yakimawa.gov'); //set the cookie with the email address
				header('Location: ./');
			}elseif(isset($_POST["first"]) && $_POST["first"] != '' && $_COOKIE["ycprofile"] != ''){
				//clean first - always clean variables
				$fname = filter_var($_POST["first"], FILTER_SANITIZE_STRING);
				$lname = filter_var($_POST["last"],  FILTER_SANITIZE_STRING);
				$phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
				$email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
				
				$link = ConnectToDBi('yakimaconnect');
				$sql = "UPDATE `user`
								SET
							 `firstName` 	= '$fname',
							 `lastName` 	= '$lname',
							 `phone`			= '$phone'
							 WHERE
							 `email`			= '$email'		
				";
				//echo $sql;
				mysqli_query($link, $sql);
				mysqli_close($link);
				
				header('Location: ./');
			}
			//print_r($_COOKIE); 
			if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != '')
			{
				$link = ConnectToDBi('yakimaconnect');
				$sql = "SELECT * FROM `user` WHERE `email` = '$_COOKIE[ycprofile]' LIMIT 0,1";
				$result = mysqli_query($link, $sql);
				$row = mysqli_fetch_assoc($result);
				$fname = $row["firstName"];
				$lname = $row["lastName"];
				$phone = $row["phone"];
				$email = $row["email"];
				mysqli_close($link);
			}else{
				$fname = "";
				$lname = "";
				$phone = "";
				$email = "";
			}
		?>
		<form data-ajax="false" method="POST" action="./profile.php" id="profile" class="validate">
			<div data-role="fieldcontain">
		  	<label class="ui-input-text" for="first">First Name:</label>
		    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required" name="first" id="first" value="<?php echo $fname; ?>" type="text">
			</div>
			<div data-role="fieldcontain">
		  	<label class="ui-input-text" for="last">Last Name:</label>
		    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required" name="last" id="last" value="<?php echo $lname; ?>" type="text">
			</div>
			<div data-role="fieldcontain">
		  	<label class="ui-input-text" for="email">Email Address:</label>
		    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required email" name="email" id="email" value="<?php echo $email; ?>" type="email">
			</div>
			<div data-role="fieldcontain">
		  	<label class="ui-input-text" for="phone">Phone:</label>
		    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required" name="phone" id="phone" value="<?php echo $phone; ?>" type="text">
			</div>
	
			<input type="submit"  value="Submit">	
		</form>
		<script>
			$("#profile").validate({
			    
			});
		</script>
		<?php if(!isset($_COOKIE["ycprofile"]))
			{
			
		?>
			<hr/>
			<p>Already Setup a Profile?</p>
			<a href="login.php<?php if(isset($_GET["report"]) && $_GET["report"] != ''){echo "?report=inprocess";}?>" data-role="button" data-theme="e" >Log in here</a> 
		<?php
			}else if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
				echo "<a href=\"logout.php\" data-role=\"button\" data-theme=\"e\" >Log out</a>";
			}
		?>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->
<div data-role="page" id="two" data-theme="a">
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>My Profile</h1>
		<a href="./" data-icon="home" data-rel="back" data-iconpos="notext"></a>
	</div><!-- /header -->

	<div data-role="content" >
			Your Profile has been created. 
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->
</body>
</html>
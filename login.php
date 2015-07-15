<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] ."/includes/php/include.php"); 
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
<div data-role="page" id="one" data-theme="a">
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>Login</h1>
		<a href="./" data-icon="home" data-iconpos="notext"></a>
	</div><!-- /header -->

	<div data-role="content" >
		<?php 
			//if we get an email, let's process this sucker
			if ((isset($_POST["email"]) && $_POST["email"] != '') && (isset($_POST["phone"]) && $_POST["phone"] != '')){
				
				//clean first - always clean variables
				$phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
				$email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
				if(isset($_POST["inprocess"]) && $_POST["inprocess"] != ''){
					$inprocess = filter_var($_POST["inprocess"], FILTER_SANITIZE_STRING);
				}
				//look up the user
				$link 	= ConnectToDBi('yakimaconnect');
				$sql 		= "SELECT * FROM `user` WHERE `email` = '$email' AND `phone` LIKE '%$phone%' LIMIT 0,1";
				$result = mysqli_query($link, $sql);
				$row    = mysqli_fetch_assoc($result);
				$num    = mysqli_num_rows($result);
				mysqli_close($link);
								
				if($num>0){  																									//if we found the user, log them in by setting the cookie
					setcookielive('ycprofile',$email,time()+2629743,'/','yakimawa.gov');
					setcookielive('ycname'	 ,$row["firstName"],time()+2629743,'/','yakimawa.gov'); 	//set the cookie with the email address and firstname
				if(isset($_GET["returnTo"]) && $_GET["returnTo"] != ''){
					header("Location: ./$_GET[returnTo]");
				}else{
					header('Location: ./login.php#two');												//send them to the yay! page
				}
				}else{																												//----------------------- DID NOT FIND MATCH
					$failed = "<p><strong>Please try again, no user found with that email and phone.</strong></p>"; //diplay a message if you failed login
					echo $failed;
				}
			}else if ((isset($_POST["email"]) && $_POST["email"] != '') && ($_POST["phone"] == '')){
				$failed = "<p><strong>Please enter both an email address and phone number.</strong></p>"; //diplay a message if you failed login
				echo $failed;
			}else if ((isset($_POST["phone"]) && $_POST["phone"] != '') && ($_POST["email"] == '')){
				$failed = "<p><strong>Please enter both an email address and phone number.</strong></p>"; //diplay a message if you failed login
				echo $failed;
			}

		?>
		<p>Please enter your <strong>Email</strong> and <strong>Phone Number</strong> to login to your account</p>
		<form data-ajax="false" method="POST" action="./login.php<?php if(isset($_GET["returnTo"]) && $_GET["returnTo"] != ''){ echo "?returnTo=$_GET[returnTo]"; } ?>" id="login" class="validate">
		<?php if(isset($_GET["report"]) && $_GET["report"] != ''){echo "<input type=\"hidden\" name=\"inprocess\" id=\"inprocess\" value=\"inprocess\">";}?>
		<div data-role="fieldcontain">
	  	<label class="ui-input-text" for="email">Email Address:</label>
	    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required email" name="email" id="email" value="" type="email">
		</div>
		<div data-role="fieldcontain">
	  	<label class="ui-input-text" for="phone">Phone:</label>
	    <input class="ui-input-text ui-body-c ui-corner-all ui-shadow-inset required" name="phone" id="phone" value="" type="text">
		</div>
		<input type="submit" value="Submit">
		</form>
		<script>
			$("#login").validate({
			    
			});
		</script>
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
			You have successfully logged in.

			<a href="./" data-icon="home" data-role="button" data-rel="external" data-prefetch="false">Back to the Yak Back Home Page</a>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->
</body>
</html>
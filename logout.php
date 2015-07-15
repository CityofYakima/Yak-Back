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
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 




<script>
$('#exitPage').live('pagebeforecreate', function(){
    $(document).empty();
    window.location.replace('./');
});

</script>
<div data-role="page" id="exitPage">
<?php 
if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){
	//This is setting the cookie to the past, triggering browser delete
	setcookie ("ycprofile", "", time() - 3600, "/", "yakimawa.gov");
	setcookie ("ycname",    "", time() - 3600, "/", "yakimawa.gov");
}

?>
	<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		<h1>My Profile</h1>
		<a href="./" data-icon="home" data-rel="back" data-iconpos="notext"></a>
	</div><!-- /header -->

	<div data-role="content" >
			You have succesfully logged out.

			<a href="./" data-icon="home" data-role="button" data-rel="external" data-prefetch="false">Back to the Yak Back Home Page</a>
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->	
	
	
</div>
</body>
</html>
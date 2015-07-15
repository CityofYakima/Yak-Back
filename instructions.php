<!DOCTYPE html> 
<html>
<head>
	<meta charset="utf-8">
	<script>
		if (window.screen.height==568) { // iPhone 4"
			document.querySelector("meta[name=viewport]").content="width=320.1";
		}
	</script>
	<meta name="viewport" content="initial-scale=1.0">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="format-detection" content="telephone=yes">
	<title>Yak Back</title>
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/config.js"></script>
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" data-theme="a"  id="home">
		<div data-role="header">
		<a href="./" data-icon="back" data-rel="back">Back</a>
		
		<h1>Information</h1>
		<div data-type="horizontal" data-role="controlgroup" class="ui-btn-right ui-btn-header">  
	    <a class="ui-btn ui-corner-left ui-btn-up-c" data-theme="c" data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="./" data-role="button"><span class="ui-btn-inner ui-corner-left"><span class="ui-btn-text">Home</span></span></a>
		</div>

	</div><!-- /header -->
	<div data-role="content" >
		<p class="info add-margin-top">Need help with Yak Back? You've come to the right place.</p>
		<h2>What is Yak Back?</h2>
    <p class="info">Yak Back is the City of Yakima's web application to help empower residents of Yakima. As valued citizens of Yakima, we value your input in help us keep the City of Yakima safe and well maintained. Working together, we can keep Yakima a beautiful place.</p>

		<h2>What do these icons mean?</h2>
   	<p class="info">
   		When starting the Yak Back Application, you are shown six icons:<br/>
   		<strong>New Report</strong>: This button allows you to submit a new report to the City of Yakima.<br/>
   		<strong>Recent</strong>: This button will show you a list of the 30 most recent request submitted to Yak Back.<br/>
   		<strong>My Reports</strong>:If you have created a profile in Yak Back, you can check by on the status of your requests with this button.<br/>
   		<strong>Profile</strong>: Use this option to create or edit your profile. This is also where you can choose to log out of the application.<br/>
   		<strong>Feeback</strong>: Use this option to provide general feedback to the City of Yakima.<br/>
   		<strong>509.575.3550</strong>: This shows you the phone number for contacting the City of Yakima. If you're on a mobile device, you can choose this to call the City.
   	</p>
   	<h2>When posting a report, it takes a long time?</h2>
   	<p class="info">When posting a report with an image, it sometimes takes a bit of time for a report to post. This is usually caused by the size of the image. We recommend you upload the smallest picture you have so that the upload process doesn't slow you down. You may also have poor cellular coverage, if that is the case, move to an area with better service.</p>

	</div><!-- /content -->
	<div data-role="footer" data-theme="f"><!-- data-position="fixed" -->
		<div class="logo">
    	<a href="./"><img src="images/logo.png" alt="" title="" border="0" /></a>
    </div>
	</div><!-- /footer -->
</div><!-- /page one -->
<div data-role="dialog" id="dialog">
    <div data-role="header">
        <h1>Login?</h1>
    </div>
    <div data-role="content">
        <p>You are currently using Yak Back as a guest. If you wish to track your requests, please log in or create a profile.</p>
        <a href="login.php" data-role="button">Login</a>
        <a href="profile.php" data-role="button">Create Profile</a>
        <a href="report.php?request=new" data-role="button" data-ajax="false">No thanks, post as a guest</a>
    </div>
</div>

</body>
</html>
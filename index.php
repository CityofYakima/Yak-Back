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
  <!--meta name="apple-mobile-web-app-capable" content="yes" /-->
  <meta name="format-detection" content="telephone=yes">
	<title>Yak Back</title>
	<link rel="apple-touch-icon-precomposed" href="./images/yc-52.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="./images/yc-72.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="./images/yc-114.png">
	<!--startup images -->
	<link href="./images/ycsplash.png" media="(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 1)"	rel="apple-touch-startup-image">
	
	<!-- iPhone (Retina) -->
	<link href="./images/ycsplash@2x.png" media="(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	
	<!-- iPhone 5 -->
	<link href="./images/ycsplash-iphone5.png"	media="(device-width: 320px) and (device-height: 568px)	and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	
	<!-- iPad -->
	<link href="./images/ycsplash-ipad-portrait.png"	media="(device-width: 768px) and (device-height: 1024px)	and (orientation: portrait)	and (-webkit-device-pixel-ratio: 1)"	rel="apple-touch-startup-image">
	<link href="./images/ycsplash-ipad-portrait.png"	media="(device-width: 768px) and (device-height: 1024px)	and (orientation: landscape)	and (-webkit-device-pixel-ratio: 1)"	rel="apple-touch-startup-image">
	
	<!-- iPad (Retina) -->
	<link href="./images/ycsplash-ipad-portrait2x.png" media="(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	<link href="./images/ycsplash-ipad-portrait2x.png" media="(device-width: 768px) and (device-height: 1024px)	and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	<!--END STARTUP IMAGES-->
	<link rel="stylesheet" href="./includes/css/jquery.mobile-1.2.0-alpha.1.min.css" />
	<script src="./includes/js/jquery-1.7.2.min.js"></script>
	<script src="./includes/js/jquery.badBrowser.js"></script>
	<script src="./includes/js/config.js"></script>
	<script src="./includes/js/jquery.mobile-1.1.1.js"></script>
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" data-theme="a"  id="home">
	<div data-role="content" >
			<?php 

			if(isset($_COOKIE["ycprofile"]) && $_COOKIE["ycprofile"] != ''){	
				echo "<h3 class=\"welcome\">Welcome back, $_COOKIE[ycname]</h3>";
				$link = "report.php?request=new";
				$rel  = '';
			}else{
				$link = "#dialog";
				$rel  = "data-rel=\"dialog\"";
			}
		?>
		<div class="menu">
    	<ul>
           	<li><a href="<?php echo $link; ?>" <?php echo $rel; ?> data-transition="slide"  data-ajax="false" title="Create a New Report - Do you know about a damaged street sign, graffiti, pothole, or street light that is burned out? Please report it."><span class="icoicon" title="Create a New Report - Do you know about a damaged street sign, graffiti, pothole, or street light that is burned out? Please report it.">e</span><br/>New Report</a></li>
            <li><a href="recent.php" data-transition="slide" title="View Recent Reports that have been submitted"><span class="icoicon" title="View Recent Reports that have been submitted">j</span><br/>Recent</a></li>
            <li><a href="myreports.php" data-transition="slide" title="View My Reports - these are reports that were created with your login"><span class="icoicon" title="View My Reports - these are reports that were created with your login">M</span><br/>My Reports</a></li>
            <li><a href="profile.php"  data-prefetch="false" data-dom-cache="false" title="Create a login profile so that you can track your specific reported issues and so that we can let you know about the status of your issue"><span class="icoicon" title="Create a login profile so that you can track your specific reported issues and so that we can let you know about the status of your issue" data-ajax="false">W</span><br/>Profile</a></li>
            <li><a href="#"  data-prefetch="false" data-dom-cache="false" title="Ask a general question or provide comments to the City on a issue."><span class="icoicon" data-ajax="false" title="Ask a general question or provide comments to the City on a issue.">m</span><br/>Feedback</a></li>
            <li><a href="tel:+15095753550" title="Call the City of Yakima at 509.575.3550"><span class="icoicon" data-ajax="false" title="Call the City of Yakima at 509.575.3550">O</span><br/>509.575.3550</a></li>
        </ul>
        <div class="well">
		<p class="info info-home">Do you know about a damaged street sign, graffiti, pothole, or street light that is burned out? Please report it by choosing new report.</p>
		
    </div>
    <a href="instructions.php" data-role="button" data-icon="info" data-mini="true" data-iconpos="notext" class="info-button pull-right" title="Yak Back Information">Info</a>
    </div>
    
	</div><!-- /content -->
	<div data-role="footer" data-theme="f"><!--  -->
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
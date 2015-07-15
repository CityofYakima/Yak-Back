<?php
	require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php");
	session_start();
	if(isset($_SESSION["token"]) && $_POST["token"] == $_SESSION["token"] && isset($_POST["longitude"]) && $_POST["longitude"] != '' ){
		
		$userID				= filter_var($_POST["userID"], FILTER_SANITIZE_STRING);
		$longitude		= filter_var($_POST["longitude"], FILTER_SANITIZE_STRING);
		$latitude			= filter_var($_POST["latitude"], FILTER_SANITIZE_STRING);
		$location			= filter_var($_POST["locationaddr"], FILTER_SANITIZE_STRING);
		$typeID 			= filter_var($_POST["typeID"], FILTER_SANITIZE_STRING);
		$description 	= filter_var($_POST["textarea"], FILTER_SANITIZE_STRING);
		if($_POST["userID"] != ''){
			$user 				= filter_var($_POST["userID"], FILTER_SANITIZE_STRING);
		}else{
			$user = "Anonymous";
		}
		//first let's lookup the actual userID from the email address provided
		$link 	= ConnectToDBi('yakimaconnect');
				
		$sql 		= "SELECT *
							FROM `user`
							WHERE `email` = '$userID';
							";
		$result = mysqli_query($link, $sql);
		$row 		= mysqli_fetch_assoc($result);
		$userID = $row["id"];
		unset($row);
		
		//UPLOAD A FILE, IF ONE IS PRESENT
		if($_FILES["photo"]["name"] != ''){
			#### FILE UPLOAD PROCESS START ####
			//define upload path
			$target_path = "/var/www/./uimages"; //THIS NEEDS TO BE CHANGE
			
			//we want to organize these files in folders by year, check if the folder exists, if it doesn't, make it
			if(!is_dir($target_path)){
				mkdir($target_path, 0755);	
			}
			
			//clean up the spaces in the filename
			$filename = date("YmdHis");
			$filename = "$filename.jpg";
					
			//more or less, we're defining the renamed file, with folder path
			$target_path = $target_path ."/". basename($filename); 
			
			//check and see if this is a pdf, if not, DO NOT allow upload (security is bestest)
			if($_FILES['photo']['type'] == 'image/jpeg'){
				move_uploaded_file($_FILES['photo']['tmp_name'], $target_path);
		
			}
			#### FILE UPLOAD PROCESS END ####
		}else{
			$filename = '';
		}
		// add right here
		if(isset($_POST["longitude"]) && $_POST["longitude"] != ''){
			$dateOpened = date("Y-m-d H:i:s");
			//NOW LET'S WRITE THAT RECORD!
			$sql = "INSERT INTO `requests`
							SET 
							`location`			= '$location',
							`photo`    			= '$filename',
							`description`   = '$description',
							`dateOpened`		= '$dateOpened',
							`typeID`   			= '$typeID',
							`userID`   			= '$userID',
							`latitude`   		= '$latitude',
							`longitude`   	= '$longitude',
							`source`				= 'app'
							";
			mysqli_query($link,$sql);
		}
		//get the ID of that request you just created, we'll need it in a second
		$sql = "SELECT *
						FROM `requests`
						ORDER BY `requests`.`id` DESC
						LIMIT 0,1;
					 ";
		$result 		 = mysqli_query($link, $sql);
		$request   	 = mysqli_fetch_assoc($result);
		$requestID   = $request["id"];
		
			
		//we need the type of Request Name for the display
		$sql = "SELECT *
						FROM `requestType`
						WHERE `id` = $typeID;
						";
		$result = mysqli_query($link, $sql);
		$type   = mysqli_fetch_assoc($result);
		$type   = $type["Name"];
		
		if($_POST["userID"] != ''){// only do this if they are posting as not anon
			//confirmation email to user
			$to2 			= $user;
			$subject2	= "Thank You for your Yak Back submission!";
			$from2 		= "City of Yakima Web <web@yakimawa.gov>";
	
			//start email message
			$email_message2 = "<html><body>";
			$email_message2 .= "<h1 style='font-size: 20px; color: #666; font-family:Arial,sans-serif; border-bottom: 1px solid #a1a5a9;'>Yak Back and Make Your Voice Count</h1>";
			$email_message2 .= "<p>Thank you for contacting the City of Yakima, you will receive a response to your request within the next 2 business days. The details of your request are below:</p>\n";
			$email_message2 .= "<strong>Reported By</strong>: $user<br/>\n";
			$email_message2 .= "<strong>Address</strong>: $location<br/>\n";
			$email_message2 .= "<strong>Request ID</strong>: $requestID<br/>\n";
			$email_message2 .= "<strong>Type</strong>: $type<br/>\n";
			$email_message2 .= "<strong>Message</strong>: $description<br/>\n";
			$email_message2 .= "</body></html>";
			
			// create email headers
			$headers2 = "From: " . $from2 . "\r\n";
			$headers2 .= "Reply-To: ". $from2 . "\r\n";
			$headers2 .= "MIME-Version: 1.0\r\n";
			$headers2 .= "Content-Type: text/html; charset=utf-8\r\n";
			
	
			mail($to2, $subject2, $email_message2, $headers2);
		}
			
		//FINALLY, let's get the details of who needs to be notified and notify them.			 
		$sql = "SELECT *, d.name AS departmentName
						FROM `requestType` t
						LEFT JOIN `type-department` td ON t.id = td.requesttypeID
						LEFT JOIN `department` d ON td.departmentID = d.departmentID
						WHERE `id` = $typeID;
					 ";
	
		$result = mysqli_query($link,$sql);
		while($row = mysqli_fetch_assoc($result)){
				
				$to 			= "$row[email]";
				$bcc			= "rbonds@ci.yakima.wa.us";
				$subject	= "New Yak Back Request ID #$requestID";
				$from 		= "Yak Back <web@yakimawa.gov>";
				
				$email_message = "<html><body style='line-height: 15px'>";
				$email_message .= "<img src='http://www.yakimawa.gov/./images/logo.png' alt='Yak Back'>";
				$email_message .= "<h1 style='font-size: 20px; color: #666; font-family:Arial,sans-serif; border-bottom: 1px solid #a1a5a9;'>Yak Back Request</h1>";
				$email_message .= "<p><strong>To manage this request, log into <a href=\"https://www.yakimawa.gov/apps/ycadmin/edit.php?id=$requestID\">Yak Back Admin</a></strong></p>";
				$email_message .= "<p><strong><u>Type</u></strong>: $type </p>\n";
				$email_message .= "<p><strong><u>Location</u></strong>: $location</p>\n";
				$email_message .= "<p><strong><u>Description</u></strong>: $description</p>\n"; 
				$email_message .= "<p><strong><u>Reported By</u></strong>: $user </p>\n"; 
				if($filename != ''){
					$email_message .="<p><strong>Photo</strong>:<br/> <img src=\"http://www.yakimawa.gov/./uimages/$filename\" alt=\"report image\" style=\"width: 50%\"></p>";
				}
				
				$email_message .= "</body></html>";
				
				// create email headers
				$headers = "From: " . $from . "\r\n";
				$headers .= "Reply-To: ". $from . "\r\n";
				$headers .= "Bcc: ". $bcc . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
				mail($to, $subject, $email_message, $headers); 
			
		}
		/* a clean script is a happy script */
		mysqli_close($link);
	}else {
	  header('Location: /yak-back');
	}
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
	<link rel="stylesheet" href="./includes/css/style.css" />
</head> 
<body> 

<!-- Start of first page: #one -->
<div data-role="page" id="one" data-theme="b">
	<div data-role="header">
		
		<h1>Report</h1>

	</div><!-- /header -->

	<div data-role="content" >	

		<p>Thank You, your request has been submitted. The details of your request are as follows:</p>
		<ul>
			<li><strong>Type of Report</strong>: <?php echo $type; ?></li>	
			<li><strong>Location</strong>: <?php echo $location; ?></li>	
			<li><strong>Description</strong>: <?php echo $description; ?></li>	
			
			<li><strong>Reported By</strong>: <?php echo $user; ?></li>	
			<?php 
				if($filename != ''){
					echo "<li><strong>Photo</strong>:<br/> <img src=\"/./uimages/$filename\" alt=\"report image\" style=\"width:50%;\"></li>";
				}
			?>
		</ul>
		<a href="./myreports.php" data-role="button" data-prefetch="false" data-dom-cache="false">View My Reports</a>
		<a href="./" data-icon="home" data-role="button">Yak Back Home Page</a>
		
	</div><!-- /content -->
	
	<div data-role="footer" data-theme="d" data-position="fixed">
		<h4>&copy; 2012 City of Yakima</h4>
	</div><!-- /footer -->
</div><!-- /page one -->
<script src="/includes/js/ga.js"></script>
</body>
</html>
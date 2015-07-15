<?php 
	header('Content-type: application/json');

	require_once($_SERVER['DOCUMENT_ROOT'] ."/yak-back/includes/php/include.php");
	ini_set('display_errors', 1);
	$link = ConnectToDBi('yakimaconnect');
	if(isset($_GET["eventid"]) && $_GET["eventid"] != ''){
		$eventid = filter_var($_GET["eventid"], FILTER_VALIDATE_INT);
		$sql = "SELECT 	r.id AS id, 
										location, 
										photo, 
										description, 
										dateOpened, 
										dateClosed,
										status , 
										latitude, 
										longitude, 
										assignedTo, 
										dateAssigned,
										Name, 
										t.id AS typeID
							FROM `requests` r, `requestType` t
							WHERE r.typeID = t.ID
							AND r.id = $eventid
							AND r.source = 'app'
							ORDER BY dateOpened DESC
							LIMIT 0 , 1
						 ";
	}
	else if(isset($_GET["userid"]) && $_GET["userid"] != ''){
		$userid = filter_var($_GET["userid"], FILTER_VALIDATE_INT);
		$sql = "SELECT 	r.id as id,
										location, 
									 	photo, 
									 	description, 
									 	dateOpened, 
									 	dateClosed, 
									 	status, 
									 	latitude, 
									 	longitude, 
									 	assignedTo,
									 	dateAssigned,
									 	Name, 
									 	firstName, 
									 	lastName, 
									 	email, 
									 	phone,  
									 	t.id as typeID, 
									 	u.id as userID
						FROM `requests` r, `requestType` t, `user` u
						WHERE r.typeID = t.ID
						AND r.userID = u.ID
						AND u.id = $userid
						AND r.source = 'app'
						ORDER BY dateOpened DESC
						LIMIT 0 , 30
						";
	}
	else {
		$sql = "SELECT r.id,location,photo,description,dateOpened,dateClosed,status,typeID,userID,closedBy,latitude,longitude,assignedTo,dateAssigned,Name
						FROM `requests` r, `requestType` t
						WHERE r.typeID = t.ID
						AND r.source = 'app'
						ORDER BY dateOpened DESC
						LIMIT 0 , 30
						";
	}
	$result = mysqli_query($link, $sql);

	$array = "";
	while($row = mysqli_fetch_assoc($result)){
		$rows[] = $row;
	}
	echo json_encode($rows);
	
	/* close connection */
	mysqli_close($link);
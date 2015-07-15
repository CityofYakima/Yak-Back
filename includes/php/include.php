<?php

function ConnectToDBi($db){
  /*********************************
		ConnectToDBi();
		Description: mysql connection to web
		$db					- the database you want to connect to
		Returns a mysqli link resource
	*********************************/
	
	$link = mysqli_connect('HOST','USER','PASS',$db);
	return $link;
}

{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$image1 = post_to_string("image1");
	$image2 = post_to_string("image2");
	$image3 = post_to_string("image3");

	$msg = "";
	$valid = true;

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		//-----------------------------------------------------------------
		//open connection
	
		$conn = db_open_conn();
	
		//----------------------------
		//update trip
	
		$sql = "update trips set ";
		$sql .= " image1='".$conn->real_escape_string($image1)."'";
		$sql .= ", image2='".$conn->real_escape_string($image2)."'";
		$sql .= ", image3='".$conn->real_escape_string($image3)."'";
		$sql .= " where trip_id=".$trip_id;
	
		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------
		//close connection
	
		$conn->close();

	}

	//----------------------------
	//pass back the trip details

	print "\"msg\":\"".$msg."\",";
	print "\"exn\":false,";
	print "\"valid\":".($valid ? "true" : "false");

} catch (Exception $e) {

		print "\"exn\":true,";
		print "\"msg\":\"Exn: {$e->getMessage()}\"";

}

?>
}
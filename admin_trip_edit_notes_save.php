{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$trip_notes = post_to_string("notes");

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
		$sql .= " trip_notes='".$conn->real_escape_string($trip_notes)."'";
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
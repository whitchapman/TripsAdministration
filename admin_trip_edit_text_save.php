{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$trip_description = post_to_string("description");
	$bullets = post_to_string("bullets");

	$msg = "";
	$valid = true;

	$bullets_array = trim_array(explode("\n", $bullets));
	$bullets_count = count($bullets_array);

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else if (strlen($trip_description) == 0) {
		$msg = "Enter Description, can not be blank.";
		$valid = false;
	} else if (strlen($bullets) == 0) {
		$msg = "Enter Bullets, can not be blank.";
		$valid = false;
	} else if ($bullets_count == 0) {
		$msg = "Enter Bullets, can not have blank lines.";
		$valid = false;
	} else {

		//-----------------------------------------------------------------
		//open connection
	
		$conn = db_open_conn();
	
		//----------------------------
		//update trip
	
		$sql = "update trips set ";
		$sql .= " trip_description='".$conn->real_escape_string($trip_description)."'";
		$sql .= " where trip_id=".$trip_id;
	
		db_exec_query($conn, $sql);
	
		//----------------------------
		//update bullets

		//remove blank lines and trim every line
		$sql = "delete from trip_bullets where trip_id=".$trip_id;
	  db_exec_query($conn, $sql);

		$sql = "insert into trip_bullets (trip_id, order_key, create_time, bullet_text) values";
		
		for ($i = 0; $i < $bullets_count; $i++) {
			$bullet = trim($bullets_array[$i]);
			if ($i > 0) {
				$sql .= ",";
			}
			$sql .= " (".$trip_id.", ".($i+1).", now(), '".$conn->real_escape_string($bullet)."')";
		}

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
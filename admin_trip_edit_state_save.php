{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$trip_state = post_to_string("trip_state");
	$max_capacity = post_to_string("max_capacity");
	$max_waitlist = post_to_string("max_waitlist");
	$num_confirmed = post_to_string("num_confirmed");
	$num_signups = post_to_string("num_signups");

	$msg = "";
	$valid = true;

	$trip_state_result = "";
	$max_capacity_result = "";
	$max_waitlist_result = "";
	$num_confirmed_result = "";
	$num_signups_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		if ((!is_numeric($trip_state)) || (intval($trip_state) < -1) || (intval($trip_state) > 7)) {
			$msg = "Correct errors below:";
			$trip_state_result = "Invalid value.";
			$valid = false;
		}

		if ((!is_numeric($max_capacity)) || (intval($max_capacity) < 0)) {
			$msg = "Correct errors below:";
			$max_capacity_result = "Invalid number.";
			$valid = false;
		}

		if ((!is_numeric($max_waitlist)) || (intval($max_waitlist) < 0)) {
			$msg = "Correct errors below:";
			$max_waitlist_result = "Invalid number.";
			$valid = false;
		}

		if ((!is_numeric($num_confirmed)) || (intval($num_confirmed) < 0)) {
			$msg = "Correct errors below:";
			$num_confirmed_result = "Invalid number.";
			$valid = false;
		}

		if ((!is_numeric($num_signups)) || (intval($num_signups) < 0)) {
			$msg = "Correct errors below:";
			$num_signups_result = "Invalid number.";
			$valid = false;
		}
		
		if ($valid) {

			if ($max_waitlist <= $max_capacity) {
				$msg = "Correct errors below:";
				$max_waitlist_result = "Needs to be greater than Max Capacity.";
				$valid = false;
			}
			
			if ($valid) {

					//-----------------------------------------------------------------
					//open connection
				
					$conn = db_open_conn();
				
					//----------------------------
					//update trip
				
					$sql = "update trips set ";
					$sql .= " trip_state=".$trip_state;
					$sql .= ", max_capacity=".$max_capacity;
					$sql .= ", max_waitlist=".$max_waitlist;
					$sql .= ", num_confirmed=".$num_confirmed;
					$sql .= ", num_signups=".$num_signups;
					$sql .= " where trip_id=".$trip_id;
				
					db_exec_query($conn, $sql);
				
					//-----------------------------------------------------------------
					//close connection

					$conn->close();

			}
		}
	}

	//----------------------------
	//pass back the trip details


	if ($valid) {

		print "\"trip_state\":\"".trip_state_to_string($trip_state)."\",";
		print "\"max_capacity\":".$max_capacity.",";
		print "\"max_waitlist\":".$max_waitlist.",";
		print "\"num_confirmed\":".$num_confirmed.",";
		print "\"num_signups\":".$num_signups.",";

	} else {

		print "\"trip_state_result\":\"".$trip_state_result."\",";
		print "\"max_capacity_result\":\"".$max_capacity_result."\",";
		print "\"max_waitlist_result\":\"".$max_waitlist_result."\",";
		print "\"num_confirmed_result\":\"".$num_confirmed_result."\",";
		print "\"num_signups_result\":\"".$num_signups_result."\",";
	}

	print "\"msg\":\"".$msg."\",";
	print "\"exn\":false,";
	print "\"valid\":".($valid ? "true" : "false");

} catch (Exception $e) {

		print "\"exn\":true,";
		print "\"msg\":\"Exn: {$e->getMessage()}\"";

}

?>
}
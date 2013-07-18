{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$full_payment_date_str = post_to_string("full_payment_date");
	$start_date_str = post_to_string("start_date");
	$end_date_str = post_to_string("end_date");
	
	$msg = "";
	$valid = true;

	$full_payment_date_result = "";
	$start_date_result = "";
	$end_date_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		if (!($full_payment_date = strtotime($full_payment_date_str))){
			$msg = "Correct errors below:";
			$full_payment_date_result = "Invalid date";
			$valid = false;
		}

		if (!($start_date = strtotime($start_date_str))){
			$msg = "Correct errors below:";
			$start_date_result = "Invalid date";
			$valid = false;
		}

		if (!($end_date = strtotime($end_date_str))){
			$msg = "Correct errors below:";
			$end_date_result = "Invalid date";
			$valid = false;
		}
		
		if ($valid) {

			if ($start_date < $full_payment_date) {
				$msg = "Correct errors below:";
				$start_date_result = "Needs to be later than Full Payment Date.";
				$valid = false;
			}

			if ($end_date < $start_date) {
				$msg = "Correct errors below:";
				$end_date_result = "Needs to be later than Start Date.";
				$valid = false;
			}
			
			if ($valid) {

					//-----------------------------------------------------------------
					//open connection
				
					$conn = db_open_conn();
				
					//----------------------------
					//update trip
				
					$sql = "update trips set ";
					$sql .= " full_payment_date='".date("Y-m-d", $full_payment_date)."'";
					$sql .= ", start_date='".date("Y-m-d", $start_date)."'";
					$sql .= ", end_date='".date("Y-m-d", $end_date)."'";
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

		print "\"full_payment_date\":\"".$full_payment_date_str."\",";
		print "\"start_date\":".json_encode($start_date_str).",";
		print "\"end_date\":".json_encode($end_date_str).",";

	} else {

		print "\"full_payment_date_result\":\"".$full_payment_date_result."\",";
		print "\"start_date_result\":\"".$start_date_result."\",";
		print "\"end_date_result\":\"".$end_date_result."\",";
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
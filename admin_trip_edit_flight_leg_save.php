{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$order_key = post_to_string("subkey");

	$flight_number = post_to_string("flight_number");
	$departure_airport = post_to_string("departure_airport");
	$departure_time_str = post_to_string("departure_time");
	$arrival_airport = post_to_string("arrival_airport");
	$arrival_time_str = post_to_string("arrival_time");

	$msg = "";
	$valid = true;

	$flight_number_result = "";
	$departure_airport_result = "";
	$departure_time_result = "";
	$arrival_airport_result = "";
	$arrival_time_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else if ((!is_numeric($order_key)) || (intval($order_key) < 1)) {
		$msg = "Invalid Key.";
		$valid = false;
	} else {

		if (strlen($flight_number) == 0) {
			$msg = "Correct errors below:";
			$flight_number_result = "Enter Flight Number, can not be blank.";
			$valid = false;
		}

		if (strlen($departure_airport) == 0) {
			$msg = "Correct errors below:";
			$departure_airport_result = "Enter Departure Airport, can not be blank.";
			$valid = false;
		}

		if (strlen($arrival_airport) == 0) {
			$msg = "Correct errors below:";
			$arrival_airport_result = "Enter Arrival Airport, can not be blank.";
			$valid = false;
		}

		//-----------------------------------------------------------------
		//validate times

		$valid_dates = true;
		if (!($departure_time = strtotime($departure_time_str))){
			$departure_time_result = "Invalid time";
			$valid_dates = false;
			$valid = false;
		}

		if (!($arrival_time = strtotime($arrival_time_str))){
			$arrival_time_result = "Invalid time";
			$valid_dates = false;
			$valid = false;
		}
		
		if ($valid_dates) {
			if ($arrival_time < $departure_time) {
				$arrival_time_result = "Needs to be later than Departure Time.";
				$valid = false;
			}
		}

		if ($valid) {

				//-----------------------------------------------------------------
				//open connection
			
				$conn = db_open_conn();
			
				//----------------------------
				//delete/insert trip option (handles both adds and edits)

				$sql = "delete from flight_legs ";
				$sql .= " where trip_id=".$trip_id;
				$sql .= " and order_key=".$order_key;

				db_exec_query($conn, $sql);

				$sql = "insert into flight_legs (trip_id, order_key, create_time, flight_number, departure_airport, departure_time, arrival_airport, arrival_time) values";
				$sql .= " (".$trip_id.", ".$order_key.", now()";
				$sql .= ", '".$conn->real_escape_string($flight_number)."'";
				$sql .= ", '".$conn->real_escape_string($departure_airport)."'";
				$sql .= ", '".date("Y-m-d H:i", $departure_time)."'";
				$sql .= ", '".$conn->real_escape_string($arrival_airport)."'";
				$sql .= ", '".date("Y-m-d H:i", $arrival_time)."')";
		
				db_exec_query($conn, $sql);
			
				//-----------------------------------------------------------------
				//close connection

				$conn->close();

		}
	}

	//----------------------------
	//pass back the trip details


	if (!$valid) {

		print "\"flight_number_result\":".json_encode($flight_number_result).",";
		print "\"departure_airport_result\":".json_encode($departure_airport_result).",";
		print "\"departure_time_result\":".json_encode($departure_time_result).",";
		print "\"arrival_airport_result\":".json_encode($arrival_airport_result).",";
		print "\"arrival_time_result\":".json_encode($arrival_time_result).",";
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
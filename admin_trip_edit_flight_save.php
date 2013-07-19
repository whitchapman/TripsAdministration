{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$flight_action = post_to_string("flight_action");
	$airline_id = post_to_string("airline");
	$airline_name = post_to_string("airline_name");
	$airline_action = post_to_string("airline_action");
	$flight_release_date_str = post_to_string("flight_release_date");
	$ticketing_date_str = post_to_string("ticketing_date");

	$msg = "";
	$valid = true;
	$insert_new_airline = ($airline_action == "new");

	$airline_existing_result = "";
	$airline_new_result = "";
	$flight_release_date_result = "";
	$ticketing_date_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		//-----------------------------------------------------------------
		//open connection
	
		$conn = db_open_conn();

		//-----------------------------------------------------------------

		if ($flight_action == "no") {

			$sql = "select count(*) num_flight_legs";
			$sql .= " from flight_legs";
			$sql .= " where trip_id=".$trip_id;
		  $result = db_exec_query($conn, $sql);
		
		  if ($row = $result->fetch_assoc()) {
		  	$num_flight_legs = $row["num_flight_legs"];
				if ($num_flight_legs > 0) {
					$msg = "Go back and manually delete ALL Flight Legs before deleting the Flight.";
					$valid = false;
				}
		  }
		
			$result->close();

			if ($valid) {

				//----------------------------
				//update trip
			
				$sql = "delete from trip_flights";
				$sql .= " where trip_id=".$trip_id;
			
				db_exec_query($conn, $sql);
			}

		} else {

			//-----------------------------------------------------------------
			//validate airline
			
			if ($insert_new_airline) {
		
				if (strlen($airline_name) == 0) {
					$airline_new_result = "Enter an Airline.";
					$valid = false;
				} else {
		
					//verify airline does not already exist
		
					$sql = "select airline_id";
					$sql .= " from airlines";
					$sql .= " where airline_name='".$conn->real_escape_string($airline_name)."'";
					$result = db_exec_query($conn, $sql);
				
					if ($row = $result->fetch_assoc()) {
						$airline_new_result = "Airline [".$airline_name."] already exists.";
						$valid = false;
					}
		
					$result->close();
				}
		
			} else {
		
				if (strlen($airline_id) == 0) {
					$airline_existing_result = "Select an Airline.";
					$valid = false;
				} else if (!is_numeric($airline_id)) {
					$airline_existing_result = "Airline [".$airline_id."] is invalid.";
					$valid = false;
				} else if ($airline_id == 0) {
					$airline_existing_result = "Select an Airline.";
					$valid = false;
				} else {
			
					//verify airline exists
			
					$sql = "select airline_id, airline_name";
					$sql .= " from airlines";
					$sql .= " where airline_id=".$airline_id;
					$result = db_exec_query($conn, $sql);
				
					if ($row = $result->fetch_assoc()) {
						$airline_name = $row["airline_name"];
					} else {
						$airline_existing_result = "Airline [".$airline_id."] does not exist.";
						$valid = false;
					}
				
					$result->close();
				}
			}

			//-----------------------------------------------------------------
			//validate dates

			$valid_dates = true;
			if (!($flight_release_date = strtotime($flight_release_date_str))){
				$flight_release_date_result = "Invalid date";
				$valid_dates = false;
				$valid = false;
			}
	
			if (!($ticketing_date = strtotime($ticketing_date_str))){
				$ticketing_date_result = "Invalid date";
				$valid_dates = false;
				$valid = false;
			}
			
			if ($valid_dates) {
				if ($ticketing_date < $flight_release_date) {
					$ticketing_date_result = "Needs to be later than Flight Release Date.";
					$valid = false;
				}
			}

			if ($valid) {

				//----------------------------
				//insert new airline
	
				if ($insert_new_airline) {
		
					$sql = "insert into airlines";
					$sql .= " (create_time, airline_name) values (now()";
					$sql .= ", '".$conn->real_escape_string($airline_name)."'";
					$sql .= ")";
					db_exec_query($conn, $sql);
	
					$airline_id = $conn->insert_id;
					print "\"new_airline\":\"".$sirline_id."\",";
				}

				//----------------------------
				//update trip

				$sql = "select trip_id";
				$sql .= " from trip_flights";
				$sql .= " where trip_id=".$trip_id;
			  $result = db_exec_query($conn, $sql);

				if ($row = $result->fetch_assoc()) {
					$flight_exists = true;
				} else {
					$flight_exists = false;
				}
			
				$result->close();

				if ($flight_exists) {

					$sql = "update trip_flights set ";
					$sql .= " airline_id=".$airline_id;
					$sql .= ", flight_release_date='".date("Y-m-d", $flight_release_date)."'";
					$sql .= ", ticketing_date='".date("Y-m-d", $ticketing_date)."'";
					$sql .= " where trip_id=".$trip_id;
				
					db_exec_query($conn, $sql);

				} else {

					$sql = "insert into trip_flights (trip_id, create_time, airline_id, flight_release_date, ticketing_date) values";
					$sql .= " (".$trip_id.", now(), ".$airline_id;
					$sql .= ", '".date("Y-m-d", $flight_release_date)."'";
					$sql .= ", '".date("Y-m-d", $ticketing_date)."')";

					db_exec_query($conn, $sql);
				}

			} else {

				//----------------------------
				//pass back the trip details
	
				$msg = "Correct the errors below.";
				print "\"airline_existing_result\":\"".$airline_existing_result."\",";
				print "\"airline_new_result\":\"".$airline_new_result."\",";
				print "\"flight_release_date_result\":\"".$flight_release_date_result."\",";
				print "\"ticketing_date_result\":\"".$ticketing_date_result."\",";
			}
		}

		//-----------------------------------------------------------------
		//close connection

		$conn->close();

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
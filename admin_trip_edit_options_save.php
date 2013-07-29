{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$trip_price = post_to_string("trip_price");
	$include_single_room = post_to_string("include_single_room");
	$single_room_price = post_to_string("single_room_price");

	$msg = "";
	$valid = true;

	$trip_price_result = "";
	$single_room_price_result= "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		if ((!is_numeric($trip_price)) || (intval($trip_price) <= 0)) {
			$msg = "Correct errors below:";
			$trip_price_result = "Invalid price, must be greater than zero.";
			$valid = false;
		}

		if ($include_single_room != "yes") {
			$include_single_room = 0;
		} else {
			//including single room
			$include_single_room = 1;

			if ((!is_numeric($single_room_price)) || (intval($single_room_price) <= 0)) {
				$msg = "Correct errors below:";
				$single_room_price_result = "Invalid price, must be greater than zero.";
				$valid = false;
			}
		}

		if ($valid) {

				//-----------------------------------------------------------------
				//open connection

				$conn = db_open_conn();

				//----------------------------
				//update trip

				$sql = "update trips set ";
				$sql .= " trip_price=".$trip_price;
				$sql .= ", include_single_room=".$include_single_room;
				$sql .= ", single_room_price=".$single_room_price;
				$sql .= " where trip_id=".$trip_id;

				db_exec_query($conn, $sql);

				//-----------------------------------------------------------------
				//close connection

				$conn->close();

		}
	}

	//----------------------------
	//pass back the trip details


	if ($valid) {

		print "\"trip_price\":".$trip_price.",";

	} else {

		print "\"trip_price_result\":\"".$trip_price_result."\",";
		print "\"single_room_price_result\":\"".$single_room_price_result."\",";
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
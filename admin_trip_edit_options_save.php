{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$trip_price = post_to_string("trip_price");

	$msg = "";
	$valid = true;

	$trip_price_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else {

		if ((!is_numeric($trip_price)) || (intval($trip_price) < 0)) {
			$msg = "Correct errors below:";
			$trip_price_result = "Invalid price.";
			$valid = false;
		}
		
		if ($valid) {

				//-----------------------------------------------------------------
				//open connection
			
				$conn = db_open_conn();
			
				//----------------------------
				//update trip
			
				$sql = "update trips set ";
				$sql .= " trip_price=".$trip_price;
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
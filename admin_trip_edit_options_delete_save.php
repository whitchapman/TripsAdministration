{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$order_key = post_to_string("subkey");

	$msg = "";
	$valid = true;

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else if ((!is_numeric($order_key)) || (intval($order_key) < 1)) {
		$msg = "Invalid Key.";
		$valid = false;
	} else {

		//-----------------------------------------------------------------
		//open connection
	
		$conn = db_open_conn();
		
		//-----------------------------------------------------------------
		//check if trip option actually exists

		$sql = "select order_key";
		$sql .= " from trip_options";
		$sql .= " where trip_id=".$trip_id;
		$sql .= " and order_key=".$order_key;
	  $result = db_exec_query($conn, $sql);
	
	  if (!($row = $result->fetch_assoc())) {
			$msg = "Trip Option does not Exist!";
			$valid = false;
	  }
	
		$result->close();

		if ($valid) {

			//----------------------------
			//delete trip option

			$sql = "delete from trip_options ";
			$sql .= " where trip_id=".$trip_id;
			$sql .= " and order_key=".$order_key;

			db_exec_query($conn, $sql);
			
		}

		//-----------------------------------------------------------------
		//close connection

		$conn->close();

	}

	//----------------------------
	//pass back the trip details


//	if ($valid) {
//
//		print "\"option_text\":".json_encode($option_text).",";
//		print "\"option_price\":".$option_price.",";
//
//	} else {
//
//		print "\"option_text_result\":".json_encode($option_text_result).",";
//		print "\"option_price_result\":".json_encode($option_price_result).",";
//	}

	print "\"msg\":\"".$msg."\",";
	print "\"exn\":false,";
	print "\"valid\":".($valid ? "true" : "false");

} catch (Exception $e) {

		print "\"exn\":true,";
		print "\"msg\":\"Exn: {$e->getMessage()}\"";

}

?>
}
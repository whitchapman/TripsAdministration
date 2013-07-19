{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$trip_id = post_to_string("trip");
	$order_key = post_to_string("subkey");
	$option_text = post_to_string("option_text");
	$option_price = post_to_string("option_price");

	$msg = "";
	$valid = true;

	$option_text_result = "";
	$option_price_result = "";

	if ((!is_numeric($trip_id)) || (intval($trip_id) < 1)) {
		$msg = "Invalid Trip.";
		$valid = false;
	} else if ((!is_numeric($order_key)) || (intval($order_key) < 1)) {
		$msg = "Invalid Key.";
		$valid = false;
	} else {

		if (strlen($option_text) == 0) {
			$msg = "Correct errors below:";
			$option_text_result = "Enter Option Text, can not be blank.";
			$valid = false;
		}

		if (!is_numeric($option_price)) {
			$msg = "Correct errors below:";
			$option_price_result = "Invalid price.";
			$valid = false;
		}
		
		if ($valid) {

				//-----------------------------------------------------------------
				//open connection
			
				$conn = db_open_conn();
			
				//----------------------------
				//delete/insert trip option (handles both adds and edits)

				$sql = "delete from trip_options ";
				$sql .= " where trip_id=".$trip_id;
				$sql .= " and order_key=".$order_key;

				db_exec_query($conn, $sql);

				$sql = "insert into trip_options (trip_id, order_key, create_time, option_text, option_price) values";
				$sql .= " (".$trip_id.", ".$order_key.", now(), '".$conn->real_escape_string($option_text)."', ".$option_price.")";
		
				db_exec_query($conn, $sql);
			
				//-----------------------------------------------------------------
				//close connection

				$conn->close();

		}
	}

	//----------------------------
	//pass back the trip details


	if ($valid) {

		print "\"option_text\":".json_encode($option_text).",";
		print "\"option_price\":".$option_price.",";

	} else {

		print "\"option_text_result\":".json_encode($option_text_result).",";
		print "\"option_price_result\":".json_encode($option_price_result).",";
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
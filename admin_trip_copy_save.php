{
<?php

	include "check_login.php";

	//-----------------------------------------------------------------

	$new_season_id = post_to_string("new_season");
	$old_trip_id = post_to_string("trip");

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------
	//verify posted ids are valid

	$new_season_id_is_valid = false;
	$old_trip_id_is_valid = false;

	if (is_numeric($new_season_id)) {

		//verify new season exists

		$sql = "select season_id";
		$sql .= " from seasons";
		$sql .= " where season_id=".$new_season_id;
		$result = db_exec_query($conn, $sql);
	
		if ($row = $result->fetch_assoc()) {
			$new_season_id_is_valid = true;
		}
	
		$result->close();
	}

	//-----------------------------------------------------------------

	if (($new_season_id_is_valid) && (is_numeric($old_trip_id))) {

		//load current trip's season_id

		$sql = "select season_id, start_date, site_name";
		$sql .= " from vw_trips";
		$sql .= " where trip_id=".$old_trip_id;
		$result = db_exec_query($conn, $sql);

		if ($row = $result->fetch_assoc()) {
			$old_season_id = $row["season_id"];
			$old_start_date = strtotime($row["start_date"]);
			$old_trip_year = date("Y", $old_start_date);
			$old_site_name = $row["site_name"];

			$old_trip_id_is_valid = true;

			//verify not copying to the trip's current season
			$new_season_id_is_valid = ($old_season_id != $new_season_id);
		}
		
		$result->close();
	}

	//-----------------------------------------------------------------
	
	if (!$new_season_id_is_valid) {

		print "\"valid\":false,";
		print "\"msg\":\"invalid season [{$new_season_id}]\"";

	} else if (!$old_trip_id_is_valid) {

		print "\"valid\":false,";
		print "\"msg\":\"invalid trip [{$old_trip_id}]\"";

	} else {

		$season_diff = $new_season_id - $old_season_id;

		//-----------------------------------------------------------------
		
		//use defaults for trip_id (auto), last_updated, and trip_state (CALC)
		//TODO: assign edit_state indicating "copied" (probably = 1)

		$sql = "insert into trips";
		$sql .= " select null, now(), null, null, null, ".$new_season_id;
		$sql .= ", date_add(full_payment_date, interval ".$season_diff." year)";
		$sql .= ", date_add(start_date, interval ".$season_diff." year)";
		$sql .= ", date_add(end_date, interval ".$season_diff." year)";
		$sql .= build_fields_sql($conn, "trips", 10);
		$sql .= " from trips";
		$sql .= " where trip_id=".$old_trip_id;
		db_exec_query($conn, $sql);

		$new_trip_id = $conn->insert_id;

		//-----------------------------------------------------------------

		$sql = "insert into trip_options";
		$sql .= " select ".$new_trip_id.", order_key, now(), null, option_text, option_price";
		$sql .= " from trip_options";
		$sql .= " where trip_id=".$old_trip_id;
		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------

		$sql = "insert into trip_bullets";
		$sql .= " select ".$new_trip_id.", order_key, now(), null, bullet_text";
		$sql .= " from trip_bullets";
		$sql .= " where trip_id=".$old_trip_id;
		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------
		//doesn't make sense to copy flight which will always be different from year to year

		$new_trip_year = $old_trip_year + $season_diff;
		$trip_str = $old_site_name." ".$new_trip_year;

		print "\"valid\":true,";
		print "\"msg\":\"".$trip_str."\",";
		print "\"season_diff\":\"".($season_diff > 0 ? "+" : "").$season_diff."\",";
		print "\"new_trip\":\"".$new_trip_id."\"";
		
	}

	//-----------------------------------------------------------------
	//close connection

	$conn->close();

?>
}
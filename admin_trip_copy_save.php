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
		
		//TODO: may want to do something different with trip_state

		//use defaults for trip_id (auto), last_updated, and trip_state (CALC)
		$sql = "insert into trips";
		$sql .= " select null, now(), null, null, ".$new_season_id;
		$sql .= ", date_add(full_payment_date, interval ".$season_diff." year)";
		$sql .= ", date_add(start_date, interval ".$season_diff." year)";
		$sql .= ", date_add(end_date, interval ".$season_diff." year)";
		$sql .= build_fields_sql($conn, "trips", 9);
		$sql .= " from trips";
		$sql .= " where trip_id=".$old_trip_id;
		db_exec_query($conn, $sql);

		$new_trip_id = $conn->insert_id;

		//-----------------------------------------------------------------
		//broken - doesn't make sense to copy flight which will always be different from year to year

//		$sql = "insert into trip_flights";
//		$sql .= " select ".$new_trip_id.", now(), null";
//		$sql .= ", date_add(flight_release_date, interval 1 year)";
//		$sql .= ", date_add(ticketing_date, interval 1 year)";
//		$sql .= build_fields_sql($conn, "trip_flights", 6);
//		$sql .= " from trip_flights";
//		$sql .= " where trip_id=".$old_trip_id;
//		db_exec_query($conn, $sql);

//		$sql = "insert into flight_legs";
//		$sql .= " select ".$new_trip_id.", order_key, now(), null";
//		$sql .= ", date_add(departure_time, interval 1 year)";
//		$sql .= ", date_add(arrival_time, interval 1 year)";
//		$sql .= build_fields_sql($conn, "flight_legs", 7);
//		$sql .= " from flight_legs";
//		$sql .= " where trip_id=".$old_trip_id;
//		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------

		$sql = "insert into trip_bullets";
		$sql .= " select ".$new_trip_id.", order_key, now(), null, bullet_text";
		$sql .= " from trip_bullets";
		$sql .= " where trip_id=".$old_trip_id;
		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------

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
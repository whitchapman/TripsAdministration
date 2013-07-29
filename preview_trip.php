<?php

	include "../utils/global.php";

  //-----------------------------------------------------------------

	//use REQUEST so can accept GET or POST
	$trip_id = key_to_string($_REQUEST, "trip");
	
	if (array_key_exists("trip_state_override", $_REQUEST)) {
		$trip_state_override = trim($_REQUEST["trip_state_override"]);
	} else {
		$trip_state_override = "";
	}

  //-----------------------------------------------------------------
	//look up season_id from trip_id

	$season_id = "";
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);

		$conn = db_open_conn();

		$sql = "select season_id";
		$sql .= " from vw_trips where trip_id=".$trip_id;
		$sql .= " order by start_date, end_date";
	  $result = db_exec_query($conn, $sql);
	
		if ($row = $result->fetch_assoc()) {
			$season_id = $row["season_id"];
		}

		$result->close();
		$conn->close();
	}

  //-----------------------------------------------------------------

	$trips_href = "preview_season.php?season=".$season_id;
	$trip_href = "preview_trip.php";

	include "../main/trip.php";
	
?>

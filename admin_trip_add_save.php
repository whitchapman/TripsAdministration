{
<?php

	$throw_errors = true;
	include "check_login.php";

	//-----------------------------------------------------------------

try {
	
	$season_id = post_to_string("season");
	$site_id = post_to_string("site");
	$site_name = post_to_string("site_name");
	$site_action = post_to_string("site_action");
	$trip_title = post_to_string("trip_title");
	$trip_leader = post_to_string("trip_leader");

	$valid = true;
	$insert_new_site = ($site_action == "new");

	$season_result = "";
	$site_existing_result = "";
	$site_new_result = "";
	$title_result = "";
	$trip_leader_result = "";

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------
	//validate season

	if (strlen($season_id) == 0) {
		$season_result = "Select a Season.";
		$valid = false;
	} else if (!is_numeric($season_id)) {
		$season_result = "Season [".$season_id."] is invalid.";
		$valid = false;
	} else {

		//verify season exists

		$sql = "select season_id";
		$sql .= " from seasons";
		$sql .= " where season_id=".$season_id;
		$result = db_exec_query($conn, $sql);
	
		if (!($row = $result->fetch_assoc())) {
			$season_result = "Season [".$season_id."] does not exist.";
			$valid = false;
		}
	
		$result->close();
	}

	//-----------------------------------------------------------------
	//validate site
	
	if ($insert_new_site) {

		if (strlen($site_name) == 0) {
			$site_new_result = "Enter a Site.";
			$valid = false;
		} else {

			//verify site does not already exist

			$sql = "select site_id";
			$sql .= " from sites";
			$sql .= " where site_name='".$site_name."'";
			$result = db_exec_query($conn, $sql);
		
			if ($row = $result->fetch_assoc()) {
				$site_new_result = "Site [".$site_name."] already exists.";
				$valid = false;
			}

			$result->close();
		}

	} else {

		if (strlen($site_id) == 0) {
			$site_existing_result = "Select a Site.";
			$valid = false;
		} else if (!is_numeric($site_id)) {
			$site_existing_result = "Site [".$site_id."] is invalid.";
			$valid = false;
		} else {
	
			//verify site exists
	
			$sql = "select site_id, site_name";
			$sql .= " from sites";
			$sql .= " where site_id=".$site_id;
			$result = db_exec_query($conn, $sql);
		
			if ($row = $result->fetch_assoc()) {
				$site_name = $row["site_name"];
			} else {
				$site_existing_result = "Site [".$site_id."] does not exist.";
				$valid = false;
			}
		
			$result->close();
		}
	}

	//-----------------------------------------------------------------
	//validate title

	if (strlen($trip_title) == 0) {
		$title_result = "Enter a Title.";
		$valid = false;
	}

	//-----------------------------------------------------------------
	//validate trip_leader
	//TODO: also check if the user has access to assign this trip_leader

	if (strlen($trip_leader) == 0) {
		$trip_leader_result = "Select a Trip leader.";
		$valid = false;
	} else if (!is_numeric($trip_leader)) {
		$trip_leader_result = "Trip leader [".$trip_leader."] is invalid.";
		$valid = false;
	} else {

		//verify trip_leader exists

		$sql = "select member_id";
		$sql .= " from trip_leaders";
		$sql .= " where member_id=".$trip_leader;
		$result = db_exec_query($conn, $sql);
	
		if (!($row = $result->fetch_assoc())) {
			$trip_leader_result = "Trip leader [".$trip_leader."] does not exist.";
			$valid = false;
		}
	
		$result->close();
	}

	//-----------------------------------------------------------------

		if ($valid) {

			//----------------------------
			//insert new site

			if ($insert_new_site) {
	
				$sql = "insert into sites";
				$sql .= " (create_time, site_name, trip_title) values (now()";
				$sql .= ", '".$site_name."'";
				$sql .= ", '".$trip_title."'";
				$sql .= ")";
				db_exec_query($conn, $sql);

				$site_id = $conn->insert_id;
				print "\"new_site\":\"".$site_id."\",";
			}

			//----------------------------
			//insert new trip

			$full_payment_date = ($season_id-1)."-11-01";
			$start_date = $season_id."-01-01";
			$end_date = $season_id."-01-08";

			$sql = "insert into trips";
			$sql .= " (create_time, season_id, full_payment_date, start_date, end_date,";
			$sql .= " site_id, trip_leader, trip_title, trip_description) values (now()";
			$sql .= ", ".$season_id;
			$sql .= ", '".$full_payment_date."'";
			$sql .= ", '".$start_date."'";
			$sql .= ", '".$end_date."'";
			$sql .= ", ".$site_id;
			$sql .= ", ".$trip_leader;
			$sql .= ", '".$trip_title."'";
			$sql .= ", ''";
			$sql .= ")";

			db_exec_query($conn, $sql);

			$trip_id = $conn->insert_id;
			print "\"new_trip\":\"".$trip_id."\",";

			//----------------------------
			//insert default trip bullets

			//site name
			//link to hotel

			//----------------------------
			//insert default trip options

			//land only
			//single room

			//----------------------------
			//pass back the trip details

			$msg = $site_name." ".$season_id;
			print "\"msg\":\"".$msg."\",";

		} else {
			print "\"msg\":\"Correct the errors above.\",";
			print "\"season_result\":\"".$season_result."\",";
			print "\"site_existing_result\":\"".$site_existing_result."\",";
			print "\"site_new_result\":\"".$site_new_result."\",";
			print "\"title_result\":\"".$title_result."\",";
			print "\"trip_leader_result\":\"".$trip_leader_result."\",";
		}

		print "\"exn\":false,";
		print "\"valid\":".($valid ? "true" : "false");

		//print "\"season_diff\":\"".($season_diff > 0 ? "+" : "").$season_diff."\",";

	//-----------------------------------------------------------------
	//close connection

	$conn->close();

} catch (Exception $e) {

		print "\"exn\":true,";
		print "\"msg\":\"Exn: {$e->getMessage()}\"";

}

?>
}
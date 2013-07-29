<?php

	include "check_login.php";

	//accept optional trip_id so can set displayed season
	$trip_id = post_to_string("trip");

	//-----------------------------------------------------------------

	$settings = load_settings();
	$current_season_id = $settings["season_id"];

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

  //-----------------------------------------------------------------

	$selected_season_id = "";
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);

		$sql = "select season_id";
		$sql .= " from vw_trips";
		$sql .= " where trip_id=".$trip_id;
	  $result = db_exec_query($conn, $sql);
	
		if ($row = $result->fetch_assoc()) {
			$selected_season_id = $row["season_id"];
		}
	
	  $result->close();
	}

	//default selection
	if (!is_numeric($selected_season_id)) {
		$selected_season_id = $current_season_id;
	}

  //-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from vw_trips";
	$sql .= " order by season_id desc, start_date, end_date";
  $result = db_exec_query($conn, $sql);

	$seasons = array();
	while ($row = $result->fetch_assoc()) {
		$season_id = $row["season_id"];
		if (!array_key_exists($season_id, $seasons)) {
			$seasons[$season_id] = array();
		}
		$seasons[$season_id][] = $row;
	}

  $result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="tabbable">
	<ul class="nav nav-tabs">
<?php

	$length = count($seasons);
	$keys  = array_keys($seasons);
	for ($i = 0; $i < $length; $i++) {
		$season_id = $keys[$i];
		$is_selected_season = ($season_id == $selected_season_id);
		print "    <li".($is_selected_season ? " class=\"active\"" : "")."><a href=\"#tabs_seasons_panel".$season_id."\" data-toggle=\"tab\">".$season_id."</a></li>\n";
	}

?>
	</ul>
	<div class="tab-content">
<?php

	for ($i = 0; $i < $length; $i++) {
		$season_id = $keys[$i];
		$is_current_season = ($season_id == $current_season_id);
		$is_selected_season = ($season_id == $selected_season_id);
		$trips = $seasons[$season_id];

		print "<div id=\"tabs_seasons_panel".$season_id."\" class=\"tab-pane".($is_selected_season ? " active" : "")."\">\n";
?>
<ul class="inline">
	<li><b>Season <?php print $season_id; ?></b></li>
	<li>
		<div class="btn-group">
			<a class="btn btn-small" href="preview_season.php?season=<?php print $season_id; ?>" target="_blank">Preview Season</a>
			<a class="btn btn-small" href="#" onclick="load_modal_trip_add(<?php print $season_id; ?>, ''); return false;">Add Trip</a>
		</div>
	</li>
	<li><span class="label label-inverse"><?php print ($is_current_season ? "Current Season" : ""); ?></span></li>
</ul>
<table class="table table-bordered"><tbody>
	<tr>
		<th>Site</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>Trip Leader</th>
		<th>Price</th>
		<th>State</th>
		<th>Trip Actions</th>
	</tr>
<?php

		foreach ($trips as $row) {
			$trip_id = $row["trip_id"];
			$site_name = htmlentities($row["site_name"]);
			$full_payment_date = strtotime($row["full_payment_date"]);
			$start_date = strtotime($row["start_date"]);
			$end_date = strtotime($row["end_date"]);
			$trip_leader_name = $row["trip_leader_name"];
			$trip_price = sprintf ("%.0f", $row["trip_price"]);
			$trip_state = $row["trip_state"];
			$max_capacity = $row["max_capacity"];
			$max_waitlist = $row["max_waitlist"];

			$num_confirmed = 25;
			$num_signups = 100;

			$start_date_str = date("n/j/Y", $start_date);
			$end_date_str = date("n/j/Y", $end_date);
			$trip_state_label = create_trip_state_label($trip_state, $full_payment_date, $start_date, $end_date, $max_capacity, $max_waitlist, $num_confirmed, $num_signups);

			print "<tr>";
			print "<td>{$site_name}</td>";
			print "<td>{$start_date_str}</td>";
			print "<td>{$end_date_str}</td>";
			print "<td>{$trip_leader_name}</td>";
			print "<td>\${$trip_price}</td>";
			print "<td>{$trip_state_label}</td>";
			print "<td><div class=\"btn-group\">";
			print "<a class=\"btn btn-small\" href=\"preview_trip.php?trip={$trip_id}\" target=\"_blank\">Preview</a>";
			print "<a class=\"btn btn-small\" href=\"#\" onclick=\"load_trip_edit({$trip_id}); return false;\">Edit</a>";
			print "<a class=\"btn btn-small\" href=\"#\" onclick=\"load_modal_trip_copy({$trip_id}); return false;\">Copy</a>";
			print "</div></td>";
			print "</tr>";
		}

		print "</tbody></table>";
		print "</div>";
	}

?>
	</div><!-- tab-content -->
</div><!-- tabbable -->

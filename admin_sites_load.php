<?php

	include "check_login.php";
	
	//accept optional trip_id so can set displayed site
	$trip_id = post_to_string("trip");

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

  //-----------------------------------------------------------------

	//TODO: change this to load all sites - how put site_id and site_name in the array?

	$sql = "select *";
	$sql .= " from vw_trips";
	$sql .= " order by site_name, season_id, start_date, end_date";
  $result = db_exec_query($conn, $sql);

	$sites = array();
	while ($row = $result->fetch_assoc()) {
		$site_id = $row["site_id"];
		if (!array_key_exists($site_id, $sites)) {
			$sites[$site_id] = array();
		}
		$sites[$site_id][] = $row;
	}

  $result->close();

	//-----------------------------------------------------------------

	$selected_site_id = "";
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);

		$sql = "select site_id";
		$sql .= " from vw_trips";
		$sql .= " where trip_id=".$trip_id;
	  $result = db_exec_query($conn, $sql);
	
		if ($row = $result->fetch_assoc()) {
			$selected_site_id = $row["site_id"];
		}
	
	  $result->close();
	}

	//default selection
	if (!is_numeric($selected_site_id)) {
		$keys  = array_keys($sites);
		$selected_site_id = $keys[0];
	}

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

	//-----------------------------------------------------------------

?>
<div class="alert alert-info">
	<ul class="inline">
		<li>
			<span>Site Name: </span>
		</li>
		<li>
		<select id="site_names_select">
			<?php
				foreach ($sites as $trips) {
					$site_id = $trips[0]["site_id"];
					$site_name = $trips[0]["site_name"];
					print "<option value=\"{$site_id}\"".($site_id == $selected_site_id ? " selected" : "").">{$site_name}</option>";
				}
			?>
		</select>
		</li>
	</ul>
</div>
<div class="tabbable">
	<div style="display:none;">
		<ul id="site_name_tab" class="nav nav-tabs">
			<?php
				foreach ($sites as $trips) {
					$site_id = $trips[0]["site_id"];
					print "<li><a href=\"#tabs_sites_panel".$site_id."\" data-toggle=\"tab\"></a></li>";
				}
			?>
		</ul>
	</div>
	<div class="tab-content">
<?php

	foreach ($sites as $trips) {
		$num_trips = count($trips);
		$site_id = $trips[0]["site_id"];
		$site_name = $trips[0]["site_name"];

		print "<div id=\"tabs_sites_panel".$site_id."\" class=\"tab-pane".($site_id == $selected_site_id ? " active" : "")."\">\n";

?>
		<ul class="inline">
			<li><b><?php print $site_name; ?></b></li>
			<li><span class="label label-inverse"><?php print $num_trips." Trip".($num_trips == 1 ? "" : "s"); ?></span></li>
			<li>
				<div class="btn-group">
					<a class="btn btn-small" href="#" onclick="load_modal_trip_add('', <?php print $site_id; ?>); return false;">Add Trip</a>
				</div>
			</li>
		</ul>
		<table class="table table-bordered"><tbody>
			<tr>
				<th>Season</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Trip Leader</th>
				<th>Price</th>
				<th>State</th>
				<th>Trip Actions</th>
			</tr>
<?php

		foreach ($trips as $row) {
			$season_id = $row["season_id"];
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
			print "<td>{$season_id}</td>";
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
		<script type="text/javascript">

			function show_sites_tab(site_id) {
				$('#site_name_tab a[href="#tabs_sites_panel'+site_id+'"]').tab('show');
			}

			$("#site_names_select").change(function() {
				var site_id = $("#site_names_select").val();
				show_sites_tab(site_id);
			});

		</script>
	</div><!-- tab-content -->
</div><!-- tabbable -->

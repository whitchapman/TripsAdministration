<?php

	include "check_login.php";

	//accept optional trip_id so can set displayed season
	$trip_id = post_to_string("trip");
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);
	}

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------

	$sql = "select season_id";
	$sql .= " from seasons";
	$sql .= " order by season_id desc";
	$result = db_exec_query($conn, $sql);

	$season_ids = array();
	while ($row = $result->fetch_assoc()) {
		$season_ids[] = $row["season_id"];
	}

	$result->close();

	//-----------------------------------------------------------------

	$sql = "select site_id, site_name, trip_title";
	$sql .= " from sites";
	$sql .= " order by site_name";
	$result = db_exec_query($conn, $sql);

	$sites = array();
	while ($row = $result->fetch_assoc()) {
		$sites[] = $row;
	}

	$result->close();

	//-----------------------------------------------------------------

	$sql = "select member_id, full_name";
	$sql .= " from vw_trip_leaders";
	$sql .= " order by full_name";
	$result = db_exec_query($conn, $sql);

	$trip_leaders = array();
	while ($row = $result->fetch_assoc()) {
		$trip_leaders[] = $row;
	}

	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select airline_id, airline_name";
//	$sql .= " from airlines";
//	$sql .= " order by airline_name";
//	$result = db_exec_query($conn, $sql);
//
//	$airlines = array();
//	while ($row = $result->fetch_assoc()) {
//		$airlines[] = $row;
//	}
//
//	$result->close();

  //-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from vw_trips";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);
	
	if ($row = $result->fetch_assoc()) {
		$site_name = $row["site_name"];
		$trip_leader_name = $row["trip_leader_name"];
		$edit_state = $row["edit_state"];
		$trip_state = $row["trip_state"];
		$season_id = $row["season_id"];
		$full_payment_date = strtotime($row["full_payment_date"]);
		$start_date = strtotime($row["start_date"]);
		$end_date = strtotime($row["end_date"]);
		$site_id = $row["site_id"];
		$trip_leader = $row["trip_leader"];
		$trip_title = htmlentities($row["trip_title"]);
		$trip_price = sprintf ("%.0f", $row["trip_price"]);
		$max_capacity = $row["max_capacity"];
		$max_waitlist = $row["max_waitlist"];
		$num_confirmed = $row["num_confirmed"];
		$num_signups = $row["num_signups"];
		$image1 = $row["image1"];
		$image2 = $row["image2"];
		$image3 = $row["image3"];
		$trip_description = $row["trip_description"];
		$trip_notes = $row["trip_notes"];

		$trip_year = date("Y", $start_date);
		$full_payment_date = date("m/d/Y", $full_payment_date);
		$start_date = date("m/d/Y", $start_date);
		$end_date = date("m/d/Y", $end_date);

		$trip_str = $site_name." ".$trip_year;
		$trip_state_label = create_trip_state_label($trip_state, $full_payment_date, $start_date, $end_date, $max_capacity, $max_waitlist, $num_confirmed, $num_signups);
	}
	
	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select bullet_text";
//	$sql .= " from trip_bullets where trip_id=".$trip_id;
//	$sql .= " order by order_key";
//  $result = db_exec_query($conn, $sql);
//
//	$bullets = array();
//  while ($row = $result->fetch_assoc()) {
//		$bullets[] = $row;
//  }
//
//	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select *";
//	$sql .= " from vw_trip_flights";
//	$sql .= " where trip_id=".$trip_id;
//  $result = db_exec_query($conn, $sql);
//
//	$flight = array();
//	if ($row = $result->fetch_assoc()) {
//		$flight = $row;
//		//$airline_id = $flight["airline_id"];
//		//$airline_name = $flight["airline_name"];
//		//$flight_release_date = strtotime($flight["flight_release_date"]);
//		//$ticketing_date = strtotime($flight["ticketing_date"]);
//	}
//
//	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select *";
//	$sql .= " from flight_legs where trip_id=".$trip_id;
//	$sql .= " order by departure_time";
//  $result = db_exec_query($conn, $sql);
//
//	$flight_legs = array();
//  while ($row = $result->fetch_assoc()) {
//		$flight_legs[] = $row;
//  }
//
//	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info" id="trip_edit_season">
	<label class="control-label" for="trip_edit_season_select">Season</label>
	<div class="controls">
		<select id="trip_edit_season_select">
			<?php
				foreach ($season_ids as $row_season_id) {
					print "<option".($row_season_id == $season_id ? " selected" : "").">{$row_season_id}</option>";
				}
			?>
		</select>
		<span id="trip_edit_season_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info">
	<div class="controls">
		<label class="radio inline">
			<input type="radio" name="trip_edit_site_radio" id="trip_edit_site_radio_existing" value="existing" checked>
			Choose Existing Site
		</label>
		<label class="radio inline">
			<input type="radio" name="trip_edit_site_radio" id="trip_edit_site_radio_new" value="new">
			Add New Site
		</label>
	</div>
</div>

<div class="tabbable">
	<div style="display:none;">
		<ul class="nav nav-tabs">
			<li><a id="trip_edit_site_link_existing" href="#trip_edit_site_panel_existing" data-toggle="tab"></a></li>
			<li><a id="trip_edit_site_link_new" href="#trip_edit_site_panel_new" data-toggle="tab"></a></li>
		</ul>
	</div>
	<div class="tab-content">
		<div id="trip_edit_site_panel_existing" class="tab-pane active">

			<div class="control-group info" id="trip_edit_site_existing">
				<label class="control-label" for="trip_edit_site_existing_select">Site</label>
				<div class="controls">
					<select id="trip_edit_site_existing_select">
						<?php
							foreach ($sites as $row) {
								$row_site_id = $row["site_id"];
								$row_site_name = $row["site_name"];
								$row_trip_title = $row["trip_title"];
								print "<option value=\"{$row_site_id}\" trip_title=\"{$row_trip_title}\"".($site_id == $row_site_id ? " selected" : "").">{$row_site_name}</option>";
							}
						?>
					</select>
					<span id="trip_edit_site_existing_result" class="help-inline">Each Site has a default trip Title.</span>
				</div>
			</div>

		</div><!-- tab-pane -->
		<div id="trip_edit_site_panel_new" class="tab-pane">

			<div class="control-group info" id="trip_edit_site_new">
				<label class="control-label" for="trip_edit_site_new_input">Site</label>
				<div class="controls">
					<input type="text" id="trip_edit_site_new_input" placeholder="New Site">
					<span id="trip_edit_site_new_result" class="help-inline">Trip Title defaults to the new Site.</span>
				</div>
			</div>

		</div><!-- tab-pane -->
	</div><!-- tab-content -->
</div><!-- tabbable -->

<div class="control-group info" id="trip_edit_title">
	<label class="control-label" for="trip_edit_title_input">Title</label>
	<div class="controls">
		<input type="text" id="trip_edit_title_input" value="<?php print $trip_title; ?>">
		<span id="trip_edit_title_result" class="help-inline">Add a little more detail than the Site Name.</span>
	</div>
</div>

<div class="control-group info" id="trip_edit_trip_leader">
	<label class="control-label" for="trip_edit_trip_leader_select">Trip Leader</label>
	<div class="controls">
		<select id="trip_edit_trip_leader_select">
			<?php
				foreach ($trip_leaders as $row) {
					$row_member_id = $row["member_id"];
					$row_full_name = $row["full_name"];
					print "<option value=\"{$row_member_id}\"".($trip_leader == $row_member_id ? " selected" : "").">{$row_full_name}</option>";
				}
			?>
		</select>
		<span id="trip_edit_trip_leader_result" class="help-inline">New Trip Leaders will be added by request.</span>
	</div>
</div>

<script type="text/javascript">

	$("#trip_edit_site_radio_existing").click(function() {
		$("#trip_edit_title_input").val($("#trip_edit_site_existing_select :selected").attr("trip_title"));
		$("#trip_edit_site_link_existing").tab("show");
	});

	$("#trip_edit_site_radio_new").click(function() {
		$("#trip_edit_site_new_input").val("");
		$("#trip_edit_title_input").val("");
		$("#trip_edit_site_link_new").tab("show");
	});

	$("#trip_edit_site_existing_select").change(function() {				
		$("#trip_edit_title_input").val($("#trip_edit_site_existing_select :selected").attr("trip_title"));
	});

	$("#trip_edit_site_new_input").blur(function() {
		$("#trip_edit_title_input").val($("#trip_edit_site_new_input").val());
	});

	function save_section_general() {
		var valid_edit = true;

		var season = $("#trip_edit_season_select").val();
		var site = $("#trip_edit_site_existing_select").val();
		var site_name = $("#trip_edit_site_new_input").val();

		var site_action = $("input[name=trip_edit_site_radio]:checked").val();

		var trip_title = $("#trip_edit_title_input").val();
		var trip_leader = $("#trip_edit_trip_leader_select").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_general_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					season: season,
					site: site,
					site_name: site_name,
					site_action: site_action,
					trip_title: trip_title,
					trip_leader: trip_leader
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_general_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_general_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_general_result").html(json.msg);
 						show_result(json.season_result, "trip_edit_season");
						show_result(json.site_existing_result, "trip_edit_site_existing");
						show_result(json.site_new_result, "trip_edit_site_new");
						show_result(json.title_result, "trip_edit_title");
						show_result(json.trip_leader_result, "trip_edit_trip_leader");
					} else {
						$("#trip_edit_trip_str").html(json.msg);
						$("#trip_edit_season_view").val(json.season);
						$("#trip_edit_site_view").val(json.site);
						$("#trip_edit_title_view").val(json.title);
						$("#trip_edit_trip_leader_view").val(json.trip_leader);
						cancel_section('general');

						//add_alert("success", "Trip Section Saved.");

						//changes made require reload of seasons and sites tabs
						reload_seasons = true;
						reload_sites = true;
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_general_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

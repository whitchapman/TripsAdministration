<?php

	include "check_login.php";

	$trip_id = post_to_string("trip");
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);
	}

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

  //-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from vw_trips";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);
	
	if ($row = $result->fetch_assoc()) {
		$trip_state = $row["trip_state"];
		$max_capacity = $row["max_capacity"];
		$max_waitlist = $row["max_waitlist"];
		$num_confirmed = $row["num_confirmed"];
		$num_signups = $row["num_signups"];
	}
	
	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info" id="trip_edit_trip_state">
	<label class="control-label" for="trip_edit_trip_state_select">State</label>
	<div class="controls">
		<select id="trip_edit_trip_state_select">
			<?php
				for ($i = -1; $i < 8; $i++) {
					$trip_state_str = trip_state_to_string($i);
					print "<option".($i == $trip_state ? " selected" : "")." value=\"{$i}\">{$trip_state_str}</option>";
				}
			?>
		</select>
		<span id="trip_edit_trip_state_result" class="help-inline">Leave on Calculate unless you know what you're doing.</span>
	</div>
</div>

<div class="control-group info" id="trip_edit_max_capacity">
	<label class="control-label" for="trip_edit_max_capacity_input">Max Capacity</label>
	<div class="controls">
		<input type="number" id="trip_edit_max_capacity_input" value="<?php print $max_capacity; ?>">
		<span id="trip_edit_max_capacity_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_max_waitlist">
	<label class="control-label" for="trip_edit_max_waitlist_input">Max Waitlist</label>
	<div class="controls">
		<input type="number" id="trip_edit_max_waitlist_input" value="<?php print $max_waitlist; ?>">
		<span id="trip_edit_max_waitlist_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_num_confirmed">
	<label class="control-label" for="trip_edit_num_confirmed_input">Num Confirmed</label>
	<div class="controls">
		<input type="number" id="trip_edit_num_confirmed_input" value="<?php print $num_confirmed; ?>">
		<span id="trip_edit_num_confirmed_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_num_signups">
	<label class="control-label" for="trip_edit_num_signups_input">Num Signups</label>
	<div class="controls">
		<input type="number" id="trip_edit_num_signups_input" value="<?php print $num_signups; ?>">
		<span id="trip_edit_num_signups_result" class="help-inline"></span>
	</div>
</div>
<script type="text/javascript">

	function save_section_state() {
		var valid_edit = true;

		var trip_state = $("#trip_edit_trip_state_select").val();
		var max_capacity = $("#trip_edit_max_capacity_input").val();
		var max_waitlist = $("#trip_edit_max_waitlist_input").val();
		var num_confirmed = $("#trip_edit_num_confirmed_input").val();
		var num_signups = $("#trip_edit_num_signups_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_state_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					trip_state: trip_state,
					max_capacity: max_capacity,
					max_waitlist: max_waitlist,
					num_confirmed: num_confirmed,
					num_signups: num_signups
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_state_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_state_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_state_result").html(json.msg);

						show_result(json.trip_state_result, "trip_edit_trip_state");
						show_result(json.max_capacity_result, "trip_edit_max_capacity");
						show_result(json.max_waitlist_result, "trip_edit_max_waitlist");
						show_result(json.num_confirmed_result, "trip_edit_num_confirmed");
						show_result(json.num_signups_result, "trip_edit_num_signups");

					} else {

						$("#trip_edit_trip_state_view").val(json.trip_state);
						$("#trip_edit_max_capacity_view").val(json.max_capacity);
						$("#trip_edit_max_waitlist_view").val(json.max_waitlist);
						$("#trip_edit_num_confirmed_view").val(json.num_confirmed);
						$("#trip_edit_num_signups_view").val(json.num_signups);

						cancel_section('state');

						//changes made require reload of seasons and sites tabs
						reload_seasons = true;
						reload_sites = true;
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_state_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

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

	$sql = "select airline_id, airline_name";
	$sql .= " from airlines";
	$sql .= " order by airline_name";
	$result = db_exec_query($conn, $sql);

	$airlines = array();
	while ($row = $result->fetch_assoc()) {
		$airlines[] = $row;
	}

	$result->close();

  //-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from vw_trip_flights";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$flight_exists = false;

	if ($row = $result->fetch_assoc()) {
		$flight_exists = true;

		$airline_id = $row["airline_id"];
		$airline_name = $row["airline_name"];
		$flight_release_date = date("n/j/Y", strtotime($row["flight_release_date"]));
		$ticketing_date = date("n/j/Y", strtotime($row["ticketing_date"]));

		$include_land_only = ($row["include_land_only"] == 1);
		$land_only_deduction = sprintf ("%.0f", $row["land_only_deduction"]);

		$flight_release_date_str = $flight_release_date;
		$ticketing_date_str = $ticketing_date;
	} else {
		$airline_id = 0;
		$flight_release_date = date("n/j/Y");
		$flight_release_date_str = "";
		$ticketing_date = date("n/j/Y");
		$ticketing_date_str = "";

		$include_land_only = true;
		$land_only_deduction = "";
	}

	$result->close();

	//-----------------------------------------------------------------

	$sql = "select count(*) num_flight_legs";
	$sql .= " from flight_legs";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$num_flight_legs = 0;
	if ($row = $result->fetch_assoc()) {
		$num_flight_legs = $row["num_flight_legs"];
	}
	
	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info">
	<div class="controls">
		<label class="checkbox inline">
			<input type="checkbox" name="trip_edit_flight_checkbox" id="trip_edit_flight_checkbox" value="yes"<?php print ($flight_exists ? " checked" : ""); ?>>
<?php if ($flight_exists) { ?>
				Uncheck to Delete this Flight.
<?php } else { ?>
				Add a Flight for this Trip.
<?php } ?>
		</label>
	</div>
</div>

<div class="tabbable">
	<div style="display:none;">
		<ul class="nav nav-tabs">
			<li><a id="trip_edit_flight_link_no" href="#trip_edit_flight_panel_no" data-toggle="tab"></a></li>
			<li><a id="trip_edit_flight_link_yes" href="#trip_edit_flight_panel_yes" data-toggle="tab"></a></li>
		</ul>
	</div>
	<div class="tab-content">
		<div id="trip_edit_flight_panel_no" class="tab-pane<?php print (!$flight_exists ? " active" : ""); ?>">

		</div><!-- tab-pane -->
		<div id="trip_edit_flight_panel_yes" class="tab-pane<?php print ($flight_exists ? " active" : ""); ?>">

			<div class="control-group info">
				<div class="controls">
					<label class="radio inline">
						<input type="radio" name="trip_edit_airline_radio" id="trip_edit_airline_radio_existing" value="existing" checked>
						Choose Existing Airline
					</label>
					<label class="radio inline">
						<input type="radio" name="trip_edit_airline_radio" id="trip_edit_airline_radio_new" value="new">
						Add New Airline
					</label>
				</div>
			</div>

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_airline_link_existing" href="#trip_edit_airline_panel_existing" data-toggle="tab"></a></li>
						<li><a id="trip_edit_airline_link_new" href="#trip_edit_airline_panel_new" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_airline_panel_existing" class="tab-pane active">
			
						<div class="control-group info" id="trip_edit_airline_existing">
							<label class="control-label" for="trip_edit_airline_existing_select">Airline</label>
							<div class="controls">
								<select id="trip_edit_airline_existing_select">
									<option value="0"></option>
									<?php
										foreach ($airlines as $row) {
											$row_airline_id = $row["airline_id"];
											$row_airline_name = $row["airline_name"];
											print "<option value=\"{$row_airline_id}\"".($airline_id == $row_airline_id ? " selected" : "").">{$row_airline_name}</option>";
										}
									?>
								</select>
								<span id="trip_edit_airline_existing_result" class="help-inline"></span>
							</div>
						</div>
			
					</div><!-- tab-pane -->
					<div id="trip_edit_airline_panel_new" class="tab-pane">
			
						<div class="control-group info" id="trip_edit_airline_new">
							<label class="control-label" for="trip_edit_airline_new_input">Airline</label>
							<div class="controls">
								<input type="text" id="trip_edit_airline_new_input" placeholder="New Airline">
								<span id="trip_edit_airline_new_result" class="help-inline"></span>
							</div>
						</div>
			
					</div><!-- tab-pane -->
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			<div class="control-group info" id="trip_edit_flight_release_date">
				<label class="control-label" for="trip_edit_flight_release_date_input">Flight Release Date</label>
				<div class="controls">
					<div class="input-append date" id="dp4" data-date="<?php print $flight_release_date; ?>" data-date-format="m/d/yyyy">
					  <input type="text" id="trip_edit_flight_release_date_input" value="<?php print $flight_release_date_str; ?>" readonly>
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>
					<span id="trip_edit_flight_release_date_result" class="help-inline"></span>
				</div>
			</div>

			<div class="control-group info" id="trip_edit_ticketing_date">
				<label class="control-label" for="trip_edit_ticketing_date_input">Ticketing Date</label>
				<div class="controls">
					<div class="input-append date" id="dp5" data-date="<?php print $ticketing_date; ?>" data-date-format="m/d/yyyy">
					  <input type="text" id="trip_edit_ticketing_date_input" value="<?php print $ticketing_date_str; ?>" readonly>
						<span class="add-on"><i class="icon-calendar"></i></span>
					</div>
					<span id="trip_edit_ticketing_date_result" class="help-inline"></span>
				</div>
			</div>

			<div class="control-group info" id="trip_edit_land_only_deduction">
				<label class="control-label" for="trip_edit_land_only_deduction_input">Land Only Deduction</label>
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="trip_edit_land_only_deduction_checkbox" id="trip_edit_land_only_deduction_checkbox" value="yes"<?php print ($include_land_only ? " checked" : ""); ?>>
							Include as a trip option
					</label>
					<input type="number" id="trip_edit_land_only_deduction_input" value="<?php print $land_only_deduction; ?>" class="<?php print ($include_land_only ? '' : 'hidden'); ?>">
					<span id="trip_edit_land_only_deduction_result" class="help-inline"></span>
				</div>
			</div>

		</div><!-- tab-pane -->
	</div><!-- tab-content -->
</div><!-- tabbable -->

<script type="text/javascript">

	var dp4 = $("#dp4").datepicker();
	var dp5 = $("#dp5").datepicker();
	
	dp4.on("changeDate", function() {
		dp4.datepicker("hide");
	});

	dp5.on("changeDate", function() {
		dp5.datepicker("hide");
	});

	$("#trip_edit_flight_checkbox").click(function() {
		if ($("input[name=trip_edit_flight_checkbox]:checked").length == 0) {
			$("#trip_edit_flight_link_no").tab("show");
<?php if ($flight_exists) { ?>
			$(".save-btn").html("Delete");
<?php if ($num_flight_legs > 0) { ?>
			$("#trip_edit_flight_result").html("For safety, can't delete Flight until deleting all Flight Legs.");
<?php } } else { ?>
			$(".save-btn").html("Save");
<?php } ?>
		} else {
			$("#trip_edit_flight_link_yes").tab("show");
<?php if ($flight_exists) { ?>
			$(".save-btn").html("Save");
			$("#trip_edit_flight_result").html("");
<?php } else { ?>
			$(".save-btn").html("Add");
<?php } ?>
		}
	});

	$("#trip_edit_airline_radio_existing").click(function() {
		$("#trip_edit_airline_link_existing").tab("show");
	});

	$("#trip_edit_airline_radio_new").click(function() {
		$("#trip_edit_airline_new_input").val("");
		$("#trip_edit_airline_link_new").tab("show");
	});

	$("#trip_edit_land_only_deduction_checkbox").click(function() {
		if ($("input[name=trip_edit_land_only_deduction_checkbox]:checked").length == 0) {
			$("#trip_edit_land_only_deduction_input").addClass("hidden");
		} else {
			$("#trip_edit_land_only_deduction_input").removeClass("hidden");
		}
	});

	function save_section_flight() {
		var valid_edit = true;

		var flight_action = "yes";
		if ($("input[name=trip_edit_flight_checkbox]:checked").length == 0) {
			flight_action = "no";
		}

		var airline = $("#trip_edit_airline_existing_select").val();
		var airline_name = $("#trip_edit_airline_new_input").val();

		var airline_action = $("input[name=trip_edit_airline_radio]:checked").val();

		var flight_release_date = $("#trip_edit_flight_release_date_input").val();
		var ticketing_date = $("#trip_edit_ticketing_date_input").val();

		var include_land_only = "yes";
		if ($("input[name=trip_edit_land_only_deduction_checkbox]:checked").length == 0) {
			include_land_only = "no";
		}

		var land_only_deduction = $("#trip_edit_land_only_deduction_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_flight_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					flight_action: flight_action,
					airline: airline,
					airline_name: airline_name,
					airline_action: airline_action,
					flight_release_date: flight_release_date,
					ticketing_date: ticketing_date,
					include_land_only: include_land_only,
					land_only_deduction: land_only_deduction
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_flight_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_flight_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_flight_result").html(json.msg);
						show_result(json.airline_existing_result, "trip_edit_airline_existing");
						show_result(json.airline_new_result, "trip_edit_airline_new");
						show_result(json.flight_release_date_result, "trip_edit_flight_release_date");
						show_result(json.ticketing_date_result, "trip_edit_ticketing_date");
						show_result(json.land_only_deduction_result, "trip_edit_land_only_deduction");
					} else {
						//changing flight affect land only option
						view_section("options");

						//reload view (flight could be removed or added which requires a reload)
						view_section("flight");
						cancel_section('flight');
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_flight_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

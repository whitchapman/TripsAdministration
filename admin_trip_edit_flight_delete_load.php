<?php

	include "check_login.php";

	$trip_id = post_to_string("trip");
	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);
	}
	
	$order_key = post_to_string("subkey");
	if (is_numeric($order_key)) {
		$order_key = intval($order_key);
	}
	
	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

  //-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from flight_legs";
	$sql .= " where trip_id=".$trip_id;
	$sql .= " and order_key=".$order_key;
  $result = db_exec_query($conn, $sql);
	
	if ($row = $result->fetch_assoc()) {
		$flight_number = $row["flight_number"];
		$departure_airport = $row["departure_airport"];
		$departure_date = date("n/j/Y", strtotime($row["departure_time"]));
		$departure_time = date("g:i a", strtotime($row["departure_time"]));
		$arrival_airport = $row["arrival_airport"];
		$arrival_date = date("n/j/Y", strtotime($row["arrival_time"]));
		$arrival_time = date("g:i a", strtotime($row["arrival_time"]));
	} else {
		$flight_number = "";
		$departure_airport = "";
		$departure_date = "";
		$departure_time = "";
		$arrival_airport = "";
		$arrival_date = "";
		$arrival_time = "";
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group">
	<label class="control-label" for="trip_edit_flight_number_view">Flight Number</label>
	<div class="controls">
		<input type="text" id="trip_edit_flight_number_view" value="<?php print $flight_number; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_departure_airport_view">Departure Airport</label>
	<div class="controls">
		<input type="text" id="trip_edit_departure_airport_view" value="<?php print $departure_airport; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_departure_date_view">Departure Date</label>
	<div class="controls">
		<input type="text" id="trip_edit_departure_date_view" value="<?php print $departure_date; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_departure_time_view">Departure Time</label>
	<div class="controls">
		<input type="text" id="trip_edit_departure_time_view" value="<?php print $departure_time; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_arrival_airport_view">Arrival Airport</label>
	<div class="controls">
		<input type="text" id="trip_edit_arrival_airport_view" value="<?php print $arrival_airport; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_arrival_date_view">Arrival Date</label>
	<div class="controls">
		<input type="text" id="trip_edit_arrival_date_view" value="<?php print $arrival_date; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_arrival_time_view">Arrival Time</label>
	<div class="controls">
		<input type="text" id="trip_edit_arrival_time_view" value="<?php print $arrival_time; ?>" readonly>
	</div>
</div>

<script type="text/javascript">
	
	$(".save-btn").html("Delete");
	$("#trip_edit_flight_result").html("Verify that you want to Delete this Flight Leg.");

	function save_section_flight() {
		var valid_edit = true;

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_flight_delete_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					subkey: <?php print $order_key; ?>
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
					} else {
						//reload view
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

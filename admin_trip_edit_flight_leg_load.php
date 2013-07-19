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
		$departure_time = date("n/j/Y  g:i a", strtotime($row["departure_time"]));
		$arrival_airport = $row["arrival_airport"];
		$arrival_time = date("n/j/Y  g:i a", strtotime($row["arrival_time"]));

		$departure_time_str = $departure_time;
		$arrival_time_str = $arrival_time;
	} else {
		$flight_number = "";
		$departure_airport = "";
		$departure_time = date("n/j/Y  g:i a");
		$arrival_airport = "";
		$arrival_time = date("n/j/Y  g:i a");

		$departure_time_str = "";
		$arrival_time_str = "";
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info" id="trip_edit_flight_number">
	<label class="control-label" for="trip_edit_flight_number_input">Flight Number</label>
	<div class="controls">
		<input type="text" id="trip_edit_flight_number_input" value="<?php print $flight_number; ?>">
		<span id="trip_edit_flight_number_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_departure_airport">
	<label class="control-label" for="trip_edit_departure_airport_input">Departure Airport</label>
	<div class="controls">
		<input type="text" id="trip_edit_departure_airport_input" value="<?php print $departure_airport; ?>">
		<span id="trip_edit_departure_airport_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_departure_time">
	<label class="control-label" for="trip_edit_departure_time_input">Departure Time</label>
	<div class="controls">
		<div class="input-append date form_datetime" id="dp6" data-date="<?php print $departure_time; ?>">
			<input type="text" id="trip_edit_departure_time_input" value="<?php print $departure_time_str; ?>" readonly>
			<span class="add-on"><i class="icon-th"></i></span>
		</div>
		<span id="trip_edit_departure_time_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_arrival_airport">
	<label class="control-label" for="trip_edit_arrival_airport_input">Arrival Airport</label>
	<div class="controls">
		<input type="text" id="trip_edit_arrival_airport_input" value="<?php print $arrival_airport; ?>">
		<span id="trip_edit_arrival_airport_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_arrival_time">
	<label class="control-label" for="trip_edit_arrival_time_input">Arrival Time</label>
	<div class="controls">
		<div class="input-append date form_datetime" id="dp7" data-date="<?php print $arrival_time; ?>">
			<input type="text" id="trip_edit_arrival_time_input" value="<?php print $arrival_time_str; ?>" readonly>
			<span class="add-on"><i class="icon-th"></i></span>
		</div>
		<span id="trip_edit_arrival_time_result" class="help-inline"></span>
	</div>
</div>

<script type="text/javascript">

	$("#dp6").datetimepicker({
		format: "m/d/yyyy  H:ii p",
		showMeridian: true,
		autoclose: true,
		pickerPosition: "bottom-right",
		minuteStep: 1,
		initialDate: "<?php print date('n/j/Y  g:i a'); ?>"
	});

	$("#dp7").datetimepicker({
		format: "m/d/yyyy  H:ii p",
		showMeridian: true,
		autoclose: true,
		pickerPosition: "top-right",
		minuteStep: 1,
		initialDate: "<?php print date('n/j/Y  g:i a'); ?>"
	});

	function save_section_flight() {
		var valid_edit = true;

		var flight_number = $("#trip_edit_flight_number_input").val();
		var departure_airport = $("#trip_edit_departure_airport_input").val();
		var departure_time = $("#trip_edit_departure_time_input").val();
		var arrival_airport = $("#trip_edit_arrival_airport_input").val();
		var arrival_time = $("#trip_edit_arrival_time_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_flight_leg_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					subkey: <?php print $order_key; ?>,
					flight_number: flight_number,
					departure_airport: departure_airport,
					departure_time: departure_time,
					arrival_airport: arrival_airport,
					arrival_time: arrival_time
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

						show_result(json.flight_number_result, "trip_edit_flight_number");
						show_result(json.departure_airport_result, "trip_edit_departure_airport");
						show_result(json.departure_time_result, "trip_edit_departure_time");
						show_result(json.arrival_airport_result, "trip_edit_arrival_airport");
						show_result(json.arrival_time_result, "trip_edit_arrival_time");

					} else {
						//reload the view
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

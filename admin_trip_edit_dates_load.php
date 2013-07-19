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
		$full_payment_date = strtotime($row["full_payment_date"]);
		$start_date = strtotime($row["start_date"]);
		$end_date = strtotime($row["end_date"]);

		$full_payment_date = date("n/j/Y", $full_payment_date);
		$start_date = date("n/j/Y", $start_date);
		$end_date = date("n/j/Y", $end_date);
	}
	
	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info" id="trip_edit_full_payment_date">
	<label class="control-label" for="trip_edit_full_payment_date_input">Full Payment Date</label>
	<div class="controls">

		<div class="input-append date" id="dp1" data-date="<?php print $full_payment_date; ?>" data-date-format="m/d/yyyy">
			<input type="text" id="trip_edit_full_payment_date_input" value="<?php print $full_payment_date; ?>" readonly>
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>

		<span id="trip_edit_full_payment_date_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_start_date">
	<label class="control-label" for="trip_edit_start_date_input">Start Date</label>
	<div class="controls">
		<div class="input-append date" id="dp2" data-date="<?php print $start_date; ?>" data-date-format="m/d/yyyy">
		  <input type="text" id="trip_edit_start_date_input" value="<?php print $start_date; ?>" readonly>
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
		<span id="trip_edit_start_date_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_end_date">
	<label class="control-label" for="trip_edit_end_date_input">End Date</label>
	<div class="controls">
		<div class="input-append date" id="dp3" data-date="<?php print $end_date; ?>" data-date-format="m/d/yyyy">
		  <input type="text" id="trip_edit_end_date_input" value="<?php print $end_date; ?>" readonly>
			<span class="add-on"><i class="icon-calendar"></i></span>
		</div>
		<span id="trip_edit_end_date_result" class="help-inline"></span>
	</div>
</div>

<script type="text/javascript">

	var dp1 = $("#dp1").datepicker();
	var dp2 = $("#dp2").datepicker();
	var dp3 = $("#dp3").datepicker();
	
	dp1.on("changeDate", function() {
		dp1.datepicker("hide");
		//dp2.datepicker("show");
	});

	dp2.on("changeDate", function() {
		dp2.datepicker("hide");
		//dp3.datepicker("show");
	});

	dp3.on("changeDate", function() {
		dp3.datepicker("hide");
	});

	function save_section_dates() {
		var valid_edit = true;

		var full_payment_date = $("#trip_edit_full_payment_date_input").val();
		var start_date = $("#trip_edit_start_date_input").val();
		var end_date = $("#trip_edit_end_date_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_dates_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					full_payment_date: full_payment_date,
					start_date: start_date,
					end_date: end_date
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_dates_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_dates_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_dates_result").html(json.msg);

						show_result(json.full_payment_date_result, "trip_edit_full_payment_date");
						show_result(json.start_date_result, "trip_edit_start_date");
						show_result(json.end_date_result, "trip_edit_end_date");

					} else {

						$("#trip_edit_full_payment_date_view").val(json.full_payment_date);
						$("#trip_edit_start_date_view").val(json.start_date);
						$("#trip_edit_end_date_view").val(json.end_date);

						cancel_section('dates');

						//changes made require reload of seasons and sites tabs
						reload_seasons = true;
						reload_sites = true;
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_dates_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

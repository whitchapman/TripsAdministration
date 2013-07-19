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
		$trip_price = sprintf ("%.0f", $row["trip_price"]);
	}
	
	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group info" id="trip_edit_price">
	<label class="control-label" for="trip_edit_price_input">Trip Price</label>
	<div class="controls">
		<input type="number" id="trip_edit_price_input" value="<?php print $trip_price; ?>">
		<span id="trip_edit_price_result" class="help-inline"></span>
	</div>
</div>

<script type="text/javascript">

	function save_section_options() {
		var valid_edit = true;

		var trip_price = $("#trip_edit_price_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_options_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					trip_price: trip_price
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_options_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_options_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_options_result").html(json.msg);

						show_result(json.trip_price_result, "trip_edit_price");

					} else {

						$("#trip_edit_price_view").val(json.trip_price);

						cancel_section('options');

						//changes made require reload of seasons and sites tabs
						reload_seasons = true;
						reload_sites = true;
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_options_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

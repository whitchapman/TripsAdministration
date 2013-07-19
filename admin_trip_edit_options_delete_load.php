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
	$sql .= " from trip_options";
	$sql .= " where trip_id=".$trip_id;
	$sql .= " and order_key=".$order_key;
  $result = db_exec_query($conn, $sql);
	
	if ($row = $result->fetch_assoc()) {
		$option_text = $row["option_text"];
		$option_price = sprintf ("%.0f", $row["option_price"]);
	} else {
		$option_text = "";
		$option_price = "0";
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div class="control-group">
	<label class="control-label" for="trip_edit_option_text_view">Option Text</label>
	<div class="controls">
		<input type="text" id="trip_edit_option_text_view" value="<?php print $option_text; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_option_price_view">Option Price</label>
	<div class="controls">
		<input type="text" id="trip_edit_option_price_view" value="<?php print $option_price; ?>" readonly>
	</div>
</div>

<script type="text/javascript">
	
	$(".save-btn").html("Delete");
	$("#trip_edit_options_result").html("Verify that you want to Delete this Trip Option.");

	function save_section_options() {
		var valid_edit = true;

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_options_delete_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					subkey: <?php print $order_key; ?>
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
					} else {
						//reload view
						view_section("options");
						cancel_section('options');
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

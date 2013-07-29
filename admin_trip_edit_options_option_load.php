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
<div class="control-group info" id="trip_edit_option_text">
	<label class="control-label" for="trip_edit_option_text_input">Option Text</label>
	<div class="controls">
		<input type="text" id="trip_edit_option_text_input" value="<?php print $option_text; ?>">
		<span id="trip_edit_option_text_result" class="help-inline"></span>
	</div>
</div>

<div class="control-group info" id="trip_edit_option_price">
	<label class="control-label" for="trip_edit_option_price_input">Option Price</label>
	<div class="controls">
		<input type="number" id="trip_edit_option_price_input" value="<?php print $option_price; ?>">
		<span id="trip_edit_option_price_result" class="help-inline">Use negative number for overall price reduction.</span>
	</div>
</div>

<script type="text/javascript">

	function save_section_options() {
		var valid_edit = true;

		var option_text = $("#trip_edit_option_text_input").val();
		var option_price = $("#trip_edit_option_price_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_options_option_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					subkey: <?php print $order_key; ?>,
					option_text: option_text,
					option_price: option_price
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

						show_result(json.option_text_result, "trip_edit_option_text");
						show_result(json.option_price_result, "trip_edit_option_price");

					} else {
						//reload the view
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

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
	
	$include_single_room = false;
	if ($row = $result->fetch_assoc()) {
		$trip_price = sprintf ("%.0f", $row["trip_price"]);
		if ($row["single_room_price"] > 0) {
			$single_room_price = sprintf ("%.0f", $row["single_room_price"]);
			$include_single_room = ($row["include_single_room"] == 1);
		}
	}
	if (!$include_single_room) {
		$single_room_price = "";
	}
	
	$result->close();

	//-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from trip_options";
	$sql .= " where trip_id=".$trip_id;
	$sql .= " order by order_key";
  $result = db_exec_query($conn, $sql);

	$trip_options = array();
  while ($row = $result->fetch_assoc()) {
		$trip_options[] = $row;
  }

	$result->close();

	//------------------------------

	$sql = "select max(order_key) max_order_key";
	$sql .= " from trip_options";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$new_order_key = 1;
  if ($row = $result->fetch_assoc()) {
		$new_order_key = $row["max_order_key"] + 1;
  }

	$result->close();

	//------------------------------

	$sql = "select *";
	$sql .= " from vw_trip_flights";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$include_land_only = false;
	if ($row = $result->fetch_assoc()) {
		if ($row["land_only_deduction"] > 0) {
			$land_only_deduction = sprintf ("%.0f", $row["land_only_deduction"]);
			$include_land_only = ($row["include_land_only"] == 1);
		}
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>

<div class="control-group">
	<label class="control-label" for="trip_edit_price_view">Trip Price</label>
	<div class="controls">
		<input type="text" id="trip_edit_price_view" value="<?php print $trip_price; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_single_room_price_view">Single Room Price</label>
	<div class="controls">
		<input type="text" id="trip_edit_single_room_price_view" value="<?php print $single_room_price; ?>" readonly>
	</div>
</div>

<br>
<ul class="inline">
	<li><b>Options</b></li>
	<li>
		<div class="btn-group">
			<a class="btn btn-small" href="#" onclick="edit_section('options', <?php print $trip_id; ?>, 'option', <?php print $new_order_key; ?>); return false;">Add Option</a>
		</div>
	</li>
</ul>

<table class="table table-bordered"><tbody>
	<tr>
		<th>Option</th>
		<th>Price</th>
		<th>Actions</th>
	</tr>
<?php

	if ($include_land_only) {
		print "<tr align=\"center\">";
		print "<td>Land Only</td>";
		print "<td>-{$land_only_deduction}</td>";
		print "<td>Edit on the Flight tab.</td>";
		print "</tr>";
	}
	
	if ($include_single_room) {
		print "<tr align=\"center\">";
		print "<td>Single Room</td>";
		print "<td>{$single_room_price}</td>";
		print "<td>Edit Above.</td>";
		print "</tr>";
	}

	foreach($trip_options as $row) {

		$order_key = $row["order_key"];
		$option_text = htmlentities($row["option_text"]);
		$option_price = sprintf ("%.0f", $row["option_price"]);

		print "<tr align=\"center\">";
		print "<td>{$option_text}</td>";
		print "<td>{$option_price}</td>";
		print "<td><div class=\"btn-group\">";
		print "<a class=\"btn btn-small\" href=\"#\" onclick=\"edit_section('options', {$trip_id}, 'option', {$order_key}); return false;\">Edit</a>";
		print "<a class=\"btn btn-small\" href=\"#\" onclick=\"edit_section('options', {$trip_id}, 'delete', {$order_key}); return false;\">Delete</a>";
		print "</div></td>";
		print "</tr>";
	}

	print "</tbody></table>";

?>

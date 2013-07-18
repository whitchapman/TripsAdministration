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

	$sql = "select trip_price";
	$sql .= " from vw_trips";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);
	
	if ($row = $result->fetch_assoc()) {
		$trip_price = sprintf ("%.0f", $row["trip_price"]);
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

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>

<div class="control-group">
	<label class="control-label" for="trip_edit_price_view">Price</label>
	<div class="controls">
		<input type="text" id="trip_edit_price_view" value="<?php print $trip_price; ?>" readonly>
	</div>
</div>

<table class="table table-bordered span6"><tbody>
	<tr>
		<th>Option</th>
		<th>Price</th>
	</tr>
<?php

	foreach($trip_options as $row) {

		$option_text = htmlentities($row["option_text"]);
		$option_price = sprintf ("%.0f", $row["option_price"]);

		print "<tr align=\"center\">";
		print "<td>{$option_text}</td>";
		print "<td>{$option_price}</td>";
		print "</tr>";
	}

	print "</tbody></table>";

?>

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
	$sql .= " from vw_trip_flights";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$flight = array();
	if ($row = $result->fetch_assoc()) {
		$flight = $row;

		$airline_id = $row["airline_id"];
		$airline_name = $row["airline_name"];
		$flight_release_date = date("n/j/Y", strtotime($row["flight_release_date"]));
		$ticketing_date = date("n/j/Y", strtotime($row["ticketing_date"]));

		$include_land_only = false;
		if ($row["land_only_deduction"] > 0) {
			$land_only_deduction = sprintf ("%.0f", $row["land_only_deduction"]);
			$include_land_only = ($row["include_land_only"] == 1);
		}
		if (!$include_land_only) {
			$land_only_deduction = "";
		}
	}

	$result->close();

	//-----------------------------------------------------------------

	$sql = "select *";
	$sql .= " from flight_legs";
	$sql .= " where trip_id=".$trip_id;
	$sql .= " order by departure_time";
  $result = db_exec_query($conn, $sql);

	$flight_legs = array();
  while ($row = $result->fetch_assoc()) {
		$flight_legs[] = $row;
  }

	$result->close();

	$sql = "select max(order_key) max_order_key";
	$sql .= " from flight_legs";
	$sql .= " where trip_id=".$trip_id;
  $result = db_exec_query($conn, $sql);

	$new_order_key = 1;
  if ($row = $result->fetch_assoc()) {
		$new_order_key = $row["max_order_key"] + 1;
  }

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

	//-----------------------------------------------------------------

	if (count($flight) == 0) {
		print "<b>Edit to Add Flight</b>";
	} else {
	
?>

<div class="control-group">
	<label class="control-label" for="trip_edit_airline_view">Airline</label>
	<div class="controls">
		<input type="text" id="trip_edit_airline_view" value="<?php print $airline_name; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_flight_release_date_view">Flight Release Date</label>
	<div class="controls">
		<input type="text" id="trip_edit_flight_release_date_view" value="<?php print $flight_release_date; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_ticketing_date_view">Ticketing Date</label>
	<div class="controls">
		<input type="text" id="trip_edit_ticketing_date_view" value="<?php print $ticketing_date; ?>" readonly>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="trip_edit_land_only_deduction_view">Land Only Deduction</label>
	<div class="controls">
		<input type="text" id="trip_edit_land_only_deduction_view" value="<?php print $land_only_deduction; ?>" readonly>
	</div>
</div>

<br>
<ul class="inline">
	<li><b>Flight Legs</b></li>
	<li>
		<div class="btn-group">
			<a class="btn btn-small" href="#" onclick="edit_section('flight', <?php print $trip_id; ?>, 'leg', <?php print $new_order_key; ?>); return false;">Add Leg</a>
		</div>
	</li>
</ul>

<table class="table table-bordered"><thead>
	<tr>
		<th rowspan="2">Flight#</th>
		<th colspan="2" align="center">Departure</th>
		<th colspan="2" align="center">Arrival</th>
		<th rowspan="2">Actions</th>
	</tr>
	<tr>
		<th>Airport</th>
		<th>Time</th>
		<th>Airport</th>
		<th>Time</th>
	</tr>
</thead><tbody>
<?php

		foreach($flight_legs as $row) {

			$order_key = $row["order_key"];
			$flight_number = htmlentities($row["flight_number"]);
			$departure_airport = htmlentities($row["departure_airport"]);
			$departure_time = date("n/j/Y  g:i a", strtotime($row["departure_time"]));
			$arrival_airport = htmlentities($row["arrival_airport"]);
			$arrival_time = date("n/j/Y  g:i a", strtotime($row["arrival_time"]));

			print "<tr align=\"center\">";
			print "<td>{$flight_number}</td>";
			print "<td>{$departure_airport}</td>";
			print "<td>{$departure_time}</td>";
			print "<td>{$arrival_airport}</td>";
			print "<td>{$arrival_time}</td>";
			print "<td><div class=\"btn-group\">";
			print "<a class=\"btn btn-small\" href=\"#\" onclick=\"edit_section('flight', {$trip_id}, 'leg', {$order_key}); return false;\">Edit</a>";
			print "<a class=\"btn btn-small\" href=\"#\" onclick=\"edit_section('flight', {$trip_id}, 'delete', {$order_key}); return false;\">Delete</a>";
			print "</div></td>";
			print "</tr>";
		}
	
		print "</tbody></table>";

	}

?>

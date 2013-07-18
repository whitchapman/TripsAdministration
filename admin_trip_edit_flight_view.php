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
		$airline_id = $flight["airline_id"];
		$airline_name = $flight["airline_name"];
		$flight_release_date = date("m/d/Y", strtotime($flight["flight_release_date"]));
		$ticketing_date = date("m/d/Y", strtotime($flight["ticketing_date"]));
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

<table class="table table-bordered"><thead>
	<tr>
		<th rowspan="2">Flight#</th>
		<th colspan="3" align="center">Departure</th>
		<th colspan="3" align="center">Arrival</th>
	</tr>
	<tr>
		<th>Airport</th>
		<th>Date</th>
		<th>Time</th>
		<th>Airport</th>
		<th>Date</th>
		<th>Time</th>
	</tr>
</thead><tbody>
<?php

		foreach($flight_legs as $row) {

			$flight_number = htmlentities($row["flight_number"]);
			$departure_airport = htmlentities($row["departure_airport"]);
			$departure_date = date("m/d/Y", strtotime($row["departure_time"]));
			$departure_time = date("g:ia", strtotime($row["departure_time"]));
			$arrival_airport = htmlentities($row["arrival_airport"]);
			$arrival_date = date("m/d/Y", strtotime($row["arrival_time"]));
			$arrival_time = date("g:ia", strtotime($row["arrival_time"]));

			print "<tr align=\"center\">";
			print "                    <td>{$flight_number}</td>";
			print "                    <td>{$departure_airport}</td>";
			print "                    <td>{$departure_date}</td>";
			print "                    <td>{$departure_time}</td>";
			print "                    <td>{$arrival_airport}</td>";
			print "                    <td>{$arrival_date}</td>";
			print "                    <td>{$arrival_time}</td>";
			print "</tr>";
		}
	
		print "</tbody></table>";

	}

?>

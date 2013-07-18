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
		$site_name = $row["site_name"];
		$trip_leader_name = $row["trip_leader_name"];
		$edit_state = $row["edit_state"];
		$trip_state = $row["trip_state"];
		$season_id = $row["season_id"];
		$full_payment_date = strtotime($row["full_payment_date"]);
		$start_date = strtotime($row["start_date"]);
		$end_date = strtotime($row["end_date"]);
		$site_id = $row["site_id"];
		$trip_leader = $row["trip_leader"];
		$trip_title = htmlentities($row["trip_title"]);
		$trip_price = sprintf ("%.0f", $row["trip_price"]);
		$max_capacity = $row["max_capacity"];
		$max_waitlist = $row["max_waitlist"];
		$num_confirmed = $row["num_confirmed"];
		$num_signups = $row["num_signups"];
		$image1 = $row["image1"];
		$image2 = $row["image2"];
		$image3 = $row["image3"];
		$trip_description = $row["trip_description"];
		$trip_notes = $row["trip_notes"];

		$trip_year = date("Y", $start_date);
		$full_payment_date = date("m/d/Y", $full_payment_date);
		$start_date = date("m/d/Y", $start_date);
		$end_date = date("m/d/Y", $end_date);

		$trip_str = $site_name." ".$trip_year;
		$trip_state_label = create_trip_state_label($trip_state, $full_payment_date, $start_date, $end_date, $max_capacity, $max_waitlist, $num_confirmed, $num_signups);
	}
	
	$result->close();

	//-----------------------------------------------------------------

	$sql = "select bullet_text";
	$sql .= " from trip_bullets where trip_id=".$trip_id;
	$sql .= " order by order_key";
  $result = db_exec_query($conn, $sql);

	$bullets = array();
  while ($row = $result->fetch_assoc()) {
		$bullets[] = $row;
  }

	$result->close();
	
	$count = count($bullets);
	$trip_bullets = "";
	for ($i = 0; $i < $count; $i++) {
		$text = $bullets[$i]["bullet_text"];
		if ($i > 0) {
			$trip_bullets .= "\n";
		}
		$trip_bullets .= $text;
	}

	//-----------------------------------------------------------------

//	$sql = "select *";
//	$sql .= " from vw_trip_flights";
//	$sql .= " where trip_id=".$trip_id;
//  $result = db_exec_query($conn, $sql);
//
//	$flight = array();
//	if ($row = $result->fetch_assoc()) {
//		$flight = $row;
//		$airline_id = $flight["airline_id"];
//		$airline_name = $flight["airline_name"];
//		$flight_release_date = strtotime($flight["flight_release_date"]);
//		$ticketing_date = strtotime($flight["ticketing_date"]);
//	}
//
//	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select *";
//	$sql .= " from flight_legs";
//	$sql .= " where trip_id=".$trip_id;
//	$sql .= " order by departure_time";
//  $result = db_exec_query($conn, $sql);
//
//	$flight_legs = array();
//  while ($row = $result->fetch_assoc()) {
//		$flight_legs[] = $row;
//  }
//
//	$result->close();

	//-----------------------------------------------------------------

//	$sql = "select *";
//	$sql .= " from trip_options";
//	$sql .= " where trip_id=".$trip_id;
//	$sql .= " order by order_key";
//  $result = db_exec_query($conn, $sql);
//
//	$trip_options = array();
//  while ($row = $result->fetch_assoc()) {
//		$trip_options[] = $row;
//  }
//
//	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<ul class="inline">
	<li><b><span id="trip_edit_trip_str"><?php print $trip_str; ?></span></b></li>
	<li><a class="btn btn-small" href="preview_trip.php?trip=<?php print $trip_id; ?>" target="_blank">Preview Trip</a></li>
</ul>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active edit-link"><a class="edit-tab" id="trip_edit_main_link_general" href="#trip_edit_main_panel_general" data-toggle="tab" onclick="return false;">General</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_dates" href="#trip_edit_main_panel_dates" data-toggle="tab" onclick="return false;">Dates</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_text" href="#trip_edit_main_panel_text" data-toggle="tab" onclick="return false;">Text</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_images" href="#trip_edit_main_panel_images" data-toggle="tab" onclick="return false;">Images</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_flight" href="#trip_edit_main_panel_flight" data-toggle="tab" onclick="return false;">Flight</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_options" href="#trip_edit_main_panel_options" data-toggle="tab" onclick="return false;">Price & Options</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_state" href="#trip_edit_main_panel_state" data-toggle="tab" onclick="return false;">State</a></li>
		<li class="edit-link"><a class="edit-tab" id="trip_edit_main_link_notes" href="#trip_edit_main_panel_notes" data-toggle="tab" onclick="return false;">Notes</a></li>
	</ul>
	<div class="tab-content">
		<div id="trip_edit_main_panel_general" class="tab-pane active">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section('general', <?php print $trip_id; ?>); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_general(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('general'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_general_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_general_link_view" href="#trip_edit_general_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_general_link_edit" href="#trip_edit_general_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_general_panel_view" class="tab-pane active">
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_season_view">Season</label>
							<div class="controls">
								<input type="text" id="trip_edit_season_view" value="<?php print $season_id; ?>" readonly>
							</div>
						</div>
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_site_view">Site</label>
							<div class="controls">
								<input type="text" id="trip_edit_site_view" value="<?php print $site_name; ?>" readonly>
							</div>
						</div>
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_title_view">Title</label>
							<div class="controls">
								<input type="text" id="trip_edit_title_view" value="<?php print $trip_title; ?>" readonly>
							</div>
						</div>
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_trip_leader_view">Trip Leader</label>
							<div class="controls">
								<input type="text" id="trip_edit_trip_leader_view" value="<?php print $trip_leader_name; ?>" readonly>
							</div>
						</div>
	
					</div>
					<div id="trip_edit_general_panel_edit" class="tab-pane"></div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_dates" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section('dates', <?php print $trip_id; ?>); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_dates(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('dates'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_dates_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_dates_link_view" href="#trip_edit_dates_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_dates_link_edit" href="#trip_edit_dates_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_dates_panel_view" class="tab-pane active">

						<div class="control-group">
							<label class="control-label" for="trip_edit_full_payment_date_view">Full Payment Date</label>
							<div class="controls">
								<input type="text" id="trip_edit_full_payment_date_view" value="<?php print $full_payment_date; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_start_date_view">Start Date</label>
							<div class="controls">
								<input type="text" id="trip_edit_start_date_view" value="<?php print $start_date; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_end_date_view">End Date</label>
							<div class="controls">
								<input type="text" id="trip_edit_end_date_view" value="<?php print $end_date; ?>" readonly>
							</div>
						</div>

					</div>
					<div id="trip_edit_dates_panel_edit" class="tab-pane"></div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_text" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section_text(); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_text(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('text'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_text_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form>

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_text_link_view" href="#trip_edit_text_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_text_link_edit" href="#trip_edit_text_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_text_panel_view" class="tab-pane active">
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_description_view">Description</label>
							<div class="controls">
								<textarea id="trip_edit_description_view" class="span9" rows="5" readonly><?php print $trip_description; ?></textarea>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_bullets_view">Bullets</label>
							<div class="controls">
								<textarea id="trip_edit_bullets_view" class="span9" rows="9" wrap="off" readonly><?php print $trip_bullets; ?></textarea>
							</div>
						</div>
	
					</div>
					<div id="trip_edit_text_panel_edit" class="tab-pane">

						<div class="control-group info" id="trip_edit_description">
							<label class="control-label" for="trip_edit_description_input">Description</label>
							<div class="controls">
								<textarea id="trip_edit_description_input" class="span9" rows="5"><?php print $trip_description; ?></textarea>
							</div>
						</div>
						
						<div class="control-group info" id="trip_edit_bullets">
							<label class="control-label" for="trip_edit_bullets_input">Bullets</label>
							<div class="controls">
								<textarea id="trip_edit_bullets_input" class="span9" rows="9" wrap="off"><?php print $trip_bullets; ?></textarea>
							</div>
						</div>
	
					</div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_images" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section_images(); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_images(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('images'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_images_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_images_link_view" href="#trip_edit_images_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_images_link_edit" href="#trip_edit_images_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_images_panel_view" class="tab-pane active">

						<div class="control-group">
							<label class="control-label" for="trip_edit_image1_view">Image 1</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image1_view" value="<?php print $image1; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_image2_view">Image 2</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image2_view" value="<?php print $image2; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_image3_view">Image 3</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image3_view" value="<?php print $image3; ?>" readonly>
							</div>
						</div>

					</div>
					<div id="trip_edit_images_panel_edit" class="tab-pane">

						<div class="control-group info" id="trip_edit_image1">
							<label class="control-label" for="trip_edit_image1_input">Image 1</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image1_input" value="<?php print $image1; ?>">
								<span id="trip_edit_image1_result" class="help-inline"></span>
							</div>
						</div>

						<div class="control-group info" id="trip_edit_image2">
							<label class="control-label" for="trip_edit_image2_input">Image 2</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image2_input" value="<?php print $image2; ?>">
								<span id="trip_edit_image2_result" class="help-inline"></span>
							</div>
						</div>

						<div class="control-group info" id="trip_edit_image3">
							<label class="control-label" for="trip_edit_image3_input">Image 3</label>
							<div class="controls">
								<input type="text" class="span5" id="trip_edit_image3_input" value="<?php print $image3; ?>">
								<span id="trip_edit_image3_result" class="help-inline"></span>
							</div>
						</div>

					</div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_flight" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section('flight', <?php print $trip_id; ?>); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_flight(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('flight'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_flight_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_flight_link_view" href="#trip_edit_flight_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_flight_link_edit" href="#trip_edit_flight_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_flight_panel_view" class="tab-pane active"></div>
					<div id="trip_edit_flight_panel_edit" class="tab-pane"></div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_options" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section('options', <?php print $trip_id; ?>); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_options(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('options'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_options_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_options_link_view" href="#trip_edit_options_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_options_link_edit" href="#trip_edit_options_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_options_panel_view" class="tab-pane active"></div>
					<div id="trip_edit_options_panel_edit" class="tab-pane"></div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_state" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section('state', <?php print $trip_id; ?>); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_state(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('state'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_state_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form class="form-horizontal">

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_state_link_view" href="#trip_edit_state_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_state_link_edit" href="#trip_edit_state_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_state_panel_view" class="tab-pane active">

						<div class="control-group">
							<label class="control-label" for="trip_edit_trip_state_view">State</label>
							<div class="controls">
								<input type="text" id="trip_edit_trip_state_view" value="<?php print trip_state_to_string($trip_state); ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_max_capacity_view">Max Capacity</label>
							<div class="controls">
								<input type="text" id="trip_edit_max_capacity_view" value="<?php print $max_capacity; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_max_waitlist_view">Max Waitlist</label>
							<div class="controls">
								<input type="text" id="trip_edit_max_waitlist_view" value="<?php print $max_waitlist; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_num_confirmed_view">Num Confirmed</label>
							<div class="controls">
								<input type="text" id="trip_edit_num_confirmed_view" value="<?php print $num_confirmed; ?>" readonly>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="trip_edit_num_signups_view">Num Signups</label>
							<div class="controls">
								<input type="text" id="trip_edit_num_signups_view" value="<?php print $num_signups; ?>" readonly>
							</div>
						</div>

					</div>
					<div id="trip_edit_state_panel_edit" class="tab-pane"></div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
		<div id="trip_edit_main_panel_notes" class="tab-pane">

			<ul class="inline">
				<li><a class="btn btn-mini edit-link" href="#" onclick="edit_section_notes(); return false;">Edit</a></li>
				<li><a class="btn btn-mini btn-primary edit-btn hidden" href="#" onclick="save_section_notes(); return false;">Save</a></li>
				<li><a class="btn btn-mini edit-btn hidden" href="#" onclick="cancel_section('notes'); return false;">Cancel</a></li>
				<li>
					<div class="control-group error">
						<div class="controls">
							<span id="trip_edit_notes_result" class="help-inline"></span>
						</div>
					</div>
				</li>
			</ul>
			<form>

			<div class="tabbable">
				<div style="display:none;">
					<ul class="nav nav-tabs">
						<li><a id="trip_edit_notes_link_view" href="#trip_edit_notes_panel_view" data-toggle="tab"></a></li>
						<li><a id="trip_edit_notes_link_edit" href="#trip_edit_notes_panel_edit" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="trip_edit_notes_panel_view" class="tab-pane active">
	
						<div class="control-group">
							<label class="control-label" for="trip_edit_notes_view">Notes</label>
							<div class="controls">
								<textarea id="trip_edit_notes_view" class="span9" rows="10" wrap="off" readonly><?php print $trip_notes; ?></textarea>
							</div>
						</div>
	
					</div><!-- tab-pane -->
					<div id="trip_edit_notes_panel_edit" class="tab-pane">

						<div class="control-group info" id="trip_edit_notes">
							<label class="control-label" for="trip_edit_notes_input">Notes</label>
							<div class="controls">
								<textarea id="trip_edit_notes_input" class="span9" rows="10" wrap="off"><?php print $trip_notes; ?></textarea>
							</div>
						</div>
						
					</div>
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			</form>
		</div><!-- tab-pane -->
	</div><!-- tab-content -->
</div><!-- tabbable -->	
</form>
<script type="text/javascript">

	function view_section(section) {
		$.ajax({
			url: "admin_trip_edit_"+section+"_view.php",
			data: {
				trip: <?php print $trip_id; ?>
			},
			type: "POST",
			dataType: "html",
			success: function(html) {
				clear_alert("info");
				//add_alert("success", "Loaded.");
				$("#trip_edit_"+section+"_panel_view").html(html);
			},
			error: function(xhr, status, thrown) {
				clear_alert("info");
				add_alert("error", "Error loading: " + thrown);
			},
			complete: function(xhr, status) {
				//alert("The request is complete!");
			}
		});
	}
	view_section("flight");
	view_section("options");

	function edit_section(section, trip_arg) {
		if (!$(".edit-link").hasClass("disabled")) {
			$(".edit-link").addClass("disabled");
			$(".edit-tab").removeAttr("data-toggle");
			$(".edit-btn").removeClass("hidden");

			if (typeof(trip_arg) == "number") {
				trip = trip_arg.toString();
			} else if (typeof(trip_arg) == "string") {
				trip = trip_arg;
			} else {
				//if not trip_arg, then don't load php, just show tab
				$("#trip_edit_"+section+"_link_edit").tab("show");
				return;
			}

			//add_alert("info", "Loading...");
			$.ajax({
				url: "admin_trip_edit_"+section+"_load.php",
				data: {
					trip: trip
				},
				type: "POST",
				dataType: "html",
				success: function(html) {
					clear_alert("info");
					//add_alert("success", "Loaded.");
					$("#trip_edit_"+section+"_panel_edit").html(html);
					$("#trip_edit_"+section+"_link_edit").tab("show");
				},
				error: function(xhr, status, thrown) {
					clear_alert("info");
					add_alert("error", "Error loading: " + thrown);
					cancel_section(section);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

	function cancel_section(section) {
		$("#trip_edit_"+section+"_result").html("");
		$("#trip_edit_"+section+"_link_view").tab("show");
		$(".edit-link").removeClass("disabled");
		$(".edit-tab").attr("data-toggle", "tab");
		$(".edit-btn").addClass("hidden");
	}

	function edit_section_text() {
		$("#trip_edit_description_input").val($("#trip_edit_description_view").val());
		$("#trip_edit_bullets_input").val($("#trip_edit_bullets_view").val());
		edit_section("text");
	}

	function save_section_text() {
		var valid_edit = true;

		var description = $("#trip_edit_description_input").val();
		var bullets = $("#trip_edit_bullets_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_text_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					description: description,
					bullets: bullets
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_text_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_text_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_text_result").html(json.msg);
					} else {
						$("#trip_edit_description_view").val($("#trip_edit_description_input").val());
						$("#trip_edit_bullets_view").val($("#trip_edit_bullets_input").val());
						cancel_section('text');
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_text_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

	function edit_section_images() {
		$("#trip_edit_image1_input").val($("#trip_edit_image1_view").val());
		$("#trip_edit_image2_input").val($("#trip_edit_image2_view").val());
		$("#trip_edit_image3_input").val($("#trip_edit_image3_view").val());
		edit_section("images");
	}

	function save_section_images() {
		var valid_edit = true;

		var image1 = $("#trip_edit_image1_input").val();
		var image2 = $("#trip_edit_image2_input").val();
		var image3 = $("#trip_edit_image3_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_images_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					image1: image1,
					image2: image2,
					image3: image3
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_images_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_images_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (!json.valid) {
						$("#trip_edit_images_result").html(json.msg);

						//show_result(json.image1_result, "trip_edit_image1");
						//show_result(json.image2_result, "trip_edit_image2");
						//show_result(json.image3_result, "trip_edit_image3");

					} else {
						$("#trip_edit_image1_view").val($("#trip_edit_image1_input").val());
						$("#trip_edit_image2_view").val($("#trip_edit_image2_input").val());
						$("#trip_edit_image3_view").val($("#trip_edit_image3_input").val());

						cancel_section('images');
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_images_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

	function edit_section_notes() {
		$("#trip_edit_notes_input").val($("#trip_edit_notes_view").val());
		edit_section("notes");
	}

	function save_section_notes() {
		var valid_edit = true;

		var notes = $("#trip_edit_notes_input").val();

		//do client-side validation here

		if (valid_edit) {
			$.ajax({
				url: "admin_trip_edit_notes_save.php",
				data: {
					trip: <?php print $trip_id; ?>,
					notes: notes
				},
				type: "POST",
				dataType: "json",
				success: function(json) {
					if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
						var html = "Error: Invalid JSON<br>";
						$("#trip_edit_notes_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else if (json.exn) {
						var html = json.msg + "<br>";
						$("#trip_edit_notes_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
					} else {
						$("#trip_edit_notes_view").val($("#trip_edit_notes_input").val());
						cancel_section('notes');
					}
				},
				error: function(xhr, status, thrown) {
					$("#trip_edit_notes_result").html("Error returned from ajax: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	}

</script>

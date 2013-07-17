<?php

	//$throw_errors = true;
	include "check_login.php";

	//accept optional season_id or site_id
	$selected_season_id = post_to_string("season"); //default to latest
	$selected_site_id = post_to_string("site"); //default to blank

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------

	$sql = "select season_id";
	$sql .= " from seasons";
	$sql .= " order by season_id desc";
	$result = db_exec_query($conn, $sql);

	$season_ids = array();
	while ($row = $result->fetch_assoc()) {
		$season_id = $row["season_id"];
		$season_ids[] = $row["season_id"];
	}

	$result->close();

	//default season if not posted in
	if (!is_numeric($selected_season_id)) {
		$selected_season_id = $season_ids[0];
	}
	//$selected_season_quoted = "\"".$selected_season_id."\"";

	//-----------------------------------------------------------------

	//default season if not posted in
	if (!is_numeric($selected_site_id)) {
		$selected_site_id = "";
	}
	$selected_title = "";

	$sql = "select site_id, site_name, trip_title";
	$sql .= " from sites";
	$sql .= " order by site_name";
	$result = db_exec_query($conn, $sql);

	$sites = array();
	while ($row = $result->fetch_assoc()) {
		$site_id = $row["site_id"];
		if ($site_id == $selected_site_id) {
			$selected_title = $row["trip_title"];
		}
		$sites[] = $row;
	}

	$result->close();
	
	//$selected_site_quoted = "\"".$selected_site_id."\"";
	//$selected_title_quoted = "\"".$selected_title."\"";

	//-----------------------------------------------------------------

	$sql = "select member_id, full_name";
	$sql .= " from vw_trip_leaders";
	$sql .= " order by full_name";
	$result = db_exec_query($conn, $sql);

	$trip_leaders = array();
	while ($row = $result->fetch_assoc()) {
		$trip_leaders[] = $row;
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div id="modal_trip_add" class="modal hide fade" tabindex="-1" role="dialog" style="width:800px;margin-left:-360px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Add Trip</h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal">

		  <div class="control-group" id="modal_trip_add_season">
		    <label class="control-label" for="modal_trip_add_season_select">Season</label>
		    <div class="controls">
					<select id="modal_trip_add_season_select">
						<?php
							foreach ($season_ids as $season_id) {
								print "<option".($season_id == $selected_season_id ? " selected" : "").">{$season_id}</option>";
							}
						?>
					</select>
			    <span id="modal_trip_add_season_result" class="help-inline"></span>
		    </div>
		  </div>

		  <div class="control-group">
		    <div class="controls">
					<label class="radio inline">
					  <input type="radio" name="modal_trip_add_site_radio" id="modal_trip_add_site_radio_existing" value="existing" checked>
					  Choose Existing Site
					</label>
					<label class="radio inline">
					  <input type="radio" name="modal_trip_add_site_radio" id="modal_trip_add_site_radio_new" value="new">
					  Add New Site
					</label>
		    </div>
		  </div>

			<div class="tabbable" id="tabs_modal_trip_add_site">
				<div style="display:none;">
					<ul id="trips_panel_tab" class="nav nav-tabs">
						<li><a id="tabs_modal_trip_add_site_link_existing" href="#tabs_modal_trip_add_site_panel_existing" data-toggle="tab"></a></li>
						<li><a id="tabs_modal_trip_add_site_link_new" href="#tabs_modal_trip_add_site_panel_new" data-toggle="tab"></a></li>
					</ul>
				</div>
				<div class="tab-content">
					<div id="tabs_modal_trip_add_site_panel_existing" class="tab-pane active">
						<div class="control-group" id="modal_trip_add_site_existing">
							<label class="control-label" for="modal_trip_add_site_select">Site</label>
							<div class="controls">
								<select id="modal_trip_add_site_select">
									<option></option>
									<?php
										foreach ($sites as $row) {
											$site_id = $row["site_id"];
											$site_name = $row["site_name"];
											$trip_title = $row["trip_title"];
											print "<option value=\"{$site_id}\" trip_title=\"{$trip_title}\"".($site_id == $selected_site_id ? " selected" : "").">{$site_name}</option>";
										}
									?>
								</select>
								<span id="modal_trip_add_site_existing_result" class="help-inline">Each Site has a default trip Title.</span>
							</div>
						</div>
					</div><!-- tab-pane -->
					<div id="tabs_modal_trip_add_site_panel_new" class="tab-pane">
						<div class="control-group" id="modal_trip_add_site_new">
							<label class="control-label" for="modal_trip_add_site_input">Site</label>
							<div class="controls">
								<input type="text" id="modal_trip_add_site_input" placeholder="New Site">
								<span id="modal_trip_add_site_new_result" class="help-inline">Trip Title defaults to the new Site.</span>
							</div>
						</div>
					</div><!-- tab-pane -->
				</div><!-- tab-content -->
			</div><!-- tabbable -->

			<div class="control-group" id="modal_trip_add_title">
				<label class="control-label" for="modal_trip_add_title_input">Title</label>
				<div class="controls">
					<input type="text" id="modal_trip_add_title_input" value="<?php print $selected_title; ?>">
					<span id="modal_trip_add_title_result" class="help-inline">Add a little more detail than the Site Name.</span>
				</div>
			</div>

		  <div class="control-group" id="modal_trip_add_trip_leader">
		    <label class="control-label" for="modal_trip_add_trip_leader_select">Trip Leader</label>
		    <div class="controls">
					<select id="modal_trip_add_trip_leader_select">
						<option></option>
						<?php
							foreach ($trip_leaders as $row) {
								$member_id = $row["member_id"];
								$full_name = $row["full_name"];
								print "<option value=\"{$member_id}\">{$full_name}</option>";
							}
						?>
					</select>
			    <span id="modal_trip_add_trip_leader_result" class="help-inline">New Trip Leaders will be added by request.</span>
		    </div>
		  </div>

			<div class="control-group error">
				<div class="controls">
					<span id="modal_trip_add_result" class="help-inline"></span>
				</div>
			</div>

		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		<button class="btn btn-primary" onclick="save_trip_add();">Save</button>
		<script type="text/javascript">

			$("#modal_trip_add_site_radio_existing").click(function() {
				$("#modal_trip_add_title_input").val($("#modal_trip_add_site_select :selected").attr("trip_title"));
				$("#tabs_modal_trip_add_site_link_existing").tab("show");
			});

			$("#modal_trip_add_site_radio_new").click(function() {
				$("#modal_trip_add_site_input").val("");
				$("#modal_trip_add_title_input").val("");
				$("#tabs_modal_trip_add_site_link_new").tab("show");
			});

			$("#modal_trip_add_site_select").change(function() {				
				$("#modal_trip_add_title_input").val($("#modal_trip_add_site_select :selected").attr("trip_title"));
			});

			$("#modal_trip_add_site_input").blur(function() {
				$("#modal_trip_add_title_input").val($("#modal_trip_add_site_input").val());
			});

			function show_result(result, control_group_id) {
				if (result.length > 0) {
					$("#"+control_group_id).addClass("error");
					$("#"+control_group_id+"_result").html(result);
				} else {
					$("#"+control_group_id).removeClass("error");
					$("#"+control_group_id+"_result").html("");
				}
			}

			function save_trip_add() {
				var valid_add = true;

				var season = $("#modal_trip_add_season_select").val();
				var site = $("#modal_trip_add_site_select").val();
				var site_name = $("#modal_trip_add_site_input").val();

				//alert($("input[name=modal_trip_add_site_radio]:checked").val());
				var site_action = $("input[name=modal_trip_add_site_radio]:checked").val();

				var trip_title = $("#modal_trip_add_title_input").val();
				var trip_leader = $("#modal_trip_add_trip_leader_select").val();

				//do validation here

				if (valid_add) {
					$("#modal_trip_add_result").html("Saving...");
					$.ajax({
						url: "admin_trip_add_save.php",
						data: {
							season: season,
							site: site,
							site_name: site_name,
							site_action: site_action,
							trip_title: trip_title,
							trip_leader: trip_leader
						},
						type: "POST",
						dataType: "json",
						success: function(json) {
							if ((typeof(json.exn) != "boolean") || (typeof(json.msg) != "string")) {
								var html = "Error: Invalid JSON<br>";
								$("#modal_trip_add_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else if (json.exn) {
								var html = json.msg + "<br>";
								$("#modal_trip_add_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else if (!json.valid) {
								$("#modal_trip_add_result").html(json.msg);
								show_result(json.season_result, "modal_trip_add_season");
								show_result(json.site_existing_result, "modal_trip_add_site_existing");
								show_result(json.site_new_result, "modal_trip_add_site_new");
								show_result(json.title_result, "modal_trip_add_title");
								show_result(json.trip_leader_result, "modal_trip_add_trip_leader");
							} else {
								$("#modal_trip_add_result").html("Added.");
								$("#modal_trip_add").modal("hide");

								//show results subtab on trips panel but set so resets to standard subtab next time on trips tab
								$("#recently_saved_trip_str").html(json.msg);
								$("#recently_saved_trip_msg").html("<p>All dates were defaulted.</p>");
								recently_saved_trip = json.new_trip;
								$('#trips_panel_tab a[href="#tabs_trips_panel_results"]').tab('show');

								reload_trips = false; //retain results tab when opening trips tab
								open_tab("trips");
								reload_trips = true;

								add_alert("success", "Trip Added.");

								reload_seasons = true;
								reload_sites = true;
							}
						},
						error: function(xhr, status, thrown) {
							$("#modal_trip_add_result").html("Error returned from ajax: " + thrown);
						},
						complete: function(xhr, status) {
							//alert("The request is complete!");
						}
					});

				}
			}



		</script>
	</div>
</div>

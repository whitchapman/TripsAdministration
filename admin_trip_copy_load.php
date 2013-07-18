<?php

	include "check_login.php";

	//accept trip_id so can remove its season_id from list (can't copy to same season)
	$trip_id = post_to_string("trip");

	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------

	//TODO: return an error message (how?) if trip_id is invalid

	if (is_numeric($trip_id)) {
		$trip_id = intval($trip_id);

		$sql = "select *";
		$sql .= " from vw_trips";
		$sql .= " where trip_id=".$trip_id;
	  $result = db_exec_query($conn, $sql);

	  if ($row = $result->fetch_assoc()) {
			$trip_season_id = $row["season_id"];
			$site_name = htmlentities($row["site_name"]);
			$start_date = strtotime($row["start_date"]);
			$trip_year = date("Y", $start_date);

			$trip_str = $site_name." ".$trip_year." [season ".$trip_season_id."]";
		}

		$result->close();
	}

	//-----------------------------------------------------------------

	$sql = "select season_id";
	$sql .= " from seasons";
	$sql .= " order by season_id desc";
	$result = db_exec_query($conn, $sql);

	$season_ids = array();
	while ($row = $result->fetch_assoc()) {
		$season_id = $row["season_id"];
		if ($season_id != $trip_season_id) {
			$season_ids[] = $row["season_id"];
		}
	}

	$result->close();
	
	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<div id="modal_trip_copy" class="modal hide fade" tabindex="-1" role="dialog" style="width:500px;margin-left:-250px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h3>Copy <?php print $trip_str; ?></h3>
	</div>
	<div class="modal-body">
		<div class="control-group info">
			<span class="help-inline">to season: </span>
			<select id="modal_trip_copy_select">
				<option></option>
				<?php
					foreach ($season_ids as $season_id) {
						print "<option>{$season_id}</option>";
					}
				?>
			</select>
		</div>
		<div class="control-group help-inline error">
			<span class="help-inline" id="modal_trip_copy_result"></span>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="save_trip_copy();">Save</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		<script type="text/javascript">

//			$("#modal_trip_copy").on("show", function () {
//				$("#modal_trip_copy_result").html("");
//				$("#modal_trip_copy_select").val("");
//			})

			function save_trip_copy() {
				var new_season = $("#modal_trip_copy_select").val();
				if (new_season == "") {
					$("#modal_trip_copy").modal("hide");
				} else {
					$.ajax({
						url: "admin_trip_copy_save.php",
						data: {
							trip: "<?php print $trip_id; ?>",
							new_season: new_season
						},
						type: "POST",
						dataType: "json",
						success: function(json) {
							if ((typeof(json.valid) != "boolean") || (typeof(json.msg) != "string")) {
								var html = "Error: Invalid JSON<br>";
								$("#modal_trip_copy_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else if (!json.valid) {
								var html = "Error: " + json.msg + "<br>";
								$("#modal_trip_copy_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else {
								$("#modal_trip_copy").modal("hide");

								add_alert("success", "Trip Copied. Editing...");
								load_trip_edit(json.new_trip);

								reload_seasons = true;
								reload_sites = true;
							}
						},
						error: function(xhr, status, thrown) {
							$("#modal_trip_copy_result").html("Error returned from ajax: " + thrown);
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

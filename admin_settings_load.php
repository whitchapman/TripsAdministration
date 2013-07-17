<?php

	include "check_login.php";

	//-----------------------------------------------------------------

	$settings = load_settings();
	$current_season_id = $settings["season_id"];
	$current_season_quoted = "\"".$current_season_id."\"";
	
	//-----------------------------------------------------------------
	//open connection

	$conn = db_open_conn();

	//-----------------------------------------------------------------

	$sql = "select distinct season_id";
	$sql .= " from vw_trips";
	$sql .= " order by season_id desc";
	$result = db_exec_query($conn, $sql);

	$season_ids = array();
	while ($row = $result->fetch_assoc()) {
		$season_ids[] = $row["season_id"];
	}

	$result->close();

	//-----------------------------------------------------------------
	//close connection
	
	$conn->close();

?>
<table class="table table-bordered"><tbody>
	<tr>
		<th>Setting</th>
		<th>Value</th>
		<th>Note</th>
		<th>Edit</th>
	</tr>
	<tr>
		<td>Current Season</td>
		<td><?php print $current_season_id; ?></td>
		<td>The season shown on the main trips page.</td>
		<td><a href="#modal_edit_current_season" role="button" class="btn btn-small" data-toggle="modal">Edit</a></td>
	</tr>
</tbody></table>

<div id="modal_edit_current_season" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:500px;margin-left:-250px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Edit Current Season</h3>
	</div>
	<div class="modal-body">
		<select id="modal_edit_current_season_select">
			<?php
				foreach ($season_ids as $season_id) {
					print "<option>{$season_id}</option>";
				}
			?>
		</select>
		<div class="control-group help-inline error">
			<span class="help-inline" id="modal_edit_current_season_result"></span>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
		<button class="btn btn-primary" onclick="save_current_season();">Save</button>
		<script type="text/javascript">

			$("#modal_edit_current_season").on("show", function () {
				$("#modal_edit_current_season_result").html("");
				$("#modal_edit_current_season_select").val(<?php print $current_season_quoted; ?>);
			})

			function save_current_season() {
				var season = $("#modal_edit_current_season_select").val();
				if (season == <?php print $current_season_quoted; ?>) {
					$("#modal_edit_current_season").modal("hide");
				} else {
					$("#modal_edit_current_season_result").html("Saving...");

					$.ajax({
						url: "admin_settings_save.php",
						data: {
							season: season
						},
						type: "POST",
						dataType: "json",
						success: function(json) {
							if ((typeof(json.valid) != "boolean") || (typeof(json.msg) != "string")) {
								var html = "Error: Invalid JSON<br>";
								$("#modal_edit_current_season_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else if (!json.valid) {
								var html = "Error: " + json.msg + "<br>";
								$("#modal_edit_current_season_result").html(html + "<pre>" + JSON.stringify(json) + "</pre>");
							} else {  
								$("#modal_edit_current_season_result").html("Saved.");
								$("#modal_edit_current_season").modal("hide");
								add_alert("success", "Settings Saved.");
								reload_seasons = true;
								load_tab("settings");
							} 
						},
						error: function(xhr, status, thrown) {
							$("#modal_edit_current_season_result").html("Error returned from ajax: " + thrown);
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


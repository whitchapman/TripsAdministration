<?php

	include "check_login.php";

	//-----------------------------------------------------------------

?>
<!DOCTYPE html>
<html lang="en"><head>
	<meta charset="utf-8">
	<title>Administration</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
	<link href="css/datepicker.css" rel="stylesheet" media="screen">
	<link href="css/docs.css" rel="stylesheet">

	<script type="text/javascript">
		
	</script>
	<script type="text/javascript" src="../js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
	<![endif]-->
	<style>
	.modal {
		max-height: 800px;
	  width: 760px;
	  margin-left: -380px;
	}
	</style>
</head>
<body>

	<!-- Navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="nav-collapse collapse">
					<p class="navbar-text pull-right">
						Logged in as General User
					</p>
					<ul class="nav">
						<li class="">
							<a href="../index.php" target="_blank">Home</a>
						</li>
						<li class="">
							<a href="./member.php">Member</a>
						</li>
						<li class="active">
							<a href="./admin.php">Admin</a>
						</li>
						<li class="">
							<a href="./logout.php" onclick="return false;">Logout</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- Subhead -->
	<header class="jumbotron subhead" id="overview">
		<div class="container">
			<h1>Admin Page</h1>
		</div>
	</header>

	<div class="container">
		<div class="row">

			<div class="span3 bs-docs-sidebar">
				<ul id="sidenav_tab" class="nav nav-list bs-docs-sidenav">
					<li class="active"><a href="#welcome" data-toggle="tab"><i class="icon-chevron-right"></i> Welcome</a></li>
					<li><a href="#settings" data-toggle="tab"><i class="icon-chevron-right"></i> Settings</a></li>
					<li><a href="#seasons"  data-toggle="tab"><i  class="icon-chevron-right"></i> Seasons</a></li>
					<li><a href="#sites"  data-toggle="tab"><i  class="icon-chevron-right"></i> Sites</a></li>
					<li><a href="#trips" data-toggle="tab"><i class="icon-chevron-right"></i> Trips</a></li>
					<li><a href="#members" data-toggle="tab"><i class="icon-chevron-right"></i> Members</a></li>
					<li><a href="#signups" data-toggle="tab"><i class="icon-chevron-right"></i> Signups</a></li>
				</ul>
			</div>

			<div class="span9">
				<br>
				<div>
					<span id="alert_info" class="alert alert-info" style="display:none;"></span>
					<span id="alert_success" class="alert alert-success" style="display:none;"></span>
					<span id="alert_error" class="alert alert-error" style="display:none;"></span>
					<script type="text/javascript">

						function add_alert(level, str) {
							$("#alert_"+level).html(str);
							$("#alert_"+level).show();
							window.setTimeout(function () {
								$("#alert_"+level).hide();
							}, 3000);
						}

						function clear_alert(level) {
							$("#alert_"+level).hide();
						}

					</script>
				</div>
				<br>
				<div class="tab-content">

					<div class="tab-pane active" id="welcome">
						<div class="hero-unit">
							<h2>Welcome to the Admin Page!</h2>
							<p>The menu at the left indicates what functionality is currently available to you.
								<ul>
									<li>Click on "Settings" to view or edit the current season.</li>
									<li>Click on "Seasons" to view, edit, or copy all seasons/trips.</li>
									<li>Click on "Sites" to view, edit, or copy trips by site.</li>
									<li>Click on "Trips" to add a new trip.</li>
								</ul>
							</p>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="settings">
						<div id="settings_results">
							<h3>Settings</h3>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="seasons">
						<div id="seasons_results">
							<h3>Seasons</h3>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="sites">
						<div id="sites_results">
							<h3>Sites</h3>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane hero-unit" id="trips">
						<div class="tabbable" id="tabs_trips">
							<div style="display:none;">
								<ul id="trips_panel_tab" class="nav nav-tabs">
									<li><a href="#tabs_trips_panel_standard" data-toggle="tab"></a></li>
									<li><a href="#tabs_trips_panel_edit" data-toggle="tab"></a></li>
									<li><a href="#tabs_trips_panel_results" data-toggle="tab"></a></li>
								</ul>
							</div>
							<div class="tab-content">
								<div id="tabs_trips_panel_standard" class="tab-pane active">
									<p>Add a new trip: <button class="btn btn-primary" type="button" onclick="load_modal_trip_add();">Add Trip</button></p>
									<p>or</p>
									<p>View trips in the context of:
										<ul>
											<li><a href="#" onclick="open_tab('seasons'); return false;">Seasons</a></li>
											<li><a href="#" onclick="open_tab('sites'); return false;">Sites</a></li>
										</ul>
									</p>
								</div>
								<div id="tabs_trips_panel_results" class="tab-pane">
									<p>You have just created <span id="recently_saved_trip_str" style="font-weight:bold;"></span></p>
									<span id="recently_saved_trip_msg"></span>
									<p>View and then edit your new trip in the context of:
										<ul>
											<li><a href="#" onclick="open_tab_seasons(); return false;">Seasons</a></li>
											<li><a href="#" onclick="open_tab_sites(); return false;">Sites</a></li>
										</ul>
									</p>
								</div>
								<div id="tabs_trips_panel_edit" class="tab-pane"></div>
							</div><!-- tab-content -->
						</div><!-- tabbable -->
					</div><!-- trips tab-pane -->

					<div class="tab-pane" id="members">
						<div id="members_results">
							<h3>Members</h3>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="signups">
						<div id="signups_results">
							<h3>Signups</h3>
						</div>
					</div><!-- tab-pane -->

				</div><!-- tab-content -->

			</div><!-- span9 -->
		</div><!-- row -->
	</div><!-- container -->

	<div id="modal_container_trip_add"></div>
	<div id="modal_container_trip_copy"></div>

	<!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript">

		//in order to be called/accessed from a loaded div, vars need to be outside jquery_ready function
		var reload_settings = true;
		var reload_seasons = true;
		var reload_sites = true;
		var reload_trips = false;

		//set when done saving a trip so can manually open a tab focused on this trip
		var recently_saved_trip;
	
		function open_tab(tab_name) {
			//alert(tab);
			$('#sidenav_tab a[href="#'+tab_name+'"]').tab('show');
		}
	
		function load_tab(tab_name, trip_arg) {
			if (typeof(trip_arg) == "number") {
				trip = trip_arg.toString();
			} else if (typeof(trip_arg) == "string") {
				trip = trip_arg;
			} else {
				trip = "";
			}
			$("#"+tab_name+"_results").html("Loading...");
			$.ajax({
				url: "admin_"+tab_name+"_load.php",
				data: {
					trip: trip
				},
				type: "POST",
				dataType: "html",
				success: function(html) {
					$("#"+tab_name+"_results").html(html);
				},
				error: function(xhr, status, thrown) {
					$("#"+tab_name+"_results").html("Error loading "+tab_name+" tab: " + thrown);
				},
				complete: function(xhr, status) {
					//alert("The request is complete!");
				}
			});
		}
	
		function open_tab_seasons() {
			//reload and show tab manually so that trip is showing
			reload_seasons = false;
			load_tab("seasons", recently_saved_trip);
			open_tab("seasons");
		}
	
		function open_tab_sites(trip_arg) {
			//reload and show tab manually so that trip is showing
			reload_sites = false;
			load_tab("sites", recently_saved_trip);
			open_tab("sites");
		}
	
		function load_modal_trip_add(season_arg, site_arg) {
			if (typeof(season_arg) == "number") {
				season = season_arg.toString();
			} else if (typeof(season_arg) == "string") {
				season = season_arg;
			} else {
				season = "";
			}
			if (typeof(site_arg) == "number") {
				site = site_arg.toString();
			} else if (typeof(site_arg) == "string") {
				site = site_arg;
			} else {
				site = "";
			}
			//add_alert("info", "Loading...");
			$.ajax({
				url: "admin_trip_add_load.php",
				data: {
					season: season,
					site: site
				},
				type: "POST",
				dataType: "html",
				success: function(html) {
					//clear_alert("info");
					//add_alert("success", "Loaded.");
					$("#modal_container_trip_add").html(html);
					$("#modal_trip_add").modal("show");
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
	
		function load_modal_trip_copy(trip) {
			if (typeof(trip) != "number") {
				add_alert("error", "Invalid Trip");
			} else {
				//$("#modal_container_trip_copy").html("Loading...");
				//add_alert("info", "Loading...");
				$.ajax({
					url: "admin_trip_copy_load.php",
					data: {
						trip: trip
					},
					type: "POST",
					dataType: "html",
					success: function(html) {
						//clear_alert("info");
						//add_alert("success", "Loaded.");
						$("#modal_container_trip_copy").html(html);
						$("#modal_trip_copy").modal("show");
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
		}
	
		$(function(){
	
			//sidebar
			window.setTimeout(function () {
				$(".bs-docs-sidenav").affix({
					offset: {
						top: function () { return $(window).width() <= 980 ? 210 : 80 },
						//bottom: 270
					}
				})
			}, 100);
	
	
			$('#sidenav_tab a[href="#settings"]').on('show', function () {
				if (reload_settings) {
					reload_settings = false;
					load_tab("settings");
				}
			});
	
			$('#sidenav_tab a[href="#seasons"]').on('show', function () {
				if (reload_seasons) {
					reload_seasons = false;
					load_tab("seasons");
				}
			});
	
			$('#sidenav_tab a[href="#sites"]').on('show', function () {
				if (reload_sites) {
					reload_sites = false;
					load_tab("sites");
				}
			});
	
			$('#sidenav_tab a[href="#trips"]').on('show', function () {
				if (reload_trips) {
					reload_trips = false;
					$('#trips_panel_tab a[href="#tabs_trips_panel_standard"]').tab('show');
				}
			});
	
	//    $('#sidenav_tab a').on('show', function (e) {
	//      alert(e.target);
	//      alert(e.target.href);
	//    });
	
		});

	</script>
</body></html>

<?php

	include "../utils/global.php";

  //-----------------------------------------------------------------

	//use REQUEST so can accept GET or POST
	$season_id = key_to_string($_REQUEST, "season");

	$trips_href = "preview_season.php?season=".$season_id;
	$trip_href = "preview_trip.php";
	
	include "../main/season.php";
	
?>

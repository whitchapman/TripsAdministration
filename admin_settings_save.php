{
<?php

	include "check_login.php";

	//-----------------------------------------------------------------

	$season_id = post_to_string("season");

	if ((!is_numeric($season_id)) || (intval($season_id) < 1)) {

		print "\"valid\":false,";
		print "\"msg\":\"invalid season [{$season_id}]\"";

	} else {

		//print "trip_id=|".$trip_id."|<br>";
		//print "bullets=|<pre>".$bullets."</pre><br><br>";

		//-----------------------------------------------------------------
		//execute the query
	
		$conn = db_open_conn();

		$sql = "update settings set current_season_id=".$season_id." where settings_id=1";
		db_exec_query($conn, $sql);

		//-----------------------------------------------------------------
		//close connection

		$conn->close();

		//-----------------------------------------------------------------

		print "\"valid\":true,";
		print "\"msg\":\"".$season_id."\"";
		
	}

?>
}
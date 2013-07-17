<?php

	$protocol = $_SERVER["HTTPS"] == "on" ? "https" : "http";
	$host = $_SERVER["HTTP_HOST"];
	$dirname = dirname($_SERVER["PHP_SELF"]);
	$location = $protocol."://".$host.$dirname."/admin.php";

//print "<p>location=".$location."</p>\n";

	header("Location: ".$location);

?>

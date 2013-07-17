<?php

	include "check_login.php";

  //-----------------------------------------------------------------

?>
<!DOCTYPE html>
<html lang="en"><head>
	<meta charset="utf-8">
	<title>Members</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
	<link href="css/docs.css" rel="stylesheet">
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
	<![endif]-->

	<script type="text/javascript">


	</script>

</head>
<body>

	<!-- Navbar	-->
	<div class="navbar navbar-inverse	navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<button type="button" class="btn btn-navbar" data-toggle="collapse"	data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="nav-collapse collapse">
					<p class="navbar-text	pull-right">
						Logged in	as General User
					</p>
					<ul	class="nav">
						<li class="">
							<a href="../index.php" target="_blank">Home</a>
						</li>
						<li class="active">
							<a href="./member.php">Member</a>
						</li>
						<li class="">
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
	    <h1>Member Page</h1>
	  </div>
	</header>

	<div class="container">
		<div class="row">

			<div class="span3	bs-docs-sidebar">
				<ul	id="sidenav_tab" class="nav nav-list bs-docs-sidenav">
					<li	class="active"><a	href="#welcome"	data-toggle="tab"><i class="icon-chevron-right"></i> Welcome</a></li>
					<li><a href="#trips" data-toggle="tab"><i	class="icon-chevron-right"></i> My Trips</a></li>
					<li><a href="#signups" data-toggle="tab"><i	class="icon-chevron-right"></i> My Signups</a></li>
				</ul>
			</div>

			<div class="span9">
				<br>
				<div class="tab-content">


					<div class="tab-pane active" id="welcome">
						<div class="hero-unit">
							<h2>Welcome to the Member Page!</h2>
							<p>Eventually, this section will contain stats, like informing you of the last time you logged on.</p>
						</div>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="trips">
						<h3>My Trips</h3>
					</div><!-- tab-pane -->

					<div class="tab-pane" id="signups">
						<div id="signups_results">
							<h3>My Signups</h3>
						</div>
					</div><!-- tab-pane -->

				</div><!-- tab-content -->

			</div><!-- span9 -->
		</div><!-- row -->
	</div><!-- container -->

	<!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript" src="../js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript">

  $(function(){

		//sidebar
		setTimeout(function	() {
			$(".bs-docs-sidenav").affix({
				offset: {
					top: function	() { return	$(window).width()	<= 980 ? 210 : 80 },
					bottom:	270
				}
			})
		}, 100);



	});

	</script>
</body></html>

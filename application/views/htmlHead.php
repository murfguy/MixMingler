<html>
<head>
	<title>MixMingler</title>
	
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

	<!-- External JavaScript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

	<!-- JS Libraries -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/tether.min.js" ></script>


	<!-- MixMingler Internal Javascript -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/viewControls.js" ></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/formControls.js" ></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/infoCollection.js" ></script>

	<!-- BootStrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

	<!-- jQuery-Confirm v3 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

	<!-- Custom CSS -->
	<?php echo link_tag('assets/css/core.css'); ?>
	<?php echo link_tag('assets/css/communities.css'); ?>
	<?php echo link_tag('assets/css/news.css'); ?>
	<?php echo link_tag('assets/css/types.css'); ?>
</head>

<body>
	<header>
			<!-- Fixed navbar -->
			<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
				<a class="navbar-brand" href="/"><span class="mixBlue">Mix</span>Mingler</a>


				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				
				<div class="collapse navbar-collapse" id="navbarCollapse">
					<ul class="navbar-nav mr-auto">
						<!--<li class="nav-item">
							<a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
						</li>-->
						<?php	if (isset($_SESSION['mixer_user'])) { ?>
								<li class="navItem"><a class="nav-link" style="color: rgb(37,188,235)" href="/user/<?php echo $_SESSION['mixer_user']; ?>"> <?php echo $_SESSION['mixer_user']; ?></a></li>
						<?php } ?>
						<li class="nav-item">
							<a class="nav-link" href="/user/"><i class="fas fa-user"></i> Streamers</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/type/"><i class="fas fa-gamepad"></i> Games</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/community/"><i class="fas fa-users"></i> Communities</a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="/account/" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-user-cog"></i> Site Info</a>
							</a>
							<div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdownMenuLink">
								<a class="nav-link" href="/faq/"><i class="fas fa-question-circle"></i> FAQ</a>
								<a class="nav-link" href="/alpha/"><i class="fas fa-bug"></i> Alpha Info</a>
							</div>
						</li>
						<?php
							if (isset($_SESSION['mixer_user'])) {
								//echo "<li class=\"navItem\"><a class=\"nav-link\" onclick=\"logout()\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
							} else {
								echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/auth/session/\"><span class=\"mixBlue\"><i class=\"fas fa-sign-in-alt\"></i> Login w/ Mixer</span></a></li>";
							}
						?>
							<?php  if (isset($_SESSION['mixer_user'])) { ?>
								
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="/account/" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fas fa-user-cog"></i> Account</a>
							</a>
							<div class="dropdown-menu  bg-dark" aria-labelledby="navbarDropdownMenuLink">
								<a class="nav-link" href="/account/">Account Settings</a>
								<?php if (isset($_SESSION['mingler_role'])) {
							if (in_array($_SESSION['mingler_role'], array('owner','admin','dev'))) { ?>
								<a class="nav-link" href="/admin/" style="color: red;">Site Admin Panel</a>
								<?php } } ?>
								<a class="nav-link" onclick="logout()" style="cursor: pointer"><span class="mixBlue"><i class="fas fa-sign-out-alt"></i> Logout</span></a>
							</div>
						 </li>
					 <?php } ?>
					</ul>
					
				</div>
			</nav>
		</header>




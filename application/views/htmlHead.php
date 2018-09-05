<html>
<head>
	<?php 
		//$ga_tracking = "UA-124100080-3"; // development
		//$ga_tracking = "UA-124100080-2"; // alpha
		//$ga_tracking = "UA-124100080-1"; // alpha

		$allowed_hosts = array('dev.mixmingler.com', 'alpha.mixmingler.com', 'mixmingler.com', 'www.mixmingler.com');
		if (isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
		    //header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
		   // exit;
		}

		switch ($_SERVER['HTTP_HOST']) {
			case "dev.mixmingler.com":
				$ga_tracking = "UA-124100080-3";
				break;
			
			case "alpha.mixmingler.com":
				$ga_tracking = "UA-124100080-2";
				break;

			case "www.mixmingler.com":
			case "mixmingler.com":
				$ga_tracking = "UA-124100080-1";
				break;
		}

		$v = "?v=".$version;
	?>

	<title>MixMingler</title>
	
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_tracking; ?>"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', '<?php echo $ga_tracking; ?>');
	</script>

	<!-- External JavaScript -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	
	<!-- BootStrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	<!-- jquery tablesorter -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/tablesorter/jquery.tablesorter.min.js"></script> 
	<!-- bootstrap-slider -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.2.0/bootstrap-slider.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.2.0/css/bootstrap-slider.min.css" />
	<!-- jquery form validator -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
	<!-- jQuery-Confirm v3 -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
	<!-- bootstrap-toggle -->
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

	<!-- JS Libraries -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/tether.min.js" ></script>

	<!-- MixMingler Internal Javascript -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/viewControls.js<?php echo $v; ?>" ></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/formControls.js<?php echo $v; ?>" ></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/infoCollection.js<?php echo $v; ?>" ></script>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">


	<!-- Custom CSS -->
	<?php echo link_tag('assets/css/core.css'.$v); ?>
	<?php echo link_tag('assets/css/communities.css'.$v); ?>
	<?php echo link_tag('assets/css/users.css'.$v); ?>
	<?php echo link_tag('assets/css/news.css'.$v); ?>
	<?php echo link_tag('assets/css/types.css'.$v); ?>
	<?php echo link_tag('assets/css/infoCards.css'.$v); ?>
	<?php echo link_tag('assets/css/material-switch.css'.$v); ?>
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
								<?php if (isset($_SESSION['site_role'])) {
							if (in_array($_SESSION['site_role'], array('owner','admin','dev'))) { ?>
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




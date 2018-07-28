<html>
<head>
	<title>MixMingler</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

	<!-- BootStrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

  <!-- jQuery Form Validator -->
  <link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/theme-default.min.css"
    rel="stylesheet" type="text/css" />

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
            <?php
              if (isset($_SESSION['mixer_user'])) {
                echo "<li class=\"navItem\" ><a class=\"nav-link\" style=\"color: rgb(37,188,235)\" href=\"/user/".$_SESSION['mixer_user']."\"> ".$_SESSION['mixer_user']."</a></li>";
              }
            ?>
            <li class="nav-item">
              <a class="nav-link" href="/user/"><i class="fas fa-user"></i> Streamers</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/type/"><i class="fas fa-gamepad"></i> Games</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/community/"><i class="fas fa-users"></i> Communities</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/faq/"><i class="fas fa-question-circle"></i> FAQ</a>
            </li>
            <?php
              if (isset($_SESSION['mixer_user'])) {
   
                echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/account/\"><i class=\"fas fa-user-cog\"></i> Account</a></li>";
              }
              ?>

            <li class="nav-item">
              <a class="nav-link" href="#" style="color: #f59292;"><i class="fas fa-cog"></i> Admin</a>
            </li>
             <?php if (isset($_SESSION['mingler_role'])) {
               if (in_array($_SESSION['mingler_role'], array('owner','admin','dev'))) {
                 echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/admin/\" style=\"color: #f59292;\"><i class=\"fas fa-cog\"></i> SiteAdmin</a></li>";
               }
             }
            ?>
          </ul>
           <ul class="nav navbar-nav navbar-right">
          <?php
              if (isset($_SESSION['mixer_user'])) {
                echo "<li class=\"navItem\"><a class=\"nav-link\" onclick=\"logout()\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
              } else {
                echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/auth/session/\"><span class=\"mixBlue\"><i class=\"fas fa-sign-in-alt\"></i> Login w/ Mixer</span></a></li>";
              }
            ?>
            <li class="nav-item">
              <a class="nav-link" href="/alpha/"><i class="fas fa-bug"></i> Alpha Info</a>
            </li>
          </ul>
        </div>
      </nav>
    </header>




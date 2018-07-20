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
                echo "<li class=\"navItem\" ><a class=\"nav-link\" style=\"color: rgb(37,188,235)\" href=\"/user/".$_SESSION['mixer_user']."\">".$_SESSION['mixer_user']."</a></li>";
              }
            ?>
            <li class="nav-item">
              <a class="nav-link" href="/user/">Streamers</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/type/">Games</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/community/">Communities</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/faq/">FAQ</a>
            </li>
            <?php
              if (isset($_SESSION['mixer_user'])) {
                echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/account/\">Account</a></li>";
                echo "<li class=\"navItem\"><a class=\"nav-link\" onclick=\"logout()\">Logout</a></li>";
              }

             if (isset($_SESSION['mingler_role'])) {
               if (in_array($_SESSION['mingler_role'], array('owner','admin','dev'))) {
                 echo "<li class=\"navItem\"><a class=\"nav-link\" href=\"/admin/\" style=\"color: red;\">Admin</a></li>";
               }
             }
            ?>
          </ul>
        </div>
      </nav>
    </header>




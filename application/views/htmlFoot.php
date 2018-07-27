<footer class="footer">
	<div class="container">
		<p>Not associated with Mixer or Microsoft.</p>
		<p>All features/styles currently present are not finalized. By using this site you agree that any information/input you provide may kept/deleted/used at the discretion of the development team.</p>
		<p>Load Time: <?php echo $this->benchmark->elapsed_time();?></p>
		<?php
			if (!empty($version)) {
				echo "<p>Version: <a href=\"/alpha/\">$version</a></p>";
			} else {
				echo "<p>No version data.</p>";
			}
		?>
	</div>
</footer>

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
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/controls.js" ></script>
</body>
</html>
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
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/controls.js" ></script>
</body>
</html>
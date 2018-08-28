<button class="btn btn-info btn-lg" onclick="topFunction()" id="topButton" data-toggle="tooltip" title="Go to top"><i class="fas fa-angle-double-up"></i></button>
<footer class="footer">
	<div class="container">
		<div class="row" style="text-align: center;">
			<p class="col">&copy; 2017-<?php echo date('Y'); ?> : Davis Murphy</p>
			<p class="col">Current Site Version: <a href="/alpha"><?php echo $version ?></a></p>
			<p class="col">Page Load Time: <?php echo $this->benchmark->elapsed_time();?></p>
		</div>
		 <p><span style="color:#ff8e8e;">MixMingler is not associated with or endorsed by <a href="https://mixer.com">Mixer.com</a> or <a href="https://www.microsoft.com">Microsoft</a>.</span></p>
		<p><span class="mixBlue">Alpha Test:</span> All features/styles currently present are not finalized. By using this site you agree that any information/input you provide may kept/deleted/used at the discretion of the development team.</p>
	</div>
</footer>
	<script type="text/javascript" src="<?php echo base_url();?>assets/js/controls.js" ></script>
</body>
</html>
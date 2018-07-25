<main role="main" class="container">
<div class="container">
	<div id="userHeader" class="pageHeader">
		<h1>Create a Community</h1>
	</div>

	<h2>Criteria for Creation</h2>
	<ul>
		<?php 
			$success = true;

			if ($creationCriteria['bannedFromCreation'] == false) {
				if ($creationCriteria['agedEnough'] == false) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Failed Criteria:</strong> Your mixer account is too young.</div>";
					$success = false;
				} 

				if ($creationCriteria['recentlyFounded'] == true) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Failed Criteria:</strong> You founded a community too recently.</div>";
					$success = false;
				} 


				if ($creationCriteria['pendingApproval'] == true) {
					echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Failed Criteria:</strong> You have a community awaiting approval.</div>";
					$success = false;
				} 
			} else {
				echo "<div class=\"alert alert-danger\" role=\"alert\"><strong>Failed Criteria:</strong> You are banned from making communities.</div>";
				$success = false;
			}

			if ($success) {
				//echo "<li>You meet all the current criteria to create a community!";
				echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Congrats!</strong> You meet all the criteria to create a community!</div>";
			} 


			
		?>
	</ul>

	<h2>Form Inputs</h2>
	<ul>	
		<li>Community Name</li>
		<li>Parent Category</li>
		<li>URL Id</li>
		<li>Short Description</li>
		<li>Long Description</li>
	</ul>
</div>
</main>
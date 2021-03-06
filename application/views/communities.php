<main role="main" class="container">
<div class="container">
	<div class="pageHeader">
		<h1><span class="mixBlue">Mix</span>Mingler Communities <?php echo devNotes('communities'); ?></h1>
	</div>

	<nav id="categoryNav">
		<a class="commToggle" category="all">All</a> |
		<a class="commToggle" category="style">Style</a>|
		<a class="commToggle" category="content">Content</a> |
		<a class="commToggle" category="platform">Platforms</a> |
		<a class="commToggle" category="region">Regional</a> |
		<a class="commToggle" category="games">Games</a> |
		<a class="commToggle" category="streamers">Streamers</a> |
		<a class="commToggle" category="misc">Misc</a>|
		<a href="/community/create/">Request a New Community!</a>
	</nav>

	<div id="communitiesList" class="communityCategory">

		<!--<p><button class="btn btn-lg btn-primary" onclick="window.location.href = '/community/create/';">Make Your Own Community!</button></p>-->



		<h2><span class="mixBlue">All</span> Communities</h2>
		<p id="communityDescription">Well, here's all the communities! If you want to drill down further, just check the categories above.</p>
		<div class="streamerList row">
			<?php
				if (count($communities) > 0) {
					$baseParams = ['size'=>'med','kind'=>'community'];

					foreach ($communities as $community) { 
						$communityParams = [
							'url' => "/community/$community->Slug",
							'name' => $community->Name,
							'category' => $community->CategorySlug,
							'stats' => ['online'=>$community->MembersOnline, 'members' => $community->MemberCount],
							'extraClasses'=>['communityListing'],
							'cover' => null,
							'tooltip' => $community->Summary];
						if (!empty($community->CoverFileType)) {
							$communityParams['cover'] = '/assets/graphics/covers/'.$community->Slug.'.'.$community->CoverFileType;} 


						echo card(array_merge($baseParams, $communityParams));
					}
				} else {
					echo "<p>No communities. Odd.</p>";
				}
			?>
		</div>
	</div>
</div>


	
</main>
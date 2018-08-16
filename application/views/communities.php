<main role="main" class="container">
<div class="container">
	<div class="pageHeader">
		<h1><span class="mixBlue">Mix</span>Mingler Communities</h1>
	</div>
		<p class="devNote"  data-toggle="tooltip" title="Planned for v0.3" data-placement="left">General updates and full fledged community features are currently under development. See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>


	<nav id="categoryNav">
		<a class="commToggle" category="all">All</a> |
		<a class="commToggle" category="style">Style</a>|
		<a class="commToggle" category="content">Content</a> |
		<a class="commToggle" category="platform">Platforms</a> |
		<a class="commToggle" category="region">Regional</a> |
		<a class="commToggle" category="games">Games</a> |
		<a class="commToggle" category="streamers">Streamers</a> |
		<a class="commToggle" category="misc">Misc</a>
	</nav>

	<div id="communitiesList" class="communityCategory">
		<button class="btn btn-lg btn-primary" onclick="window.location.href = '/community/create/';">Make Your Own Community!</button>
		<h2><span class="mixBlue">All</span> Communities</h2>
		<p id="communityDescription">Well, here's all the communities, ranked by number of members. If you want to drill down further, just check the categories above.</p>
		<div class="streamerList row">
			<?php
				if (count($communities) > 0) {
					$baseParams = ['size'=>'med','kind'=>'community'];

					foreach ($communities as $community) { 
						$communityParams = [
							'url' => "/community/$community->Slug",
							'name' => $community->Name,
							'category' => $community->CategorySlug,
							'cover' => '/assets/graphics/covers/'.$community->Slug.'.jpg',
							'stats' => ['members' => $community->MemberCount],
							'extraClasses'=>['communityListing']];

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
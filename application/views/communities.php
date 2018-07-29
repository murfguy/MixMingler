<main role="main" class="container">
<div class="container">
	<div class="pageHeader">
		<h1><span class="mixBlue">Mix</span>Mingler Communities</h1>
	</div>
		<p class="devNote"  data-toggle="tooltip" title="Planned for v0.3" data-placement="left">General updates and full fledged community features are planned for development during v0.3 (Communities). See the <a href="/alpha/">Alpha Information Page</a> for more info. Bugs or incomplete implementations are expected in this area.</p>


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
		<h2><span class="mixBlue">All</span> Communities</h2>
		<p id="communityDescription">Well, here's all the communities, ranked by popularity. If you want to drill down further, just check the categories above.</p>
		<div class="row">
			<?php
				if (count($communities) > 0) {
					foreach ($communities as $community) { ?>
						<div class="col-sm communityListing <? echo $community->category_slug; ?>">
						<h3><a href="/community/<? echo $community->slug; ?>"><? echo $community->long_name; ?></a></h3>
						<p><? echo $community->summary; ?><br><span class="muted-text"><? echo $community->memberCount; ?> members</span></p>
						</div>
					<?php }
				} else {
					echo "<p>No communities. Odd.</p>";
				}
			?>
		</div>
	</div>
</div>


	<div class="plans">
		<p><b>Plans/Ideas for this page:</b></p>
		<ul>
			<li>Toggle visible communities based on navigation</li>
			<li>Include links to request a new community</li>
			<li>Include online counts? (API research)</li>
		</ul>
	</div>
</main>
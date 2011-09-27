<?php if ($total): ?>
<nav>
	<ul class="nav">
		<?php foreach ($pages as $page): ?>
		<?php
			$url = ($page->default) ? 'index.php' : 'index.php?page='.$page->slug;
		?>
		<li><a href="<?=$url?>"><?=$page->title;?></a></li>
		<?php endforeach ?>
	</ul>
</nav>
<?php endif ?>
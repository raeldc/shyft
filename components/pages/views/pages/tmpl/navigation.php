<?php if ($total): ?>
<nav>
	<ul class="nav">
		<?php foreach ($pages as $page): ?>
		<li><a href="<?=@route('index.php?mode=site&page='.$page->permalink);?>"><?=$page->title;?></a></li>
		<?php endforeach ?>
	</ul>
</nav>
<?php endif ?>
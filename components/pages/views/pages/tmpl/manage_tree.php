<?=@helper('listbox.groups', array('selected' => $state->group))?>

<menu>
	<a class="btn" data-remote href="<?=@route('page=&view=page&layout=form')?>">      
		New
	</a>

	<a class="btn" data-remote href="<?=@route('page=&view=group&layout=form')?>">
		New Group
	</a>
</menu>

<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('&mode=admin&com=pages&page='.$page->id)?>"><?=$page->title;?></a></li>
	<?php endforeach ?>
</ul>

<div id="page-content-types" class="modal hide fade">
	<div class="modal-header">
		<a href="#" class="close">&times;</a>
		<h3>Choose a Page</h3>
	</div>
	<div class="modal-body">
		<h5>Custom Content Types</h5>
		<ul class="text-grid">
			<li>
				<img src="media://application/app-generic.png" alt="Static Page">
				<h5>Static Page</h5>
				<p>Basic Static HTML</p>
			</li>
			<li>
				<h5>Static Page</h5>
				<p>Basic Static HTML</p>
			</li>
			<li>
				<h5>Static Page</h5>
				<p>Basic Static HTML</p>
			</li>
		</ul>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn primary">Choose</a>
	</div>
</div>

<?php endif ?>
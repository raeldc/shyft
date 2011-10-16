<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('&mode=admin&page='.$page->slug)?>"><?=$page->title;?></a></li>
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
				<img src="media://application/generic-app.png" alt="Static Page">
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
			<li>
				<h5>Static Page</h5>
				<p>Basic Static HTML</p>
			</li>
			<li>
				<h5>Static Page</h5>
				<p>Basic Static HTML</p>
			</li>
		</ul>

		<h5>Content Types from Components</h5>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

		<h5>Forms and Workflows</h5>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

		<h5>Components</h5>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn primary">Choose</a>
	</div>
</div>

<?php endif ?>
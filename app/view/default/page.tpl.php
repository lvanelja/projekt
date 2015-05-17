<div class="row">
	<div class="col-xs-12">
		<div class="page-header">
			<h1><?=$title?></h1>
		</div>
	</div>
</div>

<div class="row">
<?php if (isset($links)) : ?>
	<div class="col-xs-3">
		<div class="list-group">
		<?php foreach ($links as $link) : ?>
			<a href="<?=$this->url->create($link["href"])?>" class="list-group-item <?php if(isset($link["active"])): ?>active<?php endif; ?>" ><?=$link["text"]?></a>
		<?php endforeach; ?>
		</div>
	</div>
	<div class="col-xs-9">
<?php else: ?>
	<div class="col-xs-12">
<?php endif; ?>
		<?=$content?>
	</div>
</div>
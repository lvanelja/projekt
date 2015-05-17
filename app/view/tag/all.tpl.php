<div class="row">
	<div class="col-xs-12">
		<div class="page-header">
			<h1><?=$title?></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php foreach ($tags as $tag) : ?>
			<a href="<?=$this->url->create("posts/tag/{$tag->name}")?>" class="btn btn-primary"><i class="fa fa-tag fa-fw"></i> <?=$tag->name?> (<?=$tag->postCount?>)</a>
		<?php endforeach; ?>
	</div>
</div>
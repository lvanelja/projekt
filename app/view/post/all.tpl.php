<div class="row">
	<div class="col-xs-12">
		<div class="page-header">
			<a href="<?= $this->url->create("posts/new") ?>" class="btn btn-primary pull-right">Ställ fråga</a>
			<div class="dropdown pull-right right-margin">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					Sortera efter
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
					<li role="presentation"><a role="menuitem" tabindex="-1" href="?orderby=created">Datum</a></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="?orderby=voteCount">Antal Röster</a></li>
				</ul>
			</div>
			<h1><?=$title?></h1>
		</div>
	</div>
</div>

<?php if(count($posts) > 0) : ?> 
<?php foreach ($posts as $post ) : ?>
<div class="row question">
	<div class="col-xs-1 text-center">
		<big><strong><?= $post->voteCount ?></strong></big><br/>
		röster
	</div>
	<div class="col-xs-1 text-center">
		<big><strong><?= $post->answerCount ?></strong></big><br/>
		svar
	</div>
	<div class="col-xs-10">
		<h3><a href="<?=$this->url->create("posts/view/" . $post->id) ?>"><?= $post->title ?></a></h3>
		<p><?=$post->body ?></p>
		<a href="<?=$this->url->create("users/profile/{$post->user_id}")?>">
			<img src="<?=getUserImageUrl($post->user_email, 20) ?>" class="img-rounded" />
			<strong><?=$post->user_name?></strong><br/>
			<p><?=$post->created?></p>
		</a>
		<?php if(isset($post->tags)) : ?>
		<?php foreach($post->tags as $tag) : ?>
			<a href="<?=$this->url->create("posts/tag/{$tag}")?>" class="label label-primary"><?=$tag ?></a>
		<?php endforeach;?>
		<?php endif; ?>
	</div>
</div>
<hr>
<?php endforeach; ?>
<?php else : ?>
	<p class="lead text-center"><em>Inga inlägg är skapade än :(</em></p>
	<p class="text-center"><a href="<?= $this->url->create("posts/new") ?>" class="btn btn-primary">Skriv ett nytt!</a></p>
<?php endif; ?>
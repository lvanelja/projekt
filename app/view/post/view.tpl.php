<div class="page-header">
<div class="pull-right">
	<?php if(isset($post->tags)) : ?>
	<?php foreach($post->tags as $tag) : ?>
		<a href="<?=$this->url->create("posts/tag/{$tag}")?>" class="label label-primary"><?=$tag ?></a>
	<?php endforeach;?>
	<?php endif; ?>
	</div>
	<h2><?=$post->title?></h2>
</div>
<div class="row post">
	<div class="col-xs-1">
		<div class="text-center">
			<a href="<?=$this->url->create("votes/upVote/{$post->id}")?>" class="btn btn-success <?php if(!$this->UsersController->isLoggedIn()) echo "disabled"?>"><i class="fa fa-thumbs-up"></i></a><br>
			<span class="lead score"><?=$post->voteScore?></span><br>
			<a href="<?=$this->url->create("votes/downVote/{$post->id}")?>" class="btn btn-danger <?php if(!$this->UsersController->isLoggedIn()) echo "disabled"?>"><i class="fa fa-thumbs-down"></i></a><br>
		</div>
	</div>
	<div class="col-xs-9">
		<?=$this->textFilter->doFilter($post->body, 'shortcode, markdown')?>
	</div>
	<div class="col-xs-2">
		<p><?=$post->created?></p>
		<a href="<?=$this->url->create("users/profile/{$post->user_id}")?>">
			<img src="<?=getUserImageUrl($post->user_email, 20) ?>" class="img-rounded" />
			<strong><?=$post->user_name?></strong><br/>
		</a>
	</div>
</div>
<div class="row comments">
	<div class="col-xs-11 col-xs-offset-1">
	<?php if(count($post->comments) > 0) : ?>
	<?php foreach ($post->comments as $comment) : ?>
		<div class="comment hover">	
			<?=$this->textFilter->doFilter($comment->body, 'shortcode, markdown')?> - <a href="<?=$this->url->create("users/profile/".$comment->user_id)?>"><?=$comment->user_name?></a> - <span class="text-muted"><?=$comment->created ?></span>
		</div>
	<?php endforeach; ?>
	<? endif; ?>
	<div class="comment<?php if(count($post->comments) === 0) echo " no-border"; ?>">
		<a href="<?=$this->url->create("comments/new/{$post->id}")?>" class="text-muted">Skriv en kommentar</a>
	</div>
	</div>
</div>
<hr />

<div class="page-header">
	<h3><?=count($answers)?> Svar</h3>
</div>

<?php if(count($answers) > 0) : ?> 
<?php foreach ($answers as $post ) : ?>
<div class="row">
	<div class="col-xs-1">
		<div class="text-center">
			<a href="<?=$this->url->create("votes/upVote/{$post->id}")?>" class="btn btn-success <?php if(!$this->UsersController->isLoggedIn()) echo "disabled"?>"><i class="fa fa-thumbs-up"></i></a><br>
			<span class="lead score"><?=$post->voteScore?></span><br>
			<a href="<?=$this->url->create("votes/downVote/{$post->id}")?>" class="btn btn-danger <?php if(!$this->UsersController->isLoggedIn()) echo "disabled"?>"><i class="fa fa-thumbs-down"></i></a><br>
		</div>
	</div>
	<div class="col-xs-9">
		<?=$this->textFilter->doFilter($post->body, 'shortcode, markdown')?>
	</div>
	<div class="col-xs-2">
		<p><?=$post->created?></p>
		<a href="<?=$this->url->create("users/profile/" . $post->user_id)?>">
			<img src="<?=getUserImageUrl($post->user_email, 20) ?>" class="img-rounded" />
			<strong><?=$post->user_name?></strong><br/>
		</a>
	</div>
</div>
<div class="row comments">
	<div class="col-xs-11 col-xs-offset-1">
	<?php if(count($post->comments) > 0) : ?>
	<?php foreach ($post->comments as $comment) : ?>
		<div class="comment">	
			<?=$this->textFilter->doFilter($comment->body, 'shortcode, markdown')?> - <a href="<?=$this->url->create("users/profile/".$comment->user_id)?>"><?=$comment->user_name?></a> - <span class="text-muted"><?=$comment->created ?></span>
		</div>
	<?php endforeach; ?>
	<? endif; ?>
	<div class="comment<?php if(count($post->comments) === 0) echo " no-border"; ?>">
		<a href="<?=$this->url->create("comments/new/{$post->id}")?>" class="text-muted">Skriv en kommentar</a>
	</div>
	</div>
</div>
<hr />
<?php endforeach; ?>
<?php endif; ?>


<?php if($this->UsersController->isLoggedIn()) : ?>

<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="row">
				<div class="col-xs-12">
					<h3>Skriv ett svar </h3>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-1">
					<img src="<?=getUserImageUrl($this->UsersController->getCurrentUser()->email, 100) ?>" class="img-rounded" />
				</div>
				<div class="col-xs-11 answer_form">
					<?= $answer_form ?>
				</div>
			</div>
		</div>
	</div>
</div>
<? else: ?>
<div class="row">
	<div class="col-xs-12">
		<p><a href="<?=$this->url->create("users/login")?>">Logga in</a> för att skriva ett svar.</p>
	</div>
</div>
<?php endif; ?>
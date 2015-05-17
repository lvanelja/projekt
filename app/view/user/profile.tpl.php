<div class="row">
	<div class="col-xs-12">
		<div class="page-header">
			<h1><?=$user->name?></h1>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-3">
		<div class="well">
			<p>
				<img src="<?=getUserImageUrl($user->email, 120) ?>" class="img-circle">
			</p>
			<p>
				<?=$user->profile ?>
			</p>
			<p>
				<strong>Email</strong><br/>
				<?=$user->email ?>
			</p>
		</div>
	</div>

	<div class="col-xs-9">
		<?php if(count($questions) > 0) : ?>
		<h3>Senaste frågor</h3>
		<table class="table table-hover">
		<?php foreach($questions as $question) : ?>
			<tr>
				<td width="60%"><strong><a href="<?=$this->url->create("posts/view/{$question->id}")?>"><?=$question->title?></a></strong></td>
				<td width="15%"><strong><?=$question->voteScore?></strong> poäng</td>
				<td width="25%" class="text-right"><?=$question->created?></td>
			</tr>
		<?php endforeach; ?>
		</table>
		<?php endif; ?>

		<?php if(count($answers) > 0) : ?>
		<h3>Senaste svar</h3>
		<table class="table table-hover">
		<?php foreach($answers as $answer) : ?>
			<tr>
				<td width="60%"><strong><a href="<?=$this->url->create("posts/view/{$answer->parent}")?>"><?=substr($answer->body,0,50)."..."?></a></strong></td>
				<td width="15%"><strong><?=$answer->voteScore?></strong> poäng</td>
				<td width="25%" class="text-right"><?=$answer->created?></td>
			</tr>
		<?php endforeach; ?>
		</table>
		<?php endif; ?>

		<?php if(count($comments) > 0) : ?>
		<h3>Senaste kommentarer</h3>
		<table class="table table-hover">
		<?php foreach($comments as $comment) : ?>
			<tr>
				<td width="75%"><strong><a href="<?=$this->url->create("posts/view/{$comment->post_id}")?>"><?=substr($comment->body,0,50)."..."?></a></strong></td>
				<td width="25%" class="text-right"><?=$comment->created?></td>
			</tr>
		<?php endforeach; ?>
		</table>
		<?php endif; ?>
	</div>

</div>
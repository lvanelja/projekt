	<div class="col-xs-12">
		<div class="well tags">
			<h2>Populära taggar</h2>
			<?php foreach($tags as $tag) : ?>
			<a href="<?=$this->url->create("posts/tag/{$tag->name}") ?>" class="btn btn-primary">
				<i class="fa fa-tag fa-fw"></i> <?=$tag->name?> (<?=$tag->postCount?>)
			</a>
			<?php endforeach; ?>
		</div>
	</div>
<div class="row">
	<div class="col-xs-12">
		<div class="jumbotron">
			<p class="lead"><?=$welcomeMessage?></p>
			<?php if(!$this->UsersController->isLoggedIn()) : ?>
			<a class="btn btn-lg btn-primary" href="<?=$this->url->create("users/register")?>"><i class="fa fa-user-plus fa-fw"></i> Skapa konto</a>
			<?php else : ?>
			<a class="btn btn-lg btn-primary" href="<?=$this->url->create("posts/new")?>">Skriv en fråga</a>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-8">
		<div class="well posts">
			<h2>Senaste frågor</h2>
			<?php foreach($posts as $post) : ?>
				<p>
					<a href="<?=$this->url->create("posts/view/{$post->id}")?>">
						<?=$post->title?>
					</a> - <span class="text-muted"><?=$post->created?></span>
				</p>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="col-xs-4">
		<div class="well users">
			<h2>Aktiva användare</h2>
			<ol>
			<?php foreach($users as $user) : ?>
				<li>
					<a href="<?=$this->url->create("users/profile/{$user->id}")?>">
						<img src="<?=getUserImageUrl($user->email, 15) ?>" class="img-rounded" />
						<strong><?=$user->name?></strong>
						(<?=$user->postCount?> inlägg)
					</a>
				</li>
			<?php endforeach; ?>
			</ol>
		</div>
	</div>
</div>

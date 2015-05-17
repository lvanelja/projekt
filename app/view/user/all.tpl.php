<div class="row">
	<div class="col-xs-12">
		<div class="page-header">
			<h1><?=$title?> <small>(<?=count($users)?>)</small></h1>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<?php if(count($users) > 0) : ?>
		<table class="table table-hover">
			<tbody>
			<?php foreach ($users as $user) : ?>
				<tr style="float:left">
					<td>	<img src="<?=getUserImageUrl($user->email, 30) ?>" class="img-circle">
<a href="<?=$this->url->create("users/profile/{$user->id}") ?>"><?= $user->name ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php else: ?>
		<p><em>Inga användare är registrerade</em></p>
		<?php endif; ?>
	</div>
</div>
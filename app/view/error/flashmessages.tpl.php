<?php if(count($messages) > 0) : foreach ($messages as $message) : ?>
<?php
	if($message["type"] == "error") {
		$message["type"] = "danger"; 
	}
?>
<div class="alert alert-<?=$message["type"]?> alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<?=$message["content"]?>
</div>
<?php endforeach; endif;  ?>

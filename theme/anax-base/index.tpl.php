<!doctype html>
<html class='no-js' lang='<?=$lang?>'>
<head>
<meta charset='utf-8'/>
<title><?=$title . $title_append?></title>
<?php if(isset($favicon)): ?>
	<link rel='icon' href='<?=$this->url->asset($favicon)?>'/>
<?php endif; ?>
<?php foreach($stylesheets as $stylesheet): ?>
<link rel='stylesheet' type='text/css' href='<?=$this->url->asset($stylesheet)?>'/>
<?php endforeach; ?>
<script src='<?=$this->url->asset($modernizr)?>'></script>
</head>

<body>

<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?=$this->url->create("")?>">
				<img src="<?=$this->url->create("favicon.png")?>" width="80" class="pull-left" /> Allt om tennis
				<p style="font-size:12px"> Fråga vad du vill om tennis </p>
			</a>
		</div>

		<?php $this->views->render('navbar')?>
	</div>
</nav>

<div class="container">

<?php if($this->views->hasContent("flashmessages")): ?>
<?php $this->views->render("flashmessages")?>
<?php endif; ?>

<?php if($this->views->hasContent("main")): ?>
<section class="content">
	<?php $this->views->render("main")?>
</section>
<?php endif; ?>

</div>

<?php if($this->views->hasContent("footer")): ?>
	<?php $this->views->render("footer")?>
<?php endif; ?>

<?php if(isset($jquery)):?>
	<script src='<?=$this->url->asset($jquery)?>'></script>
<?php endif; ?>

<?php if(isset($javascript_include)): foreach($javascript_include as $val): ?>
	<script src='<?=$this->url->asset($val)?>'></script>
<?php endforeach; endif; ?>

</body>
</html>
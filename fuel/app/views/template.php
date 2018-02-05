<?php
$dir = (isset($dir))? $dir:'';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<?php if ($dir == 't'): ?>
<?php \Clfunc_Tracking::Tag1(); ?>
<?php endif; ?>

<?php echo View::forge('template_header'); ?>

</head>
<body>
<?php if ($dir == 't'): ?>
<?php \Clfunc_Tracking::Tag2(); ?>
<?php endif; ?>
<div>
	<header>
		<?php $sLink = ($dir != 'adm')? '/'.$dir:'/'.$dir.'/AdminLogin'; ?>
		<span id="site-logo"><a href="<?php echo $sLink; ?>"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>
	</header>

	<div id="content">
	<?php echo $content; ?>
	</div>

	<?php echo $footer; ?>
</div>

<div class="back-alert" id="ajaxErr" style="display: none;">
	<p></p>
	<div class="ajaxErrClose"><i class="fa fa-times"></i></div>
</div>

<?php if (isset($javascript) && is_array($javascript)): ?>
	<?php foreach ($javascript as $jsfile): ?>
		<?php echo Asset::js($jsfile.$sVQ); ?>
	<?php endforeach; ?>
<?php endif; ?>
</body>
</html>

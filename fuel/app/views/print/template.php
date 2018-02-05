<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo View::forge('template_header'); ?>

	<?php echo Asset::js('jquery-ui-datepicker-ja.js'); ?>
	<?php echo Asset::js('jquery.metadata.js'); ?>
	<?php echo Asset::js('jquery.tablesorter.min.js'); ?>

	<?php echo Asset::css('jquery.timepicker.css'); ?>

	<?php if (isset($css) && is_array($css)): ?>
		<?php foreach ($css as $cssfile): ?>
			<?php echo Asset::css($cssfile); ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php \Clfunc_Tracking::Tag1(); ?>
</head>

<body>
<div>
	<?php echo $content; ?>
</div>

<?php if (isset($javascript) && is_array($javascript)): ?>
	<?php foreach ($javascript as $jsfile): ?>
		<?php echo Asset::js($jsfile); ?>
	<?php endforeach; ?>
<?php endif; ?>

</body>
</html>

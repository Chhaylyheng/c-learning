<?php
$aBaseCss = array(
	'sanitize.css',
	'style.css',
	'jquery-ui-1.11.4.min.css',
	'font-awesome.min.css',
	'complement.css',
);
$aIE9Js = array(
	'html5shiv.js',
	'ie9-custom.js',
	'css3-mediaqueries.js',
);
$aBaseJs = array(
	'jquery-1.11.3.min.js',
	'jquery-ui-1.11.4.min.js',
	'jquery.ui.autocomplete.highlight.js',
	'jquery.i18n.properties.min.js',
	'jquery.ui.touch-punch.min.js',
	'common.js',
	'cl.common.js',
	'moment-with-locales.js',
	'moment-timezone-with-data.js',
);

?>
	<title><?php echo ((isset($pagetitle))? $pagetitle.' | ':'').$title; ?></title>

	<link rel="apple-touch-icon" href="<?php echo Asset::find_file('apple-touch-icon.png', 'img').$sVQ; ?>">
	<link rel="shortcut icon" href="<?php echo Asset::get_file('shortcut-icon.png', 'img').$sVQ; ?>" type="image/vnd.microsoft.icon">
	<link rel="icon" href="<?php echo Asset::get_file('favicon.ico', 'img').$sVQ; ?>" type="image/vnd.microsoft.icon">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<?php foreach ($aBaseCss as $f): ?>
	<?php echo Asset::css($f.$sVQ); ?>
	<?php endforeach; ?>

	<!--[if lt IE 9]>
	<?php foreach ($aIE9Js as $f): ?>
	<?php echo Asset::js($f.$sVQ); ?>
	<?php endforeach; ?>
	<![endif]-->

	<?php foreach ($aBaseJs as $f): ?>
	<?php echo Asset::js($f.$sVQ); ?>
	<?php endforeach; ?>

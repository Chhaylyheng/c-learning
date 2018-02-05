<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo View::forge('template_header'); ?>

	<?php if (isset($css) && is_array($css)): ?>
		<?php foreach ($css as $cssfile): ?>
			<?php echo Asset::css($cssfile.$sVQ); ?>
		<?php endforeach; ?>
	<?php endif; ?>

</head>

<body>
<div>
	<header>
		<span id="site-logo"><a href="/g/index"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>
		<a id="system-menu-button" href="#" style="text-align: center;">
			<?php echo Asset::img('icon_setting.png'.$sVQ,array('alt'=>'', 'style'=>'width: 24px; margin-top: 3px;')); ?>
		</a>
	</header>

	<nav id="system-menu">
		<div class="menu-inner">
			<ul>
				<li class="system-menu-account">
					<span class="system-menu-account-text-name">
						<i class="fa fa-sign-in fa-fw"></i> <?php echo (($aGuest['gtName'])? $aGuest['gtName']:__('ゲスト')); ?>
					</span><br>
					<span class="system-menu-account-text-time">
						<i class="fa fa-clock-o fa-fw"></i> <?php echo ($aGuest['gtLastAccess'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aGuest['gtLastAccess']):__('初ログイン'); ?>
					</span>
					<br><?php echo View::forge('selectlang_menu'); ?>
				</li>
				<li><a href="/g/index/logout"><span><?php echo __('ログアウト'); ?></span></a></li>
			</ul>
		</div>
	</nav>

	<div id="content">
		<div id="content-inner">

		<?php if (isset($breadcrumbs)): ?>

		<ul id="breadcrumbs">
			<li><a href="/g/index">Top</a></li>
			<?php foreach ($breadcrumbs as $bc): ?>
				<?php if (isset($bc['link'])): ?>
					<li><a href="/g<?php echo $bc['link']; ?>"><?php echo $bc['name']; ?></a></li>
				<?php else: ?>
					<li><?php echo $bc['name']; ?></li>
				<?php endif;?>
			<?php endforeach; ?>
		</ul>

		<?php endif; ?>

		<?php if (isset($pagetitle)): ?>
		<h1>
			<?php if (isset($subtitle)): ?>
				<p class="font-size-70 mb8"><?php echo $subtitle; ?></p>
			<?php
				elseif (!is_null($aClass)):
					$sPWH = '';
					$sSep = '';
					if ($aClass['dpNO'])
					{
						$sPWH .= $aPeriod[$aClass['dpNO']];
						$sSep = '/';
					}
					if ($aClass['ctWeekDay'])
					{
						$sPWH .= $sSep.$aWeekDay[$aClass['ctWeekDay']];
						$sSep = '/';
					}
					if ($aClass['dhNO'])
					{
						$sPWH .= $sSep.$aHour[$aClass['dhNO']];
					}
					if ($sPWH)
					{
						$sPWH = '（'.$sPWH.'）';
					}
			?>
				<p class="font-size-70 mb8"><a href="/g/index"><i class="fa fa-book"></i> <?php echo $aClass['ctName'].$sPWH; ?></a></p>
			<?php endif; ?>
			<?php if (isset($classtitle)): ?>
				<?php echo $classtitle; ?>
			<?php else: ?>
				<?php echo $pagetitle; ?>
			<?php endif; ?>
		</h1>
		<?php endif; ?>

		<?php if (isset($ses['SES_S_ERROR_MSG'])): ?>
		<p class="error-box mb16"><?php echo nl2br($ses['SES_S_ERROR_MSG']); ?></p>
		<?php Session::delete('SES_S_ERROR_MSG'); ?>
		<?php endif; ?>
		<?php if (isset($ses['SES_S_NOTICE_MSG'])): ?>
		<div class="info-box tmp mb16">
			<p><?php echo nl2br($ses['SES_S_NOTICE_MSG']); ?></p>
			<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png'.$sVQ,array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
		</div>
		<?php Session::delete('SES_S_NOTICE_MSG'); ?>
		<?php endif; ?>

		<?php echo $content; ?>
		</div>
	</div>

	<?php echo $footer; ?>
	<?php if (isset($subwindow)) { echo $subwindow; } ?>

</div>

<div class="back-alert" id="ajaxErr" style="display: none;">
	<p></p>
	<div class="ajaxErrClose"><i class="fa fa-times"></i></div>
</div>

<script type="text/javascript" src="<?php echo CL_MAP_URL; ?>"></script>
<?php if (isset($javascript) && is_array($javascript)): ?>
	<?php foreach ($javascript as $jsfile): ?>
		<?php echo Asset::js($jsfile.$sVQ); ?>
	<?php endforeach; ?>
<?php endif; ?>
</body>
</html>

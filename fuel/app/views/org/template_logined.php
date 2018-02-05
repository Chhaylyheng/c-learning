<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo View::forge('template_header'); ?>

	<?php echo Asset::js('jquery.metadata.js'.$sVQ); ?>
	<?php echo Asset::js('jquery.tablesorter.min.js'.$sVQ); ?>

	<?php if (isset($css) && is_array($css)): ?>
		<?php foreach ($css as $cssfile): ?>
			<?php echo Asset::css($cssfile.$sVQ); ?>
		<?php endforeach; ?>
	<?php endif; ?>

</head>

<body>
<div>
	<header>
		<span id="site-logo"><a href="/org/index"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>
		<a id="main-menu-button" href="#"><?php echo Asset::img('icon_humberger.png'.$sVQ,array('alt'=>'','width'=>'20','height'=>'20','class'=>'sp-display')); ?><?php echo Asset::img('icon_arrow_l.png'.$sVQ,array('alt'=>'','width'=>'8','height'=>'16','class'=>'pc-display')); ?></a>
		<a id="system-menu-button" href="#" style="text-align: center;">
			<?php echo Asset::img('icon_setting.png',array('alt'=>'', 'style'=>'width: 24px; margin-top: 3px;')); ?>
		</a>
	</header>

	<nav id="main-menu">
		<div class="menu-inner">
			<h2 class="menu-subtitle"><?php echo __('管理'); ?></h2>
			<ul>
				<li><a href="/org/teacher"><span><?php echo __('先生一覧'); ?></span><span class="attention attn-suc"><?php echo $aGroup['gtTNum']; ?></span></a></li>
				<li><a href="/org/class"><span><?php echo __('講義一覧'); ?></span><span class="attention attn-suc"><?php echo $aGroup['gtCNum']; ?></span></a></li>
				<li><a href="/org/student"><span><?php echo __('学生一覧'); ?></span><span class="attention attn-suc"><?php echo $aGroup['gtSNum']; ?></span></a></li>
				<li><a href="/org/study"><span><?php echo __('学生履修一覧'); ?></span></a></li>
				<li><a href="/org/flag"><span><?php echo __('先生と学生の利用設定'); ?></span></a></li>
				<li><a href="/org/annual"><span><?php echo __('年次更新'); ?></span></a></li>
			</ul>
		</div>
	</nav>

	<nav id="system-menu">
		<div class="menu-inner">
			<ul>
			<li class="system-menu-account">
				<?php echo View::forge('selectlang_menu'); ?>
			</li>
			<li><a href="/org/profile"><span><?php echo __('アカウント設定'); ?></span></a></li>
			<?php if ($aGroup['gtID'] != '' && !CL_CAREERTASU_MODE): ?>
				<?php $sLD = ($aGroup['gtLDAP'])? '_ld':''; ?>
				<li><a href="<?php echo Asset::get_file('cl_admin'.$sLD.'_manual.pdf', 'docs'); ?>" target="_blank"><span><i class="fa fa-download"></i> <?php echo __('マニュアル'); ?></span></a>
			<?php endif; ?>
			<li><a href="/org/index/logout"><span><?php echo __('ログアウト'); ?></span></a></li>
			</ul>
		</div>
	</nav>

	<div id="content" class="content-padding">

<?php if (strpos(\Uri::current(), '/org/index') !== false && strtotime(CL_TROUBLE) >= time()): ?>
<div class="trouble-news"><i class="fa fa-exclamation-triangle mr4"></i><?php echo CL_TROUBLE_NEWS; ?></div>
<?php endif; ?>

		<div id="content-inner">
		<div id="ajax_alerts" style="display: none;"><span></span><button type="button" class="close ajax_close">&times;</button></div>

		<?php if (isset($breadcrumbs)): ?>

		<ul id="breadcrumbs">
			<li><a href="/org/index">Top</a></li>
			<?php foreach ($breadcrumbs as $bc): ?>
				<?php if (isset($bc['link'])): ?>
					<li><a href="/org<?php echo $bc['link']; ?>"><?php echo $bc['name']; ?></a></li>
				<?php else: ?>
					<li><?php echo $bc['name']; ?></li>
				<?php endif;?>
			<?php endforeach; ?>
		</ul>

		<?php endif; ?>

		<?php if (isset($pagetitle)): ?>
		<h1 style="line-height: 33px;">
			<?php if (isset($subtitle)): ?>
				<p class="font-size-70 mb8 line-height-1"><?php echo $subtitle; ?></p>
			<?php endif; ?>
			<?php echo $pagetitle; ?>
			<?php if (isset($aCustomMenu)): ?>
				<div class="dropdown line-height-1 va-top" style="display: inline-block;"><button class="custommenu-dropdown-toggle font-default" type="button"><div><i class="fa fa-cog width-auto" style="margin: 0;"></i></div></button></div>
			<?php endif; ?>
			<?php if (isset($aCustomBtn)): ?>
				<?php
				foreach ($aCustomBtn as $aB):
					$sIcon = (isset($aB['icon']))? '<i class="fa '.$aB['icon'].'"></i>':'';
					$sOption = '';
					if (isset($aB['option'])):
						foreach ($aB['option'] as $sK => $sV):
							$sOption .= ' '.$sK.'="'.$sV.'"';
						endforeach;
					endif;
				?>
					<a class="font-size-60 button na do width-auto line-height-1 va-top" style="padding: 8px 16px;" href="<?php echo $aB['url']; ?>"<?php echo $sOption; ?>><?php echo $sIcon.$aB['name']; ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (isset($aCheckDrop)): ?>
				<div class="dropdown inline-block font-size-60 line-height-1 va-top">
					<button type="button" class="checkdrop-dropdown-toggle" style="padding-top: 8px; padding-bottom: 8px;" id="<?php echo (isset($aCheckDrop['option']))? $aCheckDrop['option']:''; ?>_checkdrop"><div><?php echo $aCheckDrop['name']; ?></div></button>
				</div>
				<ul class="dropdown-list dropdown-list-checkdrop font-size-60 line-height-1" obj="">
					<?php foreach ($aCheckDrop['list'] as $aL): ?>
					<li><a href="<?php echo $aL['url']; ?>" class="<?php echo $aL['class']; ?> text-left" style="padding-top: 10px; padding-bottom: 10px;"><span class="font-default"><?php echo $aL['name']; ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</h1>
		<?php endif; ?>

		<?php if (isset($ses['SES_ORG_ERROR_MSG'])): ?>
		<p class="error-box mb16"><?php echo nl2br($ses['SES_ORG_ERROR_MSG']); ?></p>
		<?php Session::delete('SES_ORG_ERROR_MSG'); ?>
		<?php endif; ?>
		<?php if (isset($ses['SES_ORG_NOTICE_MSG'])): ?>
		<div class="info-box tmp mb16">
			<p><?php echo nl2br($ses['SES_ORG_NOTICE_MSG']); ?></p>
			<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png'.$sVQ,array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
		</div>
		<?php Session::delete('SES_ORG_NOTICE_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($aSearchForm)): ?>
		<form action="<?php echo $aSearchForm['url']; ?>" method="get" class="SearchForm" style="top: 36px;">
			<label for="SearchInput"><i class="fa fa-search"></i></label><input type="text" name="w" value="<?php echo $sWords; ?>" id="SearchInput" class="allow_submit"><button type="submit" class="SearchButton" name="sub_state" value="1"><?php echo __('検索'); ?></button>
		</form>
		<?php endif; ?>

		<?php echo $content; ?>
		</div>
	</div>

	<?php echo $footer; ?>
	<?php if (isset($subwindow)) { echo $subwindow; } ?>

	<?php if (isset($aCustomMenu)): ?>
<ul class="dropdown-list dropdown-list-custommenu" obj="">
	<?php
		foreach ($aCustomMenu as $aCM):
			$sIcon = (isset($aCM['icon']))? '<i class="fa '.$aCM['icon'].' mr0"></i> ':'';
			$sOption = '';
			$sCls = '';
			if (isset($aCM['option'])):
				foreach ($aCM['option'] as $sK => $sV):
					if ($sK == 'class'):
						$sCls .= ' '.$sV;
						continue;
					endif;
					$sOption .= ' '.$sK.'="'.$sV.'"';
				endforeach;
			endif;
	?>
	<li><a href="<?php echo $aCM['url']; ?>" class="text-left<?php echo $sCls; ?>"<?php echo $sOption; ?>><span class="font-default"><?php echo $sIcon.$aCM['name']; ?></span></a></li>
	<?php endforeach; ?>
</ul>
	<?php endif; ?>

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

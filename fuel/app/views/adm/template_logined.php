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
		<span id="site-logo"><a href="/adm/index"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>
		<a id="main-menu-button" href="#"><?php echo Asset::img('icon_humberger.png'.$sVQ,array('alt'=>'','width'=>'20','height'=>'20','class'=>'sp-display')); ?><?php echo Asset::img('icon_arrow_l.png'.$sVQ,array('alt'=>'','width'=>'8','height'=>'16','class'=>'pc-display')); ?></a>
		<a id="system-menu-button" href="#" style="text-align: center;">
			<?php echo Asset::img('icon_setting.png'.$sVQ,array('alt'=>'', 'style'=>'width: 24px; margin-top: 3px;')); ?>
		</a>
	</header>

	<nav id="main-menu">
		<div class="menu-inner">
			<h2 class="menu-subtitle">販売管理</h2>
			<ul>
			<li><a href="/adm/order"><span>銀行振込確認</span></a></li>
			<li><a href="/adm/sale"><span>販売履歴</span></a></li>
			</ul>

			<hr>

			<h2 class="menu-subtitle">先生管理</h2>
			<ul>
			<li><a href="/adm/group"><span>団体一覧</span></a></li>
			<li><a href="/adm/teacher"><span>先生一覧</span></a></li>
			</ul>

			<hr>

			<h2 class="menu-subtitle">その他</h2>
			<ul>
			<li><a href="/adm/coupon"><span>クーポン管理</span></a></li>
			<li><a href="/adm/kreport"><span>ケータイ研レポート</span></a></li>
			</ul>
		</div>
	</nav>

	<nav id="system-menu">
		<div class="menu-inner">
			<ul>
			<li><a href="/adm/profile"><span>アカウント設定</span></a></li>
			<li><a href="/adm/index/logout"><span>ログアウト</span></a></li>
			</ul>
		</div>
	</nav>

	<div id="content" class="content-padding">
		<div id="content-inner">
		<div id="ajax_alerts" style="display: none;"><span></span><button type="button" class="close ajax_close">&times;</button></div>

		<?php if (isset($breadcrumbs)): ?>

		<ul id="breadcrumbs">
			<li><a href="/adm/index">Top</a></li>
			<?php foreach ($breadcrumbs as $bc): ?>
				<?php if (isset($bc['link'])): ?>
					<li><a href="/adm<?php echo $bc['link']; ?>"><?php echo $bc['name']; ?></a></li>
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
					$sCBCls = '';
					if (isset($aB['option'])):
						foreach ($aB['option'] as $sK => $sV):
							$sOption .= ' '.$sK.'="'.$sV.'"';
						endforeach;
					endif;
					if (isset($aB['class'])):
						foreach ($aB['class'] as $sV):
							$sCBCls .= ' '.$sV;
						endforeach;
					endif;
				?>
					<a class="font-size-60 button na do width-auto line-height-1 va-top<?php echo $sCBCls; ?>" style="padding: 8px 16px;" href="<?php echo $aB['url']; ?>"<?php echo $sOption; ?>><?php echo $sIcon.$aB['name']; ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</h1>
		<?php endif; ?>

		<?php if (isset($ses['SES_ADM_ERROR_MSG'])): ?>
		<p class="error-box mb16"><?php echo nl2br($ses['SES_ADM_ERROR_MSG']); ?></p>
		<?php Session::delete('SES_ADM_ERROR_MSG'); ?>
		<?php endif; ?>
		<?php if (isset($ses['SES_ADM_NOTICE_MSG'])): ?>
		<div class="info-box tmp mb16">
			<p><?php echo nl2br($ses['SES_ADM_NOTICE_MSG']); ?></p>
			<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png'.$sVQ,array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
		</div>
		<?php Session::delete('SES_ADM_NOTICE_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($aSearchForm)): ?>
		<form action="<?php echo $aSearchForm['url']; ?>" method="get" class="SearchForm" style="top: 36px;">
			<label for="SearchInput"><i class="fa fa-search"></i></label><input type="text" name="w" value="<?php echo $sWords; ?>" id="SearchInput"><button type="submit" class="SearchButton" name="sub_state" value="1"><?php echo __('検索'); ?></button>
		</form>
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

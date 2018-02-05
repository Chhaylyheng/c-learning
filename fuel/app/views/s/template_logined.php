<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo View::forge('template_header'); ?>

	<?php echo Asset::js('jquery-ui-datepicker-ja.js'.$sVQ); ?>
	<?php echo Asset::js('jquery.metadata.js'.$sVQ); ?>
	<?php echo Asset::js('jquery.tablesorter.min.js'.$sVQ); ?>

	<?php echo Asset::css('jquery.timepicker.css'.$sVQ); ?>

	<?php if (isset($css) && is_array($css)): ?>
		<?php foreach ($css as $cssfile): ?>
			<?php echo Asset::css($cssfile.$sVQ); ?>
		<?php endforeach; ?>
	<?php endif; ?>

</head>

<body>
<div id="CountUnread" style="display: none;"><?php echo $iUnread; ?></div>
<?php
$sL = 'en';
if ($sLang == 'ja' || $sLang == 'cp' || $sLang == 'ct'):
	$sL = 'ja';
endif;
?>
<div id="CurrentLang" style="display: none;"><?php echo $sL; ?></div>
<div>
	<header>
		<span id="site-logo"><a href="/s/index"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>
		<a id="main-menu-button" href="#"><?php echo Asset::img('icon_humberger.png'.$sVQ,array('alt'=>'','width'=>'20','height'=>'20','class'=>'sp-display')); ?><?php echo Asset::img('icon_arrow_l.png'.$sVQ,array('alt'=>'','width'=>'8','height'=>'16','class'=>'pc-display')); ?></a>
		<a id="system-menu-button" href="#" style="text-align: center;">
			<?php echo Asset::img('icon_setting.png',array('alt'=>'', 'style'=>'width: 24px; margin-top: 3px;')); ?>
		</a>
	</header>

	<nav id="main-menu">
		<div class="menu-inner">
		<h2 class="menu-subtitle"><?php echo __('講義一覧'); ?></h2>
			<ul>
			<?php
				if (!is_null($aClassList)):
					$iToday = strtotime(date('Y/m/d'));
					foreach ($aClassList as $aC):
						if (CL_CAREERTASU_MODE && !isset($aCTeach[$aC['ctID']])):
							continue;
						endif;
						if (CL_CAREERTASU_MODE && isset($aCTeach[$aC['ctID']]) && (strtotime($aCTeach[$aC['ctID']]['ttCTStart']) > $iToday || strtotime($aCTeach[$aC['ctID']]['ttCTEnd']) < $iToday)):
							continue;
						endif;
						$iUn = 0;
						if (isset($aUnread[$aC['ctID']])):
							$iUn = (int)$aUnread[$aC['ctID']];
						endif;
			?>
				<li><a href="/s/class/index/<?php echo $aC['ctID']; ?>"><span><?php echo $aC['ctName']; ?></span><?php echo (($iUn)? '<span class="attention  attn-emp">'.$iUn.'</span>':'')?></a></li>
			<?php
					endforeach;
				endif;
			?>
			</ul>
		</div>
	</nav>

	<nav id="system-menu">
		<div class="menu-inner">
			<ul>
				<li class="system-menu-account">
					<span class="system-menu-account-text-name font-size-90">
						<i class="fa fa-sign-in fa-fw"></i> <?php echo $aStudent['stName']; ?>
					</span><br>
					<span class="system-menu-account-text-time">
						<i class="fa fa-clock-o fa-fw"></i> <?php echo ($aStudent['stLastLoginDate'] != CL_DATETIME_DEFAULT)? Clfunc_Tz::tz('Y/m/d H:i',$tz,$aStudent['stLastLoginDate']):__('初ログイン'); ?>
					</span>
					<br><?php echo View::forge('selectlang_menu'); ?>
				</li>
				<li><a href="/s/profile"><span><?php echo __('アカウント設定'); ?></span></a></li>
				<li><a href="/s/index/logout"><span><?php echo __('ログアウト'); ?></span></a></li>
			</ul>
		</div>
	</nav>

	<div id="content" class="content-padding">

<?php if (isset($aClassNews) && !is_null($aClassNews)): ?>
<?php if ($aClass['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
<ul class="class-news">
<?php foreach ($aClassNews as $aN): ?>
<?php
$sIcon = null;
$sLink = null;
$sButton = null;
if ($aN['cnChain']):
	$aU = $aN['cnChain'];
	$sPut = ($aU['put'])? __('[済]'):'';
	$sIcon = ($sPut)?  $sPut:'<i class="fa fa-chain"></i>';
	if ($aU['public'] == 0):
		$sLink = '<span class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</span>';
		$sButton = '<div class="mt4 mb4"><button class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</button></div>';
	else:
		if ($aU['public'] == 1):
			$sLink = '<a href="'.$aU['url'].DS.'class" class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a>';
			$sButton = '<div class="mt4 mb4"><a href="'.$aU['url'].DS.'class" class="button na default width-auto font-default font-normal" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a></div>';
		else:
			if ($sPut):
				$sLink = '<a href="'.$aU['url'].DS.'class" class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a>';
				$sButton = '<div class="mt4 mb4"><a href="'.$aU['url'].DS.'class" class="button na default width-auto font-default font-normal" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a></div>';
			else:
				$sLink = '<span class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</span>';
				$sButton = '<div class="mt4 mb4"><button class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</button></div>';
			endif;
		endif;
	endif;
endif;
?>
<?php $sBHead = mb_strimwidth($aN['cnBody'],0,140,'…','UTF-8').' '.$sLink; ?>
<li class="marquee news-toggle"><div class="marquee-inner"><?php echo $sIcon.$sBHead; ?></div></li>
<li class="news-detail" style="display: none;"><?php echo \Clfunc_Common::url2link(nl2br($aN['cnBody']), 0).$sButton; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endif; ?>

		<div id="content-inner">

		<?php if (isset($breadcrumbs)): ?>

		<ul id="breadcrumbs">
			<li><a href="/s/index">Top</a></li>
			<?php foreach ($breadcrumbs as $bc): ?>
				<?php if (isset($bc['link'])): ?>
					<li><a href="/s<?php echo $bc['link']; ?>"><?php echo $bc['name']; ?></a></li>
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
				<p class="font-size-70 mb8 line-height-1"><a href="/s/class/index/<?php echo $aClass['ctID']; ?>"><i class="fa fa-book"></i> <?php echo $aClass['ctName'].$sPWH; ?></a></p>
			<?php endif; ?>
			<?php if (isset($classtitle)): ?>
				<?php echo $classtitle; ?>
			<?php else: ?>
				<?php echo $pagetitle; ?>
			<?php endif; ?>
			<?php if (isset($aCustomMenu)): ?>
				<div class="dropdown line-height-1 va-top" style="display: inline-block;" id="CustomMenuBox"><button class="custommenu-dropdown-toggle font-default" type="button"><div><i class="fa fa-cog width-auto" style="margin: 0;"></i></div></button></div>
			<?php endif; ?>
			<?php if (isset($aCustomBtn)): ?>
				<?php
				foreach ($aCustomBtn as $aB):
					if ($aClass['ctStatus'] && $aB['show'] == -1) continue;
					if (!$aClass['ctStatus'] && $aB['show'] == 1) continue;
					$sIcon = (isset($aB['icon']))? '<i class="fa '.$aB['icon'].' mr0"></i> ':'';
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
					<a class="font-size-60 button na do width-auto line-height-1 va-top<?php echo $sCBCls; ?>" style="padding: 8px 8px;" href="<?php echo $aB['url']; ?>"<?php echo $sOption; ?>><?php echo $sIcon.$aB['name']; ?></a>
				<?php endforeach; ?>
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

	<?php if (isset($aCustomMenu)): ?>
<ul class="dropdown-list dropdown-list-custommenu" obj="">
	<?php
		$iM = 0;
		foreach ($aCustomMenu as $aCM):
			if ($aClass['ctStatus'] && $aCM['show'] == -1) continue;
			if (!$aClass['ctStatus'] && $aCM['show'] == 1) continue;
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
			$iM++;
	?>
	<li><a href="<?php echo $aCM['url']; ?>" class="text-left<?php echo $sCls; ?>"<?php echo $sOption; ?>><span class="font-default"><?php echo $sIcon.$aCM['name']; ?></span></a></li>
	<?php endforeach; ?>
	<?php if ($iM == 0): ?>
<style>#CustomMenuBox { display: none!important; } </style>
	<?php endif; ?>
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

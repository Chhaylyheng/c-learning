<!DOCTYPE html>
<html lang="ja">
<head>
<?php \Clfunc_Tracking::Tag1(); ?>

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
<?php \Clfunc_Tracking::Tag2(); ?>
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
		<span id="site-logo"><a href="/t/index"><?php echo Asset::img($sLogo.'.png'.$sVQ,array('alt'=>CL_SITENAME,'width'=>'144','height'=>'32')); ?></a></span>

		<a id="main-menu-button" href="#"><?php echo Asset::img('icon_humberger.png'.$sVQ,array('alt'=>'','width'=>'20','height'=>'20','class'=>'sp-display')); ?><?php echo Asset::img('icon_arrow_l.png'.$sVQ,array('alt'=>'','width'=>'8','height'=>'16','class'=>'pc-display')); ?></a>

		<?php $sSysMenuBtn = 'margin-right: 8px;'; ?>

<?php if (!$aAssistant): ?>
		<a id="system-menu-button"  href="#" style="<?php echo $sSysMenuBtn; ?>">
			<div class="sys-menu-image"><?php echo ($aTeacher['ttImage'])? '<img src="/upload/profile/t/'.$aTeacher['ttImage'].'?'.mt_rand().'">':Asset::img('img_no_icon.png'.$sVQ); ?></div>
			<div class="sys-menu-name pc-display"><?php echo $aTeacher['ttName']; ?>
			<?php if ($aTeacher['gtID'] == ''): ?>
				<br>
				<?php if ($aTeacher['ptID'] > 0): ?>
					<?php echo $aTeacher['ptName']; ?>
					<?php echo date('- \'y/m/d', strtotime($aTeacher['coTermDate'])); ?>
				<?php else: ?>
					Quick - Free
				<?php endif; ?>
			<?php endif; ?>
			</div>
			<i class="fa fa-caret-down"></i>
		</a>
<?php else: ?>
		<a id="system-menu-button"  href="#">
			<div class="sys-menu-image"><?php echo Asset::img('icon_setting.png'.$sVQ, array('style'=>'width: 20px; height: 20px; margin: 6px;')); ?></div>
			<div class="sys-menu-name pc-display"><?php echo $aAssistant['atName']; ?></div>
			<i class="fa fa-caret-down"></i>
		</a>
<?php endif; ?>

		<?php if ($aTeacher['gtID'] == '' && !CL_CAREERTASU_MODE): ?>
			<a id="manual-button" class="button na default width-auto font-size-80 mt12 mr4" style="padding: 4px;" href="/t/index/manual"><?php echo __('マニュアル'); ?> <span class="font-size-80 font-red">NEW!</span></a>
		<?php endif; ?>

	</header>

	<nav id="main-menu">
		<div class="menu-inner">
		<h2 class="menu-subtitle"><?php echo __('実施中の講義'); ?></h2>
			<ul>
			<?php
				if (!is_null($aClassList)):
					foreach ($aClassList as $aC):
						$iUn = 0;
						if (isset($aUnread[$aC['ctID']])):
							$iUn = (int)$aUnread[$aC['ctID']];
						endif;
			?>
				<li><a href="/t/class/index/<?php echo $aC['ctID']; ?>"><span><?php echo $aC['ctName']; ?></span><?php echo (($iUn)? '<span class="attention  attn-emp">'.$iUn.'</span>':'') ?></a></li>
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

<?php if (!$aAssistant): ?>
				<li class="system-menu-account" style="padding-left: 8px; padding-right: 8px;">
					<div class="system-menu-account-text sp-display">
						<span class="system-menu-account-text-name">
							<i class="fa fa-sign-in fa-fw"></i>
							<?php echo $aTeacher['ttName']; ?>
						</span><br>
						<?php if ($aTeacher['gtID'] == ''): ?>
						<span class="system-menu-account-text-name">
							<i class="fa fa-id-card-o fa-fw"></i>
							<?php if ($aTeacher['ptID'] > 0): ?>
								<?php echo $aTeacher['ptName']; ?>
								<?php echo date('- Y/m/d', strtotime($aTeacher['coTermDate'])); ?>
							<?php else: ?>
								Quick - Free
							<?php endif; ?>
						</span>
						<?php endif; ?>
					</div>
					<div class="system-menu-account-text">
						<?php if (CL_CAREERTASU_MODE): ?>
						<div class="system-menu-account-text-name" style="margin-bottom: 4px;">
							<i class="fa fa-id-card-o fa-fw"></i>
							<?php echo $aCTPlan[$aTeacher['ttCTPlan']]; ?>
							(<?php echo date('Y/m/d', strtotime($aTeacher['ttCTEnd'])); ?>)<br>
						</div>
						<?php endif; ?>
						<span class="system-menu-account-text-time">
							<i class="fa fa-clock-o fa-fw"></i>
							<?php echo ($aTeacher['ttLastLoginDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aTeacher['ttLastLoginDate']):__('初ログイン'); ?>
						</span><br>
						<?php echo View::forge('selectlang_menu'); ?>
					</div>
				</li>
				<li><a href="/t/profile"><span><?php echo __('アカウント設定'); ?></span></a></li>
 				<?php if ($aTeacher['gtID'] == '' && !preg_match('/CL_AIR/i', Input::user_agent())): ?>
				<li><a href="/t/payment/product"><span><?php echo __('購入手続き'); ?></span></a></li>
				<li><a href="/t/payment"><span><?php echo __('見積・購入履歴'); ?></span></a></li>
				<?php endif; ?>

				<?php if ($aTeacher['gtID'] != '' && !CL_CAREERTASU_MODE): ?>
					<?php $sLD = ($aGroup['gtLDAP'])? '_ld':''; ?>
					<li><a href="<?php echo Asset::get_file('cl_teacher'.$sLD.'_manual.pdf', 'docs'); ?>" target="_blank"><span><i class="fa fa-download"></i> <?php echo __('マニュアル'); ?></span></a>
				<?php elseif ($aTeacher['gtID'] == '' && !CL_CAREERTASU_MODE): ?>
					<li><a href="/t/index/manual"><span><?php echo __('マニュアル'); ?></span></a>
				<?php endif; ?>

				<li><a href="mailto:air-support@c-learning.jp"><span><i class="fa fa-envelope-o"></i> <?php echo __('サポートセンター'); ?></span></a></li>
				<li><a href="/t/index/logout"><span><?php echo __('ログアウト'); ?></span></a></li>
<?php else: ?>
				<li class="system-menu-account">
					<div class="system-menu-account-text sp-display">
						<span class="system-menu-account-text-name">
							<i class="fa fa-sign-in fa-fw"></i>
							<?php echo $aAssistant['atName']; ?>
						</span>
					</div>
					<div class="system-menu-account-text">
						<span class="system-menu-account-text-time">
							<i class="fa fa-clock-o fa-fw"></i>
							<?php echo ($aAssistant['atLastLoginDate'] != CL_DATETIME_DEFAULT)? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aAssistant['atLastLoginDate']):__('初ログイン'); ?>
						</span><br>
						<?php echo View::forge('selectlang_menu'); ?>
					</div>
				</li>
				<li><a href="/a/profile"><span><?php echo __('アカウント設定'); ?></span></a></li>

				<?php if ($aTeacher['gtID'] != '' && !CL_CAREERTASU_MODE): ?>
					<?php $sLD = ($aGroup['gtLDAP'])? '_ld':''; ?>
					<li><a href="<?php echo Asset::get_file('cl_teacher'.$sLD.'_manual.pdf', 'docs'); ?>" target="_blank"><span><i class="fa fa-download"></i> <?php echo __('マニュアル'); ?></span></a>
				<?php endif; ?>

				<li><a href="/a/login/logout"><span><?php echo __('ログアウト'); ?></span></a></li>
<?php endif; ?>
			</ul>
		</div>
	</nav>

	<div id="content" class="content-padding">

<?php if (strpos(\Uri::current(), '/t/index') !== false && strtotime(CL_TROUBLE) >= time()): ?>
<div class="trouble-news"><i class="fa fa-exclamation-triangle mr4"></i><?php echo CL_TROUBLE_NEWS; ?></div>
<?php endif; ?>

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
	$sIcon = '<i class="fa fa-chain"></i>';
	$sTarget = ($aU['target'])? ' target="_blank"':'';

	$sLink = '<a href="'.$aU['url'].'" class="font-blue font-bold"'.$sTarget.'><i class="fa fa-chain"></i>'.$aU['title'].'</a>';
	$sButton = '<div class="mt4 mb4"><a href="'.$aU['url'].'" class="button na default width-auto font-default font-normal" style="padding: 8px;"'.$sTarget.'><i class="fa fa-chain"></i>'.$aU['title'].'</a></div>';

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
			<li><a href="/t/index">Top</a></li>
			<?php foreach ($breadcrumbs as $bc): ?>
				<?php if (isset($bc['link'])): ?>
					<li><a href="/t<?php echo $bc['link']; ?>"><?php echo $bc['name']; ?></a></li>
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
				<p class="font-size-70 mb8 line-height-1"><a href="/t/class/index/<?php echo $aClass['ctID']; ?>"><i class="fa fa-book"></i> <?php echo $aClass['ctName'].$sPWH.' <i class="fa fa-user"></i>'.__(':num名',array('num'=>$aClass['scNum'])); ?> [<?php echo \Clfunc_Common::getCode($aClass['ctCode']); ?>]</a></p>
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

		<?php if (isset($ses['SES_T_ERROR_MSG'])): ?>
		<p class="error-box mb16"><?php echo nl2br($ses['SES_T_ERROR_MSG']); ?></p>
		<?php Session::delete('SES_T_ERROR_MSG'); ?>
		<?php endif; ?>
		<?php if (isset($ses['SES_T_NOTICE_MSG'])): ?>
		<div class="info-box tmp mb16">
			<p><?php echo nl2br($ses['SES_T_NOTICE_MSG']); ?></p>
			<a href="#" class="close-button"><?php echo Asset::img('icon_close_tmp.png'.$sVQ,array('width'=>'9','height'=>'9','alt'=>'')); ?></a>
		</div>
		<?php Session::delete('SES_T_NOTICE_MSG'); ?>
		<?php endif; ?>

		<?php if (isset($aSearchForm)): ?>
		<form action="<?php echo $aSearchForm['url']; ?>" method="get" class="SearchForm">
			<label for="SearchInput"><i class="fa fa-search"></i></label><input type="text" name="w" value="<?php echo $sWords; ?>" id="SearchInput" class="allow_submit"><button type="submit" class="SearchButton" name="sub_state" value="1"><?php echo __('検索'); ?></button>
		</form>
		<?php endif; ?>

		<?php echo $content; ?>
		</div>
	</div>

	<?php echo $footer; ?>

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

<?php if (isset($javascript) && is_array($javascript)): ?>
	<?php foreach ($javascript as $jsfile): ?>
		<?php echo Asset::js($jsfile.$sVQ); ?>
	<?php endforeach; ?>
<?php endif; ?>
</body>
</html>

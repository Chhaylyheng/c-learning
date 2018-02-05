<h1>
	<i class="fa fa-sign-in fa-fw"></i> <?php echo $sTitle; ?>
	<p class="font-size-60 mt8"><?php echo $sLogined; ?></p>
</h1>

<?php if (!$aStudent['stMailAuth']): ?>

<div class="mt16 info-box font-white" style="background-color: #880000; border: 3px solid #cc0000;">
<p class="font-size-120"><i class="fa fa-exclamation-circle"></i> <?php echo __('メールアドレスの認証が完了していません。'); ?></p>
<p class="mt4"><a href="/s/profile/mailauth" class="button na default width-auto"><?php echo __('メールアドレス認証メールを再送信する'); ?></a></p>
</div>

<?php endif; ?>

<div class="mt16">
	<h2><a href="#" class="link-out accordion acc-open"><?php echo __('履修中の講義'); ?></a></h2>
	<div class="accordion-content acc-content-open">
	<div class="accordion-content-inner">
<?php
	if (!is_null($aActClass)):
		$sWD = null;
		$iToday = strtotime('Y/m/d');
		foreach ($aActClass as $aC):
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

			if ($aWeekDay[$aC['ctWeekDay']] != $sWD && $aC['ctWeekDay'] > 0):
?>
				<h3 class="mt12"><?php echo ($sWD = $aWeekDay[$aC['ctWeekDay']]); ?></h3>
			<?php endif; ?>
			<p class="mt4"><a href="/s/class/index/<?php echo $aC['ctID']; ?>" class="link-out"><?php echo ($aC['dhNO'])? $aHour[$aC['dhNO']]:'　 '; ?>　<?php echo $aC['ctName'].(($iUn)? '<span class="attention  attn-emp">'.$iUn.'</span>':''); ?></a></p>

			<?php if ($aC['ctFunctionFlag'] & \Clfunc_Flag::C_FUNC_NEWS): ?>
			<?php if (isset($aNewsList[$aC['ctID']])): ?>
				<?php foreach ($aNewsList[$aC['ctID']] as $aN): ?>
				<?php
				$sIcon = null;
				$sLink = null;
				$sButton = null;
				if ($aN['cnChain']):
					$aU = $aN['cnChain'];

					$aAnc = explode('#', $aU['url']);
					$sURL = $aAnc[0];
					$sAnc = (isset($aAnc[1]))? '#'.$aAnc[1]:null;

					$sPut = ($aU['put'])? __('[済]'):'';
					$sIcon = ($sPut)?  $sPut:'<i class="fa fa-chain"></i>';
					if ($aU['public'] == 0):
						$sLink = '<span class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</span>';
						$sButton = '<div class="mt4 mb4"><button class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</button></div>';
					else:
						if ($aU['public'] == 1):
							$sLink = '<a href="'.$sURL.DS.'top'.DS.'?ct='.$aC['ctID'].$sAnc.'" class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a>';
							$sButton = '<div class="mt4 mb4"><a href="'.$sURL.DS.'top'.DS.'?ct='.$aC['ctID'].$sAnc.'" class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a></div>';
						else:
							if ($sPut):
								$sLink = '<a href="'.$sURL.DS.'top'.DS.'?ct='.$aC['ctID'].$sAnc.'" class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a>';
								$sButton = '<div class="mt4 mb4"><a href="'.$sURL.DS.'top'.DS.'?ct='.$aC['ctID'].$sAnc.'" class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</a></div>';
							else:
								$sLink = '<span class="font-blue font-bold"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</span>';
								$sButton = '<div class="mt4 mb4"><button class="button na default width-auto" style="padding: 8px;"><i class="fa fa-chain"></i>'.$aU['title'].$sPut.'</button></div>';
							endif;
						endif;
					endif;
				endif;
				?>
				<?php $sBHead = mb_strimwidth($aN['cnBody'],0,140,'…','UTF-8').' '.$sLink; ?>
				<div class="marquee news-toggle"><div class="marquee-inner"><?php echo $sIcon.$sBHead; ?></div></div>
				<div class="news-detail" style="display: none;"><?php echo \Clfunc_Common::url2link(nl2br($aN['cnBody']), 0).$sButton; ?></div>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php endif; ?>

		<?php endforeach; ?>
		<?php endif; ?>

		<?php if (!CL_CAREERTASU_MODE && !is_null($aActClass)): ?>
			<div class="button-box"><a href="/s/attend/history/1" class="button na default"><?php echo __('出席履歴'); ?></a></div>
		<?php endif; ?>

	</div>
	</div>
</div>


<?php if (is_null($aGroup) || !($aGroup['gtStudentAuthFlag'] & \Clfunc_Flag::S_AUTH_STADY)): ?>
<div class="mt16">
	<div class="info-box">
		<h2><?php echo __('履修登録'); ?></h2>
		<hr>
		<?php echo Form::open(array('action'=>'/s/class/entry','method'=>'post','class'=>'')) ; ?>
			<p><?php echo Form::input('c_code','',array('maxlength'=>'20','placeholder'=>__('履修する講義の講義コード'))); ?></p>
			<p class="button-box"><?php echo Form::button('c_submit',__('講義の確認'),array('class'=>'button na do')); ?></p>
		<?php Form::close(); ?>
	</div>
</div>
<?php endif; ?>

<?php if (CL_ENV == 'DEVELOPMENT'): ?>
<p><?php echo $aStudent['stDeviceToken']; ?></p>
<?php endif; ?>

<?php
	if (!is_null($aTest)):
		foreach ($aTest as $aQ):
			$sLink = null;
			$aPub = array(__('締切'),'red');
			if ($aQ['tbPublic'] == 1):
				$sLink = 'ans';
				$aPub = array(__('公開中'),'blue');
				if ($aQ['tbAutoCloseDate'] != CL_DATETIME_DEFAULT):
					$aPub[2] = '～ '.date('n/j H:i',strtotime($aQ['tbAutoCloseDate']));
				endif;
			endif;
			$sPut = __('未回答');
			$sScore = __('得点').'：─';
			$sTime = __('解答時間').'：─';
			if (isset($aQ['TPut'])):
				$sLink = 'result';
				$sPut = '<span style="color: #CC0000;">['.__('済').']'.date('m/d H:i',strtotime($aQ['TPut']['tpDate'])).'</span>';
				$sScore = __('得点').'：'.(($aQ['tbScorePublic'])? $aQ['TPut']['tpScore'].'/'.__(':num点',array('num'=>$aQ['tbTotal'])):'─');
				$sTime  = __('解答時間').'：'.Clfunc_Common::Sec2Min($aQ['TPut']['tpTime']);
			endif;
?>
<div>
<?php if (!is_null($sLink)): ?>
	<?php echo Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/test/<?php echo $sLink.DS.$aQ['tbID'].Clfunc_Mobile::SesID(); ?>"><?php echo $aQ['tbTitle']; ?></a><br>
<?php else: ?>
	<?php echo Clfunc_Mobile::emj('MEMO').$aQ['tbTitle']; ?><br>
<?php endif; ?>
	┣<span style="color: <?php echo $aPub[1]; ?>;"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?> <?php echo Clfunc_Mobile::emj('CLOCK').$aPub[2]; ?><?php endif; ?>
	<br>
	┣<?php echo $sPut; ?><br>
	┣<?php echo $sScore; ?><br>
	┗<?php echo $sTime; ?><br>
</div><br>
<?php
		endforeach;
	endif;
?>

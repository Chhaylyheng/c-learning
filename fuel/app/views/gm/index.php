<?php
	if (!is_null($aQuest)):
		foreach ($aQuest as $aQ):
			$sLink = null;
			$sQuick = ($aQ['qbQuickMode'])? '[Q]':'';
			$aPub = array(__('締切'),'red');
			if ($aQ['qbPublic'] == 1):
				$sLink = 'ans';
				$aPub = array('公開中','blue');
				if ($aQ['qbAutoCloseDate'] != CL_DATETIME_DEFAULT):
					$aPub[2] = '～ '.date('n/j H:i',strtotime($aQ['qbAutoCloseDate']));
				endif;
			endif;
			$sPut = __('未回答');
			if (isset($aQ['QPut'])):
				$sLink = 'result';
				$sPut = '<span style="color: #CC0000;">'.__('[済]').date('m/d H:i',strtotime($aQ['QPut']['qpDate'])).'</span>';
				if ($aQ['qbComPublic'] > 0):
					if ($aQ['QPut']['qpComment']):
						$sPut .= '<a href="/g/quest/result/'.$aQ['qbID'].Clfunc_Mobile::SesID().'">'.Clfunc_Mobile::emj('SMILE').'</a>';
					endif;
				endif;
			endif;
?>
<div>
<?php if (!is_null($sLink)): ?>
	<?php echo Clfunc_Mobile::emj('PENCIL'); ?><a href="/g/quest/<?php echo $sLink.DS.$aQ['qbID'].Clfunc_Mobile::SesID(); ?>"><?php echo $sQuick.$aQ['qbTitle']; ?></a><br>
<?php else: ?>
	<?php echo Clfunc_Mobile::emj('MEMO').$sQuick.$aQ['qbTitle']; ?><br>
<?php endif; ?>
	┣<span style="color: <?php echo $aPub[1]; ?>;"><?php echo $aPub[0]; ?></span>
	<?php if (isset($aPub[2])): ?> <?php echo Clfunc_Mobile::emj('CLOCK').$aPub[2]; ?><?php endif; ?>
	<br>
<?php
	$sAns = '';
	$sBent = '';
	$sSep = '┗';
	if ($aQ['qbBentPublic']):
		$sBent = $sSep.'<a href="/g/quest/bent/'.$aQ['qbID'].Clfunc_Mobile::SesID().'">'.__('集計結果').'</a><br>';
		$sSep = '┣';
	endif;
?>
	<?php echo $sSep.$sPut; ?><br>
	<?php echo $sBent; ?>
	<?php echo $sAns; ?>
</div><br>
<?php
		endforeach;
	endif;
?>

<?php echo Clfunc_Mobile::hr(); ?>

<div><a href="/g/index/logout<?php echo Clfunc_Mobile::SesID(); ?>"><?php echo __('ログアウト'); ?></a></div>


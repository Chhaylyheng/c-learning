
<div><?php echo __('提出'); ?>：<span style="color: #CC0000;"><?php echo ($aQuest['qpNum'] + $aQuest['qpGNum'] + $aQuest['qpTNum']); ?></span></div>

<?php echo Clfunc_Mobile::hr(); ?>

<?php if ($aQuest['qbComment']): ?>
	<div style="background-color: #0000CC; color: #FFFFFF;"><?php echo __('先生からのコメント'); ?></div>
	<p><?php echo nl2br($aQuest['qbComment']); ?></p>
<?php endif; ?>


<?php if (!is_null($aQuery)): ?>
<?php foreach ($aQuery as $sK => $aQ): ?>
<?php $qNO = (int)$aQ['qqSort']; ?>
<div style="background-color: #0000CC; color: #FFFFFF; padding: 2px 0;"><?php echo __('設問.:no',array('no'=>$qNO)); ?><?php echo ($aQ['qqStyle'] == 1)? ' ※'.__('複数回答'):''; ?></div>
<div><?php echo nl2br($aQ['qqText']); ?></div>
<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage'])): ?>
<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqImage']; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
<?php endif; ?>
<div style="background-color: #CC0000; color: #ffffff; margin-top: 5px; padding: 2px 0;"><?php echo __('回答'); ?></div>

<?php
	if ($aQ['qqStyle'] < 2):
		$aQB = $aBent['ALL'][$sK];
		if ($aQuest['qbQuerySort'] == 1):
			krsort($aQB);
		endif;
		foreach ($aQB as $i => $aB):
			if ($i == 0):
				continue;
			endif;
			if ($aQ['qqStyle'] == 0):
				$sAvg = ($aB['qbTotal'])? round((($aB['qbNum']/$aB['qbTotal'])*100),1):0;
			else:
				$sAvg = ($aB['qbAll'])? round((($aB['qbNum']/$aB['qbAll'])*100),1):0;
			endif;
			$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
?>
<div style="margin-top: 5px;">[<?php echo $i; ?>]<?php echo nl2br($aQ['qqChoice'.$i]); ?></div>
<?php if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i])): ?>
<div><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.CL_Q_SMALL_PREFIX.$aQ['qqChoiceImg'.$i]; ?>" style="max-width: 100%; width: auto; height: auto;"></div>
<?php endif; ?>
<div style="text-align: right;"><?php echo $sAvg; ?>% / <?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></div>
<?php
		endforeach;
		if ($aQB[0]['qbNum'] > 0):
			$i = 0;
			$aB = $aQB[$i];
			if ($aQ['qqStyle'] == 0):
				$sAvg = ($aB['qbTotal'])? round((($aB['qbNum']/$aB['qbTotal'])*100),1):0;
			else:
				$sAvg = ($aB['qbAll'])? round((($aB['qbNum']/$aB['qbAll'])*100),1):0;
			endif;
			$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
?>
<div style="margin-top: 5px;"><?php echo __('（無回答）'); ?></div>
<div style="text-align: right;"><?php echo $sAvg; ?>% / <?php echo __(':num名',array('num'=>$aB['qbNum'])); ?></div>
<?php
		endif;
	else:
		if (isset($aBent['ALL'][$sK])):
			$aQB = $aBent['ALL'][$sK];
			$iBlank = 0;
			foreach ($aQB as $i => $aB):
				if ($aB['qbText'] != ''):
					switch ($aB['qaPick']):
						case 1:
							$sIcon = '<span style="color: #CCCC33;">★</span>';
						break;
						case -1:
							$sIcon = '<span style="color: #CC0000;">×</span>';
						break;
						default:
							$sIcon = '<span style="color: #CCCC33;">☆</span>';
						break;
					endswitch;
?>
<div><?php echo $sIcon.nl2br($aB['qbText']); ?></div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php
				else:
					$iBlank++;
				endif;
			endforeach;
			if ($iBlank > 0):
				$sBlank = '<div>'.__('（無回答：:num名）',array('num'=>$iBlank)).'</div>';
				echo $sBlank;
			endif;
		endif;
	endif;
?>
<?php endforeach; ?>
<?php endif; ?>

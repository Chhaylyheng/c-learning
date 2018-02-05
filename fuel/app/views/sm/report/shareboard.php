<?php
$sRbID = $aReport['rbID'];
$sStID = $aStu['stID'];

$sRed = '#cc0000';
$sGreen = '#008800';
$sGray = '#666666';


?>
<?php if ($aReport['rbShare'] == 2): ?>
<div style="text-align: center; font-size: 90%;">
<a href="/s/report/shareboard/<?php echo $sRbID.DS.$sStID; ?>/s"><?php echo __('共有板').Clfunc_Mobile::emj('SMILE').$aPut['rpComNum']; ?></a>
 | <a href="/s/report/shareboard/<?php echo $sRbID.DS.$sStID; ?>/r"><?php echo __('評価分布').'★'.$aPut['rpAvgScore']; ?></a>
</div>
<?php echo Clfunc_Mobile::hr(); ?>
<?php endif; ?>

<?php
$aFiles = null;
for ($i = 1; $i <= 3; $i++):
	if ($aPut['fID'.$i] != ''):
		$aFiles[$i]['name'] = $aPut['fName'.$i];
		$aFiles[$i]['path'] = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aPut['fID'.$i],'mode'=>'e'));
		$aFiles[$i]['size'] = \Clfunc_Common::FilesizeFormat($aPut['fSize'.$i],1);
		$aFiles[$i]['tag'] = Clfunc_Mobile::emj('SMILE').'<a href="'.$aFiles[$i]['path'].'">'.$aFiles[$i]['name'].'（'.$aFiles[$i]['size'].'）</a><br>';
	endif;
endfor;
?>

<?php if ($sM == 's'): ?>

<div style="margin-top: 8px;">
<div style="background-color: #88AAFF; color: #333333; padding: 2px 0;"><?php echo __('提出内容') ?></div>
<div style="font-size: 80%; margin-bottom: 4px; margin-top: 2px;"><?php echo \Clfunc_Mobile::emj('SMILE'); ?><span style="color: #008800;"><?php echo $aStu['stName']; ?></span> <?php echo \Clfunc_Mobile::emj('CLOCK').(($aPut['rpDate'] != CL_DATETIME_DEFAULT)? date('Y/m/d H:i',strtotime($aPut['rpDate'])):'─'); ?></div>
<?php
	if (!is_null($aFiles)):
?>
<div style="margin-bottom: 4px; font-size: 80%;">
<?php
	foreach ($aFiles as $i => $aF):
		echo $aF['tag'];
	endforeach;
?>
</div>
<?php
	endif;
?>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo ($aPut['rpTeachPut'])? '['.__('先生による提出').']':($aPut['rpDate'] != CL_DATETIME_DEFAULT)? nl2br(\Clfunc_Common::url2link($aPut['rpText'],480)):'['.__('未提出').']'; ?></div>

<div style="font-size: 80%;">
<?php echo \Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/report/rescreate/<?php echo $sRbID.DS.$sStID; ?>">コメントする</a>
<?php
	if ($aReport['rbShare'] == 2 && $aStudent['stID'] != $sStID):
?>
<form action="/s/report/rate/<?php echo $sRbID.DS.$sStID.Clfunc_Mobile::SesID(); ?>" method="post" style="margin: 3px 0; text-align: center;">
<?php echo Clfunc_Mobile::SesID('post'); ?>

<select name="rate">
<option value="0"><?php echo __('未評価'); ?></option>
<?php
$aStar = array(
	1 => '★☆☆☆☆',
	2 => '★★☆☆☆',
	3 => '★★★☆☆',
	4 => '★★★★☆',
	5 => '★★★★★',
);

for ($i = 1; $i <= 5; $i++):
	$sSel = (!is_null($aRate) && $aRate['rrScore'] == $i)? ' selected':'';
?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $aStar[$i]; ?></option>
<?php endfor; ?>
</select>
<input type="submit" value="<?php echo __('評価する'); ?>" name="sub_state">
</form>
<?php
	endif;
?>
</div>

<?php
	if (!is_null($aParents)):
?>
<div style="margin-left: 4px;">
<?php
	foreach ($aParents as $sP => $aC):
		$bTeach = preg_match('/^[t|a]/', $aC['rcID']);
		$cName = ($bTeach)? (($aC['atName'])? $aC['atName']:$aC['ttName']):$aC['stName'];
		$cColor = ($bTeach)? $sRed:$sGreen;
		$sParam = $sRbID.DS.$sStID.DS.$aC['no'];
		$sDate = ($aC['rcDate'] != '0000-00-00 00:00:00')? date('\'y/m/d H:i',strtotime($aC['rcDate'])):'─';
		if ($aC['rcID'] == $aStudent['stID']):
			$aWriter = array($sGreen,$aStudent['stName']);
		else:
			switch ($aReport['rbAnonymous']):
				case 0:
					$aWriter = array($sGray, '匿名');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array($sGray, '匿名');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
?>
<div style="margin-top: 4px; border-top: 1px solid #6688FF; border-left: 1px solid #6688FF; padding: 2px 0 4px 2px;">
<div style="font-size: 80%; margin-bottom: 4px;"><?php echo \Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $aWriter[0]; ?>;"><?php echo $aWriter[1]; ?></span> <?php echo \Clfunc_Mobile::emj('CLOCK').$sDate; ?></div>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo nl2br(\Clfunc_Common::url2link($aC['rcComment'],0)); ?></div>
<div style="font-size: 80%;">
<?php echo \Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/report/rescreate/<?php echo $sParam.\Clfunc_Mobile::SesID(); ?>">返信する</a>
<?php
		if ($aC['rcID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('CLOVER'); ?><a href="/s/report/resedit/<?php echo $sParam.\Clfunc_Mobile::SesID(); ?>">編集</a>
<?php
		endif;
?>
<?php
		if ($aC['rcID'] == $aStudent['stID'] && !isset($aCnt['p'.$aC['no']])):
?>
<?php echo \Clfunc_Mobile::emj('WARN'); ?><a href="/s/report/resdelete/<?php echo $sParam.\Clfunc_Mobile::SesID(); ?>">削除</a>
<?php
		endif;
?>
</div>

<?php
	if (isset($aComments[$sP]['children'])):
?>

<div style="margin-left: 4px;">
<?php
	foreach ($aComments[$sP]['children'] as $aCG):
		$bTeach = preg_match('/^[t|a]/', $aCG['rcID']);
		$cName = ($bTeach)? (($aCG['atName'])? $aCG['atName']:$aCG['ttName']):$aCG['stName'];
		$cColor = ($bTeach)? $sRed:$sGreen;
		$sParam = $sRbID.DS.$sStID.DS.$aCG['no'];
		$sDate = ($aCG['rcDate'] != '0000-00-00 00:00:00')? date('\'y/m/d H:i',strtotime($aCG['rcDate'])):'─';
		if ($aCG['rcID'] == $aStudent['stID']):
			$aWriter = array($sGreen,$aStudent['stName']);
		else:
			switch ($aReport['rbAnonymous']):
				case 0:
					$aWriter = array($sGray, '匿名');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array($sGray, '匿名');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
?>
<div style="margin-top: 4px; border-top: 1px solid #6688FF; border-left: 1px solid #6688FF; padding: 2px 0 4px 2px;">
<div style="font-size: 80%; margin-bottom: 4px;"><?php echo \Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $aWriter[0]; ?>;"><?php echo $aWriter[1]; ?></span> <?php echo \Clfunc_Mobile::emj('CLOCK').$sDate; ?></div>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo nl2br(\Clfunc_Common::url2link($aCG['rcComment'],0)); ?></div>
<div style="font-size: 80%;">
<?php
		if ($aCG['rcID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('CLOVER'); ?><a href="/s/report/resedit/<?php echo $sParam.\Clfunc_Mobile::SesID(); ?>">編集</a>
<?php
		endif;
?>
<?php
		if ($aCG['rcID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('WARN'); ?><a href="/s/coop/resdelete/<?php echo $sParam.\Clfunc_Mobile::SesID(); ?>">削除</a>
<?php
		endif;
?>
</div>
</div>
<?php
	endforeach;
?>
</div>
<?php
endif;
?>
</div>
<?php
	endforeach;
?>
</div>
<?php
endif;
?>
</div>

<?php elseif ($sM == 'r'): ?>

<?php
	if ($aReport['rbShare'] == 2 && $aStudent['stID'] != $sStID):
?>
<form action="/s/report/rate/<?php echo $sRbID.DS.$sStID.Clfunc_Mobile::SesID(); ?>" method="post" style="text-align: center;">
<?php echo Clfunc_Mobile::SesID('post'); ?>

<select name="rate">
<option value="0"><?php echo __('未評価'); ?></option>
<?php
$aStar = array(
	1 => '★☆☆☆☆',
	2 => '★★☆☆☆',
	3 => '★★★☆☆',
	4 => '★★★★☆',
	5 => '★★★★★',
);

for ($i = 1; $i <= 5; $i++):
	$sSel = (!is_null($aRate) && $aRate['rrScore'] == $i)? ' selected':'';
?>
<option value="<?php echo $i; ?>"<?php echo $sSel; ?>><?php echo $aStar[$i]; ?></option>
<?php endfor; ?>
</select>
<input type="submit" value="<?php echo __('評価する'); ?>" name="sub_state">
</form>
<?php
	endif;
?>

<table style="border-collapse: collapse; border: 1px solid gray; margin: 5px auto;" cellpadding="4">
<thead>
<tr>
	<th style="border: 1px solid gray;"><?php echo __('評価点'); ?></th>
	<th style="border: 1px solid gray;"><?php echo __('割合'); ?></th>
	<th style="border: 1px solid gray;"><?php echo __('人数'); ?></th>
</tr>
</thead>
<tbody>
<?php
for ($i = 5; $i >= 1; $i--):
	$sAvg = ($aCount['rcNum'] > 0)?  round(($aCount['rc'.$i] / $aCount['rcNum']) * 100, 1):0;
	$sAvg = (strpos($sAvg,'.') > 0)? $sAvg:$sAvg.'.0';
?>
<tr>
<td style="border: 1px solid gray; text-align: center;"><?php echo $i; ?></td>
<td style="border: 1px solid gray; text-align: right;"><?php echo $sAvg; ?>%</td>
<td style="border: 1px solid gray; text-align: right;"><?php echo (int)$aCount['rc'.$i]; ?></td>
</tr>
<?php endfor; ?>
</tbody>
</table>

<?php endif; ?>













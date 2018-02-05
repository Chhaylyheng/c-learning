<?php
$sGreen = '#008833';
$sRed = '#cc0000';
$sGray = '#888888';
$sWhite = '#ffffff';
$sHead = '#3366CC';


$aP = $aContact;
$iP = $aContact['no'];
$sWriter = ($aP['coTeach'])? $sRed:$sGreen;
$sDate = date('\'y/m/d H:i',strtotime($aP['coDate']));
$iResNum = (isset($aThread['C']))? count($aThread['C']):0;
$sSubject = ($aP['coSubject'])? $aP['coSubject']:'(No subject)';

$sDelDisp = ($aP['coID'] == $aStudent['stID'])? 'inline':'none';

$aRead = array('icon'=>'MAIL', 'color'=>$sGray);
if ($aP['coID'] != $aStudent['stID'] && !$aP['coRead']):
	$aRead = array('icon'=>'NEW', 'color'=>$sRed);
endif;

$sSubRead = (isset($aNew[$iP]))? 'NEW':'MEMO';
?>

<div style="margin-top: 5px; margin-left: 1px; border-left: 3px solid <?php echo $aRead['color']; ?>; padding-left: 2px; font-size: 80%;">
<div style="color: white; background-color: <?php echo $sHead; ?>; padding-top: 2px; padding-bottom: 2px;">
<?php echo Clfunc_Mobile::emj($aRead['icon']); ?><?php echo $sSubject; ?>
</div>
<div>
<?php echo Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $sWriter; ?>;"><?php echo $aP['coName']; ?></span> <?php echo Clfunc_Mobile::emj('CLOCK').$sDate; ?>
</div>
<div style="margin-top: 3px; margin-bottom: 3px;">
<?php echo nl2br(\Clfunc_Common::url2link($aP['coBody']),480); ?>
</div>
<div style="text-align: right;">
<a href="/s/contact/rescreate/<?php echo $iP; ?>"><?php echo __('返信する'); ?></a>
<a href="/s/contact/resdelete/<?php echo $iP; ?>" style="display: <?php echo $sDelDisp; ?>"><?php echo __('削除'); ?></a>
</div>
</div>

<?php
if (!is_null($aChildren)):
foreach ($aChildren as $iC => $aC):
	$sWriter = ($aC['coTeach'])? $sRed:$sGreen;
	$sDate = date('\'y/m/d H:i',strtotime($aC['coDate']));
	$sDelDisp = ($aC['coID'] == $aStudent['stID'])? 'inline':'none';
	$sSubject = ($aC['coSubject'])? $aC['coSubject']:'(No subject)';

	$aRead = array('icon'=>'MAIL', 'color'=>$sGray);
	if ($aC['coID'] != $aStudent['stID'] && !$aC['coRead']):
		$aRead = array('icon'=>'NEW', 'color'=>$sRed);
	endif;
?>

<div style="margin-top: 5px; margin-left: 7px; border-left: 3px solid <?php echo $aRead['color']; ?>; padding-left: 2px; font-size: 80%;">
<div style="color: white; background-color: <?php echo $sHead; ?>; padding-top: 2px; padding-bottom: 2px;">
<?php echo Clfunc_Mobile::emj($aRead['icon']); ?><?php echo $sSubject; ?>
</div>
<div>
<?php echo Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $sWriter; ?>;"><?php echo $aC['coName']; ?></span> <?php echo Clfunc_Mobile::emj('CLOCK').$sDate; ?>
</div>
<div style="margin-top: 3px; margin-bottom: 3px;">
<?php echo nl2br(\Clfunc_Common::url2link($aC['coBody']),480); ?>
</div>
<div style="text-align: right;">
<a href="/s/contact/rescreate/<?php echo $iP; ?>"><?php echo __('返信する'); ?></a>
<a href="/s/contact/resdelete/<?php echo $iC; ?>" style="display: <?php echo $sDelDisp; ?>"><?php echo __('削除'); ?></a>
</div>
</div>

<?php
endforeach;
endif;
?>

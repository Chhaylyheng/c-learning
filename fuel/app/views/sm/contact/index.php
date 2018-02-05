<?php
$sGreen = '#008833';
$sRed = '#cc0000';
$sGray = '#888888';
$sWhite = '#ffffff';

if (!is_null($aContact)):
	foreach ($aContact as $iP => $aThread):
		$aP = $aThread['P'];
		$sWriter = ($aP['coTeach'])? $sRed:$sGreen;
		$sDate = date('\'y/m/d H:i',strtotime($aP['coDate']));
		$iResNum = (isset($aThread['C']))? count($aThread['C']):0;
		$sSubject = ($aP['coSubject'])? $aP['coSubject']:'(No subject)';

		$sRead = 'MAIL';
		if ($aP['coID'] != $aStudent['stID'] && !$aP['coRead']):
			$sRead = 'NEW';
		endif;

		$sSubRead = (isset($aNew[$iP]))? 'NEW':'MEMO';
?>
<div style="margin-top: 5px; font-size: 80%;">
	<?php echo Clfunc_Mobile::emj($sRead); ?><a href="/s/contact/thread/<?php echo $iP.Clfunc_Mobile::SesID(); ?>"><?php echo $sSubject; ?></a> <?php echo Clfunc_Mobile::emj($sSubRead); ?><?php echo $iResNum; ?><br>
	┗<?php echo Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $sWriter; ?>;"><?php echo $aP['coName']; ?></span> <?php echo Clfunc_Mobile::emj('CLOCK').$sDate; ?><br>
</div>
<?php
		endforeach;
	endif;
?>

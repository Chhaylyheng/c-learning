<?php
$bWrite = false;
if ($aCCategory['ccStuWrite']):
	$bWrite = true;
?>
<div style="text-align: center;"><?php echo \Clfunc_Mobile::emj('MEMO'); ?><a href="/s/coop/rescreate/<?php echo $aCCategory['ccID'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('スレッドを立てる'); ?></a></div>
<?php
endif;
$sGreen = '#008833';
$sRed = '#cc0000';
$sGray = '#888888';

if (!is_null($aParents)):
	$iMax = count($aParents);
	foreach ($aParents as $aP):
		$bTeach = preg_match('/^[t|a]/', $aP['cID']);
		$cName = ($aP['atName'])? $aP['atName']:(($aP['ttName'])? $aP['ttName']:(($aP['stName'])? $aP['stName']:$aP['cName']));
		$cColor = ($bTeach)? $sRed:$sGreen;
		$sNew = (!isset($aAlready[$aP['cNO']]))? 'NEW':'MEMO';
		$aFiles = null;

		for ($i = 1; $i <= 3; $i++):
			if ($aP['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aP['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1);
				$sThumb = null;
				if ($aP['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'t'));
				endif;
				$aFiles[$i]['id'] = $aP['fID'.$i];
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aP['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aP['cDate'] != '0000-00-00 00:00:00')? date('\'y/m/d H:i',strtotime($aP['cDate'])):'─';
		if ($aP['cID'] == $aStudent['stID']):
			$aWriter = array($sGreen,$aStudent['stName']);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array($sGray, __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array($sGray, __('匿名'));
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
?>
<div style="margin-top: 8px;">
<div style="background-color: #88AAFF; color: #333333; padding: 2px 0;"><?php echo \Clfunc_Mobile::emj($sNew).$aP['cTitle']; ?></div>
<div style="font-size: 80%; margin-bottom: 4px; margin-top: 2px;"><?php echo \Clfunc_Mobile::emj('SMILE'); ?><span style="color: <?php echo $aWriter[0]; ?>;"><?php echo $aWriter[1]; ?></span> <?php echo \Clfunc_Mobile::emj('CLOCK').$sDate; ?></div>
<?php
	if (!is_null($aFiles)):
?>
<div style="margin-bottom: 4px; font-size: 80%;">
<?php
		foreach ($aFiles as $i => $aF):
?>
<?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $aF['path']; ?>"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a><br>
<?php
		endforeach;
?>
</div>
<?php
	endif;
	if (isset($aP['cText'])):
?>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo nl2br(\Clfunc_Common::url2link($aP['cText'],0)); ?></div>
<?php
	endif;
	if ($bWrite):
?>
<div style="font-size: 80%;">
<?php echo \Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/coop/rescreate/<?php echo $aCCategory['ccID'].DS.$aP['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('コメントする'); ?></a>
<?php
		if ($aP['cID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('CLOVER'); ?><a href="/s/coop/resedit/<?php echo $aCCategory['ccID'].DS.$aP['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('編集'); ?></a>
<?php
		endif;
?>
<?php
		if ($aP['cID'] == $aStudent['stID'] && !isset($aCnt['r'.$aP['cNO']])):
?>
<?php echo \Clfunc_Mobile::emj('WARN'); ?><a href="/s/coop/resdelete/<?php echo $aCCategory['ccID'].DS.$aP['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('削除'); ?></a>
<?php
		endif;
?>
</div>
<?php
	endif;
?>

<?php
	if (isset($aCoops[$aP['cNO']])):
?>
<div style="margin-left: 4px;">
<?php
	foreach ($aCoops[$aP['cNO']] as $aC):
		$bTeach = preg_match('/^[t|a]/', $aC['cID']);
		$cName = ($aC['atName'])? $aC['atName']:(($aC['ttName'])? $aC['ttName']:(($aC['stName'])? $aC['stName']:$aC['cName']));
		$cColor = ($bTeach)? $sRed:$sGreen;
		$sNew = (!isset($aAlready[$aC['cNO']]))? 'NEW':'MEMO';
		$aFiles = null;

		for ($i = 1; $i <= 3; $i++):
			if ($aC['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aC['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aC['fSize'.$i],1);
				$sThumb = null;
				if ($aC['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aC['fID'.$i],'mode'=>'t'));
				endif;
				$aFiles[$i]['id'] = $aC['fID'.$i];
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aC['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aC['cDate'] != '0000-00-00 00:00:00')? date('\'y/m/d H:i',strtotime($aC['cDate'])):'─';
		if ($aC['cID'] == $aStudent['stID']):
			$aWriter = array($sGreen,$aStudent['stName']);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array($sGray, __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array($sGray, __('匿名'));
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
<?php
	if (!is_null($aFiles)):
?>
<div style="margin-bottom: 4px; font-size: 80%;">
<?php
		foreach ($aFiles as $i => $aF):
?>
<?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $aF['path']; ?>"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a><br>
<?php
		endforeach;
?>
</div>
<?php
	endif;
	if (isset($aC['cText'])):
?>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo nl2br(\Clfunc_Common::url2link($aC['cText'],0)); ?></div>
<?php
	endif;
	if ($bWrite):
?>
<div style="font-size: 80%;">
<?php echo \Clfunc_Mobile::emj('PENCIL'); ?><a href="/s/coop/rescreate/<?php echo $aCCategory['ccID'].DS.$aC['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('返信する'); ?></a>
<?php
		if ($aC['cID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('CLOVER'); ?><a href="/s/coop/resedit/<?php echo $aCCategory['ccID'].DS.$aC['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('編集'); ?></a>
<?php
		endif;
?>
<?php
		if ($aC['cID'] == $aStudent['stID'] && !isset($aCnt['p'.$aC['cNO']])):
?>
<?php echo \Clfunc_Mobile::emj('WARN'); ?><a href="/s/coop/resdelete/<?php echo $aCCategory['ccID'].DS.$aC['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('削除'); ?></a>
<?php
		endif;
?>
</div>
<?php
	endif;
?>

<?php
	if (isset($aCoops[$aP['cNO']][$aC['cNO']]['children'])):
?>

<div style="margin-left: 4px;">
<?php
	foreach ($aCoops[$aP['cNO']][$aC['cNO']]['children'] as $aG):
		$bTeach = preg_match('/^[t|a]/', $aG['cID']);
		$cName = ($aG['atName'])? $aG['atName']:(($aG['ttName'])? $aG['ttName']:(($aG['stName'])? $aG['stName']:$aG['cName']));
		$cColor = ($bTeach)? $sRed:$sGreen;
		$sNew = (!isset($aAlready[$aG['cNO']]))? 'NEW':'MEMO';
		$aFiles = null;

		for ($i = 1; $i <= 3; $i++):
			if ($aG['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aG['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aG['fSize'.$i],1);
				$sThumb = null;
				if ($aG['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aG['fID'.$i],'mode'=>'t'));
				endif;
				$aFiles[$i]['id'] = $aG['fID'.$i];
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aG['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aG['cDate'] != '0000-00-00 00:00:00')? date('\'y/m/d H:i',strtotime($aG['cDate'])):'─';
		if ($aG['cID'] == $aStudent['stID']):
			$aWriter = array($sGreen,$aStudent['stName']);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array($sGray, __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array($sGray, __('匿名'));
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
<?php
	if (!is_null($aFiles)):
?>
<div style="margin-bottom: 4px; font-size: 80%;">
<?php
		foreach ($aFiles as $i => $aF):
?>
<?php echo \Clfunc_Mobile::emj('CLIP'); ?><a href="<?php echo $aF['path']; ?>"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a><br>
<?php
		endforeach;
?>
</div>
<?php
	endif;
	if (isset($aG['cText'])):
?>
<div style="margin-left: 0.5em; margin-bottom: 4px;"><?php echo nl2br(\Clfunc_Common::url2link($aG['cText'],0)); ?></div>
<?php
	endif;
	if ($bWrite):
?>
<div style="font-size: 80%;">
<?php
		if ($aG['cID'] == $aStudent['stID']):
?>
<?php echo \Clfunc_Mobile::emj('CLOVER'); ?><a href="/s/coop/resedit/<?php echo $aCCategory['ccID'].DS.$aG['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('編集'); ?></a>
<?php
		endif;
?>
<?php
		if ($aG['cID'] == $aStudent['stID'] && !isset($aCnt['p'.$aG['cNO']])):
?>
<?php echo \Clfunc_Mobile::emj('WARN'); ?><a href="/s/coop/resdelete/<?php echo $aCCategory['ccID'].DS.$aG['cNO'].\Clfunc_Mobile::SesID(); ?>"><?php echo __('削除'); ?></a>
<?php
		endif;
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
endif;
?>

<?php
if (!is_null($aMaterial)):
	$aM = $aMaterial;
	$iNO = $aM['mNO'];

	$sNew = (!$aM['already'])? '<span class="attention attn-emp">'.__('未読').'</span>':'';
	$sPath = '';
	$sSize = '';
	if ($aM['fID'] != ''):
		if (!$aM['already']):
			$sPath = \Uri::create('getfile/s3file/:fid/:mode/:mno/:sid',array('fid'=>$aM['fID'],'mode'=>'me','mno'=>$iNO,'sid'=>$aStudent['stID']));
		else:
			$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'e'));
		endif;
		$sSize = \Clfunc_Common::FilesizeFormat($aM['fSize'],1);
		$sIcon = 'paperclip';
		$sThumb = null;
		if ($aM['fFileType'] == 2):
			$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aM['fID'],'mode'=>'t'));
			$sIcon = 'film';
		endif;
		$sFile = '<p><i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.$aM['fName'].'（'.$sSize.'）</a></p>';
	endif;
	$aYoutube = null;
	$aEURL = null;

	if (is_array($aM['mURL'])):
		foreach ($aM['mURL'] as $i => $v):
			if (!$v) continue;
			$aYoutube[$i] = \Clfunc_Common::createYoutubeTag($v);
			if (!$aM['already'] && $aM['fID'] == ''):
				$aEURL[$i] = \Uri::create('getfile/externallink/:mno/:eno/:sid',array('mno'=>$iNO, 'eno'=>$i,'sid'=>$aStudent['stID']));
			else:
				$aEURL[$i] = \Uri::create('getfile/externallink/:mno/:eno',array('mno'=>$iNO, 'eno'=>$i));
			endif;
		endforeach;
	endif;
?>
<h2><?php echo $sNew.' '.$aM['mTitle']; ?><br><span class="mat-date"><?php echo Clfunc_Tz::tz('Y/n/d H:i',$tz,$aM['mDate']); ?></span></h2>
<?php if ($sPath): ?>
<div class="mat-files mt16" style="max-width: 640px;">
<?php
		switch ($aM['fFileType']):
			case 2:	# 映像の場合
?>
<video class="width-100"  controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video>
<?php
			break;
			case 1:
?>
<img class="width-100" src="<?php echo $sPath; ?>" alt="<?php echo $aM['fName'].'（'.$sSize.'）'; ?>">
<?php
			break;
			default:
				echo $sFile;
			break;
		endswitch;
?>
</div>
<?php endif; ?>

<?php
if (is_array($aM['mURL'])):
	foreach ($aM['mURL'] as $i => $v):
		if (!$v) continue;
		if (isset($aYoutube[$i]) && $aYoutube[$i]):
?>
<div class="mat-files mt16" style="max-width: 640px;">
	<div class="iframeWrap"><?php echo $aYoutube[$i]; ?></div>
</div>
<?php
		elseif (isset($aM['clurl'][$i]) && $aM['clurl'][$i]):
			$aU = $aM['clurl'][$i];

			if (!$aM['already'] && $aM['fID'] == ''):
				$sURL = \Uri::create('getfile/cllink/:mno/:sid/:link',array('mno'=>$iNO,'sid'=>$aStudent['stID'],'link'=>base64_encode($aU['url'].'/mat')));
			else:
				$sURL = $aU['url'].'/mat';
			endif;

			$sPut = ($aU['put'])? '<span class="attention attn-emp">'.__('済').'</span>':'';
?>
<div class="mat-files mt8">
<?php
			if ($aU['public'] == 0):
?>
	<p><button type="button" class="button na back-silver width-auto"><?php echo $aU['title'].$sPut; ?></button></p>
<?php
			elseif ($aU['public'] == 1):
?>
	<p><a href="<?php echo $sURL; ?>" class="button na do width-auto"><?php echo $aU['title'].$sPut; ?></a></p>
<?php
			else:
				if ($aU['put']):
?>
			<p><a href="<?php echo $sURL; ?>" class="button na default width-auto"><?php echo $aU['title'].$sPut; ?></a></p>
<?php
				else:
?>
			<p><button type="button" class="button na back-silver width-auto"><?php echo $aU['title'].$sPut; ?></button></p>
<?php
				endif;
			endif;
?>
</div>
<?php
		else:
?>
<div class="mat-files mt8">
	<p><i class="fa fa-external-link"></i> <a href="<?php echo $aEURL[$i]; ?>" target="_blank"><?php echo $v; ?></a></p>
</div>
<?php
		endif;
	endforeach;
endif;
if ($aM['mText']):
?>
<div class="mat-text mt16">
	<p><?php echo nl2br($aM['mText']); ?></p>
</div>
<?php
endif;
endif;
?>
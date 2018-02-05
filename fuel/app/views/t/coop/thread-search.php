<?php
$sMyID = $aTeacher['ttID'];
$sMyName = $aTeacher['ttName'];

if (!is_null($aAssistant)):
	$sMyID = $aAssistant['atID'];
	$sMyName = $aAssistant['atName'];
endif;

if (!is_null($aParents)):
	$iMax = count($aParents);
	foreach ($aParents as $aP):
		$bTeach = preg_match('/^[t|a]/', $aP['cID']);
		$cName = ($aP['atName'])? $aP['atName']:(($aP['ttName'])? $aP['ttName']:(($aP['stName'])? $aP['stName']:$aP['cName']));
		$cColor = ($bTeach)? 'font-red':'font-green';
		$sJsKey = $aP['ccID'].'_'.$aP['cNO'];
		$aFiles = null;
		for ($i = 1; $i <= 3; $i++):
			if ($aP['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aP['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aP['fSize'.$i],1);

				$sIcon = 'paperclip';
				$sThumb = null;
				if ($aP['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'t'));
					$sLink  = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aP['fID'.$i],'mode'=>'e'));
					$sIcon = 'film';
				endif;
				$aFiles[$i]['id'] = $aP['fID'.$i];
				$aFiles[$i]['icon'] = $sIcon;
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aP['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aP['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['cDate']):'─';
		if ($aP['cID'] == $sMyID):
			$aWriter = array('font-red',$sMyName);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array('font-gray', __('匿名'));
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aP['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aP['cID'] == $sMyID || !$bTeach)? '':' display: none;';

		$sTitle = $aP['cTitle'];
		$sText = $aP['cText'];
?>
<div class="thread-box">
<h2 class="thread-title"><a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO']; ?>"><?php echo $sTitle ?></a></h2>
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<?php
	if (!is_null($aFiles)):
?>
<ul class="files">
<?php
		foreach ($aFiles as $i => $aF):
			$sFName = $aF['name'];
?>
<li class="width-30 inline-block mobi-100 mr8" obj="<?php echo $i.'_'.$aF['id']; ?>">
<?php
			switch ($aP['fFileType'.$i]):
				case 2:	# 映像の場合
?>
<video class="width-100" controls="controls" preload="none" src="<?php echo $aF['path']; ?>" poster="<?php echo $aF['thumb']; ?>"></video>
<p class="font-size-80"><?php echo $sFName.'('.$aF['size'].')'; ?></p>
<?php
				break;
				case 1:
?>
<img class="width-100" src="<?php echo $aF['path']; ?>" alt="<?php echo $aF['name'].'('.$aF['size'].')'; ?>">
<p class="font-size-80"><?php echo $sFName.'('.$aF['size'].')'; ?></p>
<?php
				break;
				default:
?>
<i class="fa fa-<?php echo $aF['icon']; ?>"></i> <a href="<?php echo $aF['path']; ?>" target="_blank"><?php echo $sFName.'('.$aF['size'].')'; ?></a>
<?php
				break;
			endswitch;
?>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($sText,480)); ?></p>
</div>
</div>
<?php
endforeach;
endif;
?>

<?php
	if (!is_null($aCoops)):
	foreach ($aCoops as $aC):
		$bTeach = preg_match('/^[t|a]/', $aC['cID']);
		$cName = ($aC['atName'])? $aC['atName']:(($aC['ttName'])? $aC['ttName']:(($aC['stName'])? $aC['stName']:$aC['cName']));
		$cColor = ($bTeach)? 'font-red':'font-green';
		$sJsKey = $aC['ccID'].'_'.$aC['cNO'];
		$aFiles = null;
		for ($i = 1; $i <= 3; $i++):
			if ($aC['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aC['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aC['fSize'.$i],1);

				$sIcon = 'paperclip';
				$sThumb = null;
				if ($aC['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aC['fID'.$i],'mode'=>'t'));
					$sLink  = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aC['fID'.$i],'mode'=>'e'));
					$sIcon = 'film';
				endif;
				$aFiles[$i]['id'] = $aC['fID'.$i];
				$aFiles[$i]['icon'] = $sIcon;
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aC['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aC['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aC['cDate']):'─';
		if ($aC['cID'] == $sMyID):
			$aWriter = array('font-red', $sMyName);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'));
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array('font-gray', __('匿名'));
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aC['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aC['cID'] == $sMyID || !$bTeach)? '':' display: none;';

		$sText = $aC['cText'];
?>
<div class="thread-box">
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<?php
	if (!is_null($aFiles)):
?>
<ul class="files">
<?php
		foreach ($aFiles as $i => $aF):
			$sFName = $aF['name'];
?>
<li class="width-30 inline-block mobi-100 mr8" obj="<?php echo $i.'_'.$aF['id']; ?>">
<?php
			switch ($aC['fFileType'.$i]):
				case 2:	# 映像の場合
?>
<video class="width-100" controls="controls" preload="none" src="<?php echo $aF['path']; ?>" poster="<?php echo $aF['thumb']; ?>"></video>
<p class="font-size-80"><?php echo $sFName.'('.$aF['size'].')'; ?></p>
<?php
				break;
				case 1:
?>
<img class="width-100" src="<?php echo $aF['path']; ?>" alt="<?php echo $aF['name'].'('.$aF['size'].')'; ?>">
<p class="font-size-80"><?php echo $sFName.'('.$aF['size'].')'; ?></p>
<?php
				break;
				default:
?>
<i class="fa fa-<?php echo $aF['icon']; ?>"></i> <a href="<?php echo $aF['path']; ?>" target="_blank"><?php echo $sFName.'('.$aF['size'].')'; ?></a>
<?php
				break;
			endswitch;
?>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($sText,480)); ?></p>
</div>
</div>

<?php
	endforeach;
	endif;
?>

<script>
$(function() {
	$("#c<?php echo $iNO; ?>").mark("<?php echo $sWords; ?>");
});
</script>


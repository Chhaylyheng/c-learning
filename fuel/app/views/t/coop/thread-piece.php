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
			$aWriter = array('font-red',$sMyName,' mine');
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'),'');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName,'');
					else:
						$aWriter = array('font-gray', __('匿名'),'');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName,'');
				break;
			endswitch;
		endif;
		$sTileLink = '';
		if ($aCCategory['ccStuWrite'] && $aCCategory['ccStuNum']):
			$sTileLink = '<a href="/t/coop/tile/'.$aP['ccID'].DS.$aP['cNO'].'" class="CoopTileLink" target="_blank" title="'.__('画像のタイル表示').'"><i class="fa fa-object-group"></i></a>';
		endif;
		$sEditBtnDisp = ($aP['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aP['cID'] == $sMyID || !$bTeach)? '':' display: none;';

		$sUnread = (!isset($aAlready[$aP['cNO']]))? '<span class="attention attn-emp">'.__('未読').'</span> ':'';
?>
<div class="thread-box">
<h2 class="thread-title"><a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO']; ?>"><?php echo $sUnread.$aP['cTitle'] ?></a><?php echo $sTileLink; ?></h2>
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0].$aWriter[2]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<?php
	if (!is_null($aFiles)):
?>
<ul class="files">
<?php
		foreach ($aFiles as $i => $aF):
?>
<li class="width-30 inline-block mobi-100 mr8" obj="<?php echo $i.'_'.$aF['id']; ?>">
<?php
			switch ($aP['fFileType'.$i]):
				case 2:	# 映像の場合
?>
<video class="width-100" controls="controls" preload="none" src="<?php echo $aF['path']; ?>" poster="<?php echo $aF['thumb']; ?>"></video>
<?php
				break;
				case 1:
?>
<img class="width-100" src="<?php echo $aF['path']; ?>" alt="<?php echo $aF['name'].'('.$aF['size'].')'; ?>">
<?php
				break;
				default:
?>
<i class="fa fa-<?php echo $aF['icon']; ?>"></i> <a href="<?php echo $aF['path']; ?>" target="_blank"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a>
<?php
				break;
			endswitch;
?>
<span class="f-name" style="display: none;"><?php echo $aF['name']; ?></span>
<span class="f-size" style="display: none;"><?php echo $aF['size']; ?></span>
<span class="f-path" style="display: none;"><?php echo $aF['path']; ?></span>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aP['cText'],480)); ?></p>
<p class="thread-text-raw"><?php echo $aP['cText']; ?></p>
</div>
<div class="thread-option">
<span class="thread-coms"><span class="font-size-160 thread-comnum"><?php echo (int)(isset($aCnt['r'.$aP['cNO']]))? $aCnt['r'.$aP['cNO']]:0; ?></span><?php echo __('コメント'); ?></span>
<button type="button" class="button na do width-auto CoopReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('コメントする'); ?></button>
<button type="button" class="button na default width-auto CoopThreadEdit ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sEditBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopPDelete ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sDeleteBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('削除'); ?></button>
<button type="button" class="button na default width-auto CoopAlreadyShow ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('既読'); ?>:<span class="thread-alrnum"><?php echo (int)$aP['cAlreadyNum']; ?></span></button>
</div>
<div class="thread-res"></div>

<?php
	if (isset($aCoops[$aP['cNO']])):
?>
<ul class="comment-list">
<?php
	foreach ($aCoops[$aP['cNO']] as $aC):
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
			$aWriter = array('font-red', $sMyName,' mine');
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', __('匿名'),'');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName,'');
					else:
						$aWriter = array('font-gray', __('匿名'),'');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName,'');
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aC['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aC['cID'] == $sMyID || !$bTeach)? '':' display: none;';

		$sUnread = (!isset($aAlready[$aC['cNO']]))? '<span class="attention attn-emp">'.__('未読').'</span> ':'';
?>
<li class="anchor-block" id="c<?php echo $aC['cNO']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details"><?php echo $sUnread; ?> by <span class="thread-writer <?php echo $aWriter[0].$aWriter[2]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<?php
	if (!is_null($aFiles)):
?>
<ul class="files">
<?php
		foreach ($aFiles as $i => $aF):
?>
<li class="width-30 inline-block mobi-100 mr8" obj="<?php echo $i.'_'.$aF['id']; ?>">
<?php
			switch ($aC['fFileType'.$i]):
				case 2:	# 映像の場合
?>
<video class="width-100" controls="controls" preload="none" src="<?php echo $aF['path']; ?>" poster="<?php echo $aF['thumb']; ?>"></video>
<?php
				break;
				case 1:
?>
<img class="width-100" src="<?php echo $aF['path']; ?>" alt="<?php echo $aF['name'].'('.$aF['size'].')'; ?>">
<?php
				break;
				default:
?>
<i class="fa fa-<?php echo $aF['icon']; ?>"></i> <a href="<?php echo $aF['path']; ?>" target="_blank"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a>
<?php
				break;
			endswitch;
?>
<span class="f-name" style="display: none;"><?php echo $aF['name']; ?></span>
<span class="f-size" style="display: none;"><?php echo $aF['size']; ?></span>
<span class="f-path" style="display: none;"><?php echo $aF['path']; ?></span>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aC['cText'],480)); ?></p>
<p class="thread-text-raw"><?php echo $aC['cText']; ?></p>
</div>
<div class="thread-option">
<span class="thread-coms"><span class="font-size-160 thread-comnum"><?php echo (int)(isset($aCnt['p'.$aC['cNO']]))? $aCnt['p'.$aC['cNO']]:0; ?></span><?php echo __('コメント'); ?></span>
<button type="button" class="button na do width-auto CoopReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('返信する'); ?></button>
<button type="button" class="button na default width-auto CoopResEdit ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sEditBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopDelete ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sDeleteBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('削除'); ?></button>
<button type="button" class="button na default width-auto CoopAlreadyShow ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('既読'); ?>:<span class="thread-alrnum"><?php echo (int)$aC['cAlreadyNum']; ?></span></button>
</div>
<div class="thread-res"></div>

<?php
	if (isset($aCoops[$aP['cNO']][$aC['cNO']]['children'])):
?>
<ul class="comment-list">
<?php
	foreach ($aCoops[$aP['cNO']][$aC['cNO']]['children'] as $aCG):
		$bTeach = preg_match('/^[t|a]/', $aCG['cID']);
		$cName = ($aCG['atName'])? $aCG['atName']:(($aCG['ttName'])? $aCG['ttName']:(($aCG['stName'])? $aCG['stName']:$aCG['cName']));
		$cColor = ($bTeach)? 'font-red':'font-green';
		$sJsKey = $aCG['ccID'].'_'.$aCG['cNO'];
		$aFiles = null;
		for ($i = 1; $i <= 3; $i++):
			if ($aCG['fID'.$i]):
				$sLink = \Uri::create('getfile/s3file/:fid',array('fid'=>$aCG['fID'.$i]));
				$sSize = \Clfunc_Common::FilesizeFormat($aCG['fSize'.$i],1);

				$sIcon = 'paperclip';
				$sThumb = null;
				if ($aCG['fFileType'.$i] == 2):
					$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aCG['fID'.$i],'mode'=>'t'));
					$sLink  = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aCG['fID'.$i],'mode'=>'e'));
					$sIcon = 'film';
				endif;
				$aFiles[$i]['id'] = $aCG['fID'.$i];
				$aFiles[$i]['icon'] = $sIcon;
				$aFiles[$i]['thumb'] = $sThumb;
				$aFiles[$i]['path'] = $sLink;
				$aFiles[$i]['name'] = $aCG['fName'.$i];
				$aFiles[$i]['size'] = $sSize;
			endif;
		endfor;
		$sDate = ($aCG['cDate'] != '0000-00-00 00:00:00')? ClFunc_Tz::tz('Y/m/d H:i',$tz,$aCG['cDate']):'─';
		if ($aCG['cID'] == $sMyID):
			$aWriter = array('font-red', $sMyName,' mine');
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', '匿名','');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName,'');
					else:
						$aWriter = array('font-gray', '匿名','');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName,'');
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aCG['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aCG['cID'] == $sMyID || !$bTeach)? '':' display: none;';

		$sUnread = (!isset($aAlready[$aCG['cNO']]))? '<span class="attention attn-emp">'.__('未読').'</span> ':'';
	?>
<li class="anchor-block" id="c<?php echo $aCG['cNO']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details"><?php echo $sUnread; ?> by <span class="thread-writer <?php echo $aWriter[0].$aWriter[2]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
<div class="thread-body">
<?php
	if (!is_null($aFiles)):
?>
<ul class="files">
<?php
		foreach ($aFiles as $i => $aF):
?>
<li class="width-30 inline-block mobi-100 mr8" obj="<?php echo $i.'_'.$aF['id']; ?>">
<?php
			switch ($aCG['fFileType'.$i]):
				case 2:	# 映像の場合
?>
<video class="width-100" controls="controls" preload="none" src="<?php echo $aF['path']; ?>" poster="<?php echo $aF['thumb']; ?>"></video>
<?php
				break;
				case 1:
?>
<img class="width-100" src="<?php echo $aF['path']; ?>" alt="<?php echo $aF['name'].'('.$aF['size'].')'; ?>">
<?php
				break;
				default:
?>
<i class="fa fa-<?php echo $aF['icon']; ?>"></i> <a href="<?php echo $aF['path']; ?>" target="_blank"><?php echo $aF['name'].'('.$aF['size'].')'; ?></a>
<?php
				break;
			endswitch;
?>
<span class="f-name" style="display: none;"><?php echo $aF['name']; ?></span>
<span class="f-size" style="display: none;"><?php echo $aF['size']; ?></span>
<span class="f-path" style="display: none;"><?php echo $aF['path']; ?></span>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
<p class="thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aCG['cText'],480)); ?></p>
<p class="thread-text-raw"><?php echo $aCG['cText']; ?></p>
</div>
<div class="thread-option">
<button type="button" class="button na default width-auto CoopResEdit ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sEditBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
<button type="button" class="button na default width-auto CoopDelete ml4" style="padding: 4px 8px; vertical-align: middle;<?php echo $sDeleteBtnDisp; ?>" value="<?php echo $sJsKey; ?>"><?php echo __('削除'); ?></button>
<button type="button" class="button na default width-auto CoopAlreadyShow ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('既読'); ?>:<span class="thread-alrnum"><?php echo (int)$aCG['cAlreadyNum']; ?></span></button>
</div>
<div class="thread-res"></div>
</div>
</li>
<?php
	endforeach;
?>
</ul>
<?php
	endif;
?>

</div>
</li>
<?php
	endforeach;
?>
</ul>
<?php
	endif;
?>

</div>
<?php
endforeach;
endif;
?>

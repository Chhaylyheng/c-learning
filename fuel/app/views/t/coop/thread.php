<?php
$sMyID = $aTeacher['ttID'];
$sMyName = $aTeacher['ttName'];

if (!is_null($aAssistant)):
	$sMyID = $aAssistant['atID'];
	$sMyName = $aAssistant['atName'];
endif;

?>
<div class="res-field"></div>
<?php
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
		$sTileLink = '';
		if ($aCCategory['ccStuWrite'] && $aCCategory['ccStuNum']):
			$sTileLink = '<a href="/t/coop/tile/'.$aP['ccID'].DS.$aP['cNO'].'" class="CoopTileLink" target="_blank" title="'.__('画像のタイル表示').'"><i class="fa fa-object-group"></i></a>';
		endif;
		$sEditBtnDisp = ($aP['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aP['cID'] == $sMyID || !$bTeach)? '':' display: none;';
?>
<div class="anchor-block" id="c<?php echo $aP['cNO']; ?>">
<div class="thread-box">
<h2 class="thread-title"><a href="/t/coop/thread/<?php echo $aP['ccID'].DS.$aP['cNO']; ?>"><?php echo $aP['cTitle'] ?></a><?php echo $sTileLink; ?></h2>
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
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
?>
<li class="anchor-block" id="c<?php echo $aC['cNO']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
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
			$aWriter = array('font-red', $sMyName);
		else:
			switch ($aCCategory['ccAnonymous']):
				case 0:
					$aWriter = array('font-gray', '匿名');
				break;
				case 1:
					if ($bTeach):
						$aWriter = array($cColor, $cName);
					else:
						$aWriter = array('font-gray', '匿名');
					endif;
				break;
				case 2:
					$aWriter = array($cColor, $cName);
				break;
			endswitch;
		endif;
		$sEditBtnDisp = ($aCG['cID'] == $sMyID)? '':' display: none;';
		$sDeleteBtnDisp = ($aCG['cID'] == $sMyID || !$bTeach)? '':' display: none;';
	?>
<li class="anchor-block" id="c<?php echo $aCG['cNO']; ?>"><span class="tree-line"></span>
<div class="thread-box">
<div class="thread-details">by <span class="thread-writer <?php echo $aWriter[0]; ?>"><?php echo $aWriter[1]; ?></span> on <span class="thread-date"><?php echo $sDate; ?></span></div>
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
</div>
<?php
endforeach;
endif;
?>


<form action="/t/coop/res/<?php echo $aCCategory['ccID']; ?>" method="post" class="res-box width-100" style="display: none;">
	<input type="hidden" name="c_no" value="0">
	<input type="hidden" name="c_id" value="">
	<input type="hidden" name="mode" value="input">
	<input type="hidden" name="ct" value="<?php echo $aClass['ctID']; ?>">
	<div class="res-msg-box"></div>
	<div class="formControl font-size-90" style="margin: auto;">
		<div class="formGroup" style="display: none;">
			<div class="formLabel"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box">
				<input type="text" name="c_title" value="" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-100 text-left">
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><a href="#" class="upload-box-toggle"><i class="fa fa-plus-square-o"></i><i class="fa fa-minus-square-o" style="display: none;"></i> <?php echo __('ファイル選択'); ?></a></div>
			<div class="formContent inline-box">
				<ul class="file-uploader" style="display: none;">
<?php
	for ($i = 1; $i <= 3; $i++):
?>
					<li class="width-16em file-box">
						<div class="input-cover text-center" style="background-size: cover;">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: none;">
								<p><i class="fa fa-paperclip"></i> <a href="" class="file" target="_blank"><span class="name"></span></a><br><span class="size"></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="c_file<?php echo $i; ?>" value="">
					</li>
<?php
	endfor;
?>
				</ul>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('本文'); ?></div>
			<div class="formContent inline-box">
<?php if (!preg_match('/CL_AIR/i', $_SERVER['HTTP_USER_AGENT'])): ?>
				<div id="dbbtn" class="mb4"></div>
<?php endif; ?>
				<textarea name="c_text" class="width-100 text-left font-size-100" rows="6"></textarea>
			</div>
		</div>
<?php
	$sMNone = '';
	if ($aClass['tpNum'] == 0 && $aCCategory['ccStuNum'] == 0 && is_null($aAssistant) && is_null($aAssist)):
		$sMNone = 'display: none;';
	endif;
	$sTNone = '';
	if ($aClass['tpNum'] == 0 && is_null($aAssistant) && is_null($aAssist)):
		$sTNone = 'display: none;';
	endif;
	$sSNone = ($aCCategory['ccStuNum'] == 0)? 'display: none;':'';
?>
		<div class="formGroup" style="<?php echo $sMNone; ?>">
			<div class="formLabel"><?php echo __('メール通知'); ?></div>
			<div class="formContent inline-box">
				<label class="mr16" style="<?php echo $sTNone; ?>"><input type="checkbox" name="mail-teacher" value="1"><?php echo __('先生に通知'); ?></label>
				<label style="<?php echo $sSNone; ?>"><input type="checkbox" name="mail-student" value="1"><?php echo __('学生に通知'); ?></label>
			</div>
		</div>
	</div>
	<div class="res-button-box mt8">
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 ThreadRegist" style="padding: 4px 8px; display: none;" name="sub_state" value="1"><?php echo __('スレッドを登録する'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toComment" style="padding: 4px 8px; display: none;"><?php echo __('コメントする'); ?></button>
		<button type="submit" class="button do na width-auto CoopReplyToSubmit font-size-90 toUpdate" style="padding: 4px 8px; display: none;"><?php echo __('更新する'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToQuote font-size-90" style="padding: 4px 8px;"><?php echo __('引用'); ?></button>
		<button type="button" class="button default na width-auto CoopReplyToCancel font-size-90" style="padding: 4px 8px;"><?php echo __('キャンセル'); ?></button>
	</div>
<?php
	\Clfunc_Common::DropboxChooseBtn();
?>
</form>

<div class="QBFloatBox" style="display: none;">
	<div class="QBFloatClose"><i class="fa fa-times"></i></div>
	<div class="QBFloatMember"></div>
</div>


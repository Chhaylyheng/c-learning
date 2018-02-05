<?php
	$errClass = array(
		'm_title'=>'',
		'm_url.0'  =>'',
		'm_url.1'  =>'',
		'm_url.2'  =>'',
		'm_url.3'  =>'',
		'm_file' =>'',
		'm_text' =>''
	);
	$errMsg = $errClass;

	if (!is_null($error)):
		foreach ($errClass as $key => $val):
			if (isset($error[$key])):
				$errClass[$key] = ' class="input-error"';
				$errMsg[$key] = '<p class="error-msg">'.$error[$key].'</p>';
			endif;
		endforeach;
	endif;

	$sSubBtn = '登録';
	$sAction = 'create/'.$aMCategory['mcID'];
	if (isset($aMaterial)):
		$sSubBtn = '更新';
		$sAction = 'edit/'.$aMCategory['mcID'].'/'.$aMaterial['mNO'];
	endif;
?>


<div class="info-box">
<form action="/t/material/<?php echo $sAction; ?>" method="post">
	<p class="mt0 text-right"><?php echo __('<sup>*</sup>は必須項目'); ?></p>
	<div class="formControl" style="margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('タイトル'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<input type="text" name="m_title" value="<?php echo $m_title; ?>" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('タイトルを入力してください'); ?>" class="width-40em text-left"<?php echo $errClass['m_title']; ?>>
				<?php echo $errMsg['m_title']; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材ファイル選択'); ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
<?php
	$bAlready = false;
	if ($fileinfo['file']):
		$bAlready = true;
		$sName = $fileinfo['name'];
		if (isset($aMaterial) && $fileinfo['file'] == $aMaterial['fID']):
			$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$fileinfo['file']));
		else:
			$sFile = \Uri::create('getfile/download/temp/:file/:name', array('file'=>$fileinfo['file'],'name'=>$sName));
		endif;
		$sSize = \Clfunc_Common::FilesizeFormat($fileinfo['size'],1);
	endif;
?>
					<li class="width-20em">
						<div class="input-cover text-center" style="background-size: cover;<?php echo (($bAlready && $fileinfo['isimg'])? 'background-image: url(\''.$sFile.'\')':'');?>">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
								<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="m_file" value="<?php echo (($bAlready)? htmlspecialchars(serialize($fileinfo)):''); ?>">
					</li>
				</ul>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材URL'); ?></div>
			<div class="formContent inline-box">
<?php if (!preg_match('/CL_AIR/i', $_SERVER['HTTP_USER_AGENT'])): ?>
			<div id="dbbtn" class="mb4"></div>
<?php endif; ?>

<?php $id = 'id="dburl"'; ?>
<?php for ($i = 0; $i <= 3; $i++): ?>
			<div class="ExtURLBox" style="padding: 8px 0; border-bottom: 1px dotted silver;" data="<?php echo $i; ?>">
				<?php $sChain = (isset($clurl[$i]) && $clurl[$i])? '<i class="fa fa-chain"></i> '.$clurl[$i]:''; ?>
				<p id="material-chain-<?php echo $i; ?>" class="mt4 mb4 font-blue font-bold"><?php echo $sChain; ?></p>
				<input type="text" name="m_url[]" value="<?php echo isset($m_url[$i])? $m_url[$i]:''; ?>" maxlength="255" placeholder="" <?php echo $id; ?> class="width-100em text-left"<?php echo isset($errClass['m_url.'.$i])? $errClass['m_url.'.$i]:''; ?>>
				<?php echo (isset($errMsg['m_url.'.$i]))? $errMsg['m_url.'.$i]:''; ?>
				<div class="mt4" data="<?php echo $aClass['ctID']; ?>">
<?php
						$aChoice = array(
							'Quest'=>array('color'=>'default','disable'=>''),
							'Test'=>array('color'=>'default','disable'=>''),
							'Coop'=>array('color'=>'default','disable'=>''),
							'Report'=>array('color'=>'default','disable'=>''),
						);
						if (!$aNums['Quest']):
							$aChoice['Quest']['color'] = 'back-silver';
							$aChoice['Quest']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Test']):
							$aChoice['Test']['color'] = 'back-silver';
							$aChoice['Test']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Coop']):
							$aChoice['Coop']['color'] = 'back-silver';
							$aChoice['Coop']['disable'] = 'disabled="disabled"';
						endif;
						if (!$aNums['Report']):
							$aChoice['Report']['color'] = 'back-silver';
							$aChoice['Report']['disable'] = 'disabled="disabled"';
						endif;
?>
					<button type="button" class="button na <?php echo $aChoice['Quest']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Quest']['disable']; ?> style="padding: 4px 8px;" data="quest"><?php echo __('アンケートから選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Test']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Test']['disable']; ?> style="padding: 4px 8px;" data="test"><?php echo __('小テストから選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Coop']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Coop']['disable']; ?> style="padding: 4px 8px;" data="coop"><?php echo __('協働板から選択'); ?></button>
					<button type="button" class="button na <?php echo $aChoice['Report']['color']; ?> width-auto ListChoice font-size-90 mb8" <?php echo $aChoice['Report']['disable']; ?> style="padding: 4px 8px;" data="report"><?php echo __('レポートから選択'); ?></button>
				</div>
			</div>
<?php $id = ''; ?>
<?php endfor; ?>

			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('コメント等'); ?></div>
			<div class="formContent inline-box">
				<textarea name="m_text" class="width-50em text-left"<?php echo $errClass['m_text']; ?> rows="6"><?php echo $m_text; ?></textarea>
				<?php echo $errMsg['m_text']; ?>
			</div>
		</div>
<?php
	if (!isset($aMaterial)):
		$aCheck = array(0=>'',1=>'');
		$aCheck[$m_public] = ' checked';
?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材の公開'); ?></div>
			<div class="formContent inline-box">
				<label class="formChk"><input type="radio" name="m_public" value="0"<?php echo $aCheck[0]; ?>><?php echo __('後で公開する'); ?></label>
				<label class="formChk"><input type="radio" name="m_public" value="1"<?php echo $aCheck[1]; ?>><?php echo __('すぐに公開する'); ?></label>
				<?php if ($aMCategory['mcMail']): ?>
				<p class="font-silver"><?php echo __('※教材を公開した際に、履修学生へ教材公開通知のメールが送信されます。'); ?></p>
				<?php endif; ?>
			</div>
		</div>
<?php endif; ?>
	</div>
	<div class="button-box mt32">
		<button type="submit" class="button do" name="sub_state" value="1"><?php echo $sSubBtn; ?><?php echo __('確認'); ?></button>
	</div>
</form>
</div>
<?php
	\Clfunc_Common::DropboxChooseBtn();
?>
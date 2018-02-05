<?php

$sFile = null;
if ($aReport['baseFID'] != ''):
	$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aReport['baseFID'],'mode'=>'e'));
	$sSize = \Clfunc_Common::FilesizeFormat($aReport['baseFSize'],1);
	$sIcon = 'paperclip';
	$sThumb = null;
	if ($aReport['baseFFileType'] == 2):
		$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aReport['baseFID'],'mode'=>'t'));
		$sIcon = 'film';
	endif;
	$sFile = '<p>'.__('添付ファイル').'：<i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.$aReport['baseFName'].'（'.$sSize.'）</a></p>';
endif;

?>


<div class="info-box">
<?php if (isset($error)): ?>
	<p class="error-box"><?php echo $error; ?></p>
<?php endif; ?>

<form action="/s/report/put/<?php echo $aReport['rbID']; ?>" method="POST">
	<div class="formControl" style="margin: auto; width: 800px;">

		<div class="formGroup">
			<div class="formLabel"><?php echo __('内容/備考'); ?></div>
			<div class="formContent inline-box">
				<p class="mt4" style="max-width: 100%;"><?php echo nl2br(\Clfunc_Common::url2link($aReport['rbText'],480)); ?></p>
<?php
switch ($aReport['baseFFileType']):
case 2:	# 映像の場合
	?>
<video style="width: 50%; max-width: 100%;" controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video>
<?php
	break;
	case 1:
?>
<img style="width: 50%; max-width: 100%;" src="<?php echo $sPath; ?>" alt="<?php echo $aReport['baseFName'].'（'.$sSize.'）'; ?>">
<?php
	break;
	default:
		echo $sFile;
	break;
endswitch;
?>
			</div>
		</div>


		<div class="formGroup">
			<div class="formLabel"><?php echo __('提出テキスト'); ?></div>
			<div class="formContent inline-box">
				<textarea name="rpText" class="text-left" rows="6" style="max-width: 100%;"><?php echo (isset($aInput['rpText']))? $aInput['rpText']:''; ?></textarea>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('提出ファイル'); ?></div>
			<div class="formContent inline-box">
				<ul class="file-uploader">
<?php
for ($i = 1; $i <= 3; $i++):
	$bAlready = false;
	if (isset($aInput['f'.$i]) && $aInput['f'.$i] != ''):
		$bAlready = true;
		$sName = $aInput['fileinfo'.$i]['name'];
		$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$aInput['fileinfo'.$i]['file']));
		$sSize = \Clfunc_Common::FilesizeFormat($aInput['fileinfo'.$i]['size'],1);
	endif;
?>
					<li class="mt4 file-box" style="width: 33%; display: inline-block;">
						<div class="input-cover text-center" style="background-size: cover;<?php echo (($bAlready)? 'background-image: url(\''.$sFile.'\')':'');?>">
							<i class="fa fa-plus fa-3x mt16"></i>
							<p><?php echo __('ファイルを選択'); ?></p>
							<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
								<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
								<p class="remove"><i class="fa fa-times fa-2x"></i></p>
							</div>
							<div class="upload-progress"><div class="upload-progress-bar"></div></div>
						</div>
						<span class="hidden-file"><input type="file" name="file-input" autocomplete="off"></span>
						<input type="hidden" name="<?php echo 'f'.$i; ?>" value="<?php echo (($bAlready)? htmlspecialchars(serialize($aInput['fileinfo'.$i])):''); ?>">
					</li>
<?php endfor; ?>
				</ul>
			</div>
		</div>

	</div>

	<p class="font-gray text-center"><?php echo __('※提出テキスト、提出ファイルのどちらかの入力が必須です。'); ?></p>

	<div class="button-box mt16">
		<button type="submit" class="button do formSubmit" name="state" value="check"><?php echo __('提出確認'); ?></button>
	</div>

</form>
</div>

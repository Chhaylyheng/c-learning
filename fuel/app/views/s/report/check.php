<div class="info-box">
	<p class="text-center font-red font-size-120 mt8 mb8 font-bold"><i class="fa fa-exclamation-circle fa-lg va-top"></i> <?php echo __('まだ提出は完了していません。'); ?></p>
	<p class="text-center mb8"><?php echo __('提出内容を確認の上、「提出する」ボタンを押してください。'); ?></p>
	<hr>

<form action="/s/report/check/<?php echo $aReport['rbID']; ?>" method="POST">
	<div class="formControl" style="margin: auto;">

		<div class="formGroup">
			<div class="formLabel"><?php echo __('提出テキスト'); ?></div>
			<div class="formContent inline-box font-blue">
				<p style="max-width: 100%;"><?php echo ($aInput['rpText'])? nl2br($aInput['rpText']):'─'; ?></p>
			</div>
		</div>

		<div class="formGroup">
			<div class="formLabel"><?php echo __('提出ファイル'); ?></div>
			<div class="formContent inline-box font-blue">
<?php
for ($i = 1; $i <= 3; $i++):
	if (isset($aInput['f'.$i]) && $aInput['f'.$i] != ''):
		$sName = $aInput['fileinfo'.$i]['name'];
		if ($aInput['fileinfo'.$i]['file'] == $aPut['fID'.$i]):
			$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$aInput['fileinfo'.$i]['file']));
		else:
			$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$aInput['fileinfo'.$i]['file'], 'name'=>$aInput['fileinfo'.$i]['name']));
		endif;
		$sSize = \Clfunc_Common::FilesizeFormat($aInput['fileinfo'.$i]['size'],1);

		if ($aInput['fileinfo'.$i]['isimg']):
?>
<p class="mb8"><i class="fa fa-paperclip"></i><?php echo $sName.'（'.$sSize.'）'; ?><br><img style="width: 30em; max-width: 100%;" src="<?php echo $sFile; ?>" alt="<?php echo $sName.'（'.$sSize.'）'; ?>"></p>
<?php else: ?>
<p class="mb8"><i class="fa fa-paperclip"></i><?php echo $sName.'（'.$sSize.'）'; ?></p>
<?php
		endif;
	endif;
?>
<?php endfor; ?>
			</div>
		</div>

	</div>
	<hr>
	<div class="button-box mt16">
		<button class="button default na width-auto mt16" style="float: left;" value="back" name="back" type="submit"><?php echo __('戻る'); ?></button>
		<button class="button do" value="check" name="check" type="submit"><?php echo __('提出する'); ?></button>
	</div>
</form>
</div>

<?php
$sMode = 'create';
$sMSubmit = '登録';
if ($iNO):
	$sMode = 'edit';
	$sMSubmit = '更新';
endif;

?>
<div class="info-box">
<form action="/s/alog/check/<?php echo $aALTheme['altID'].(($iNO)? DS.$iNO:''); ?>" method="post">
	<p class="mt0 text-center"><?php echo __('以下の内容で記録を'.$sMSubmit.'します。'); ?></p>
	<div class="formControl" style="min-width: 50%; margin: auto;">

<?php if ($aALTheme['altTitle']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTitleLabel']; ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo $al_title; ?>
			</div>
		</div>
<?php endif; ?>

<?php if ($aALTheme['altRange']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altRangeLabel']; ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo $al_date_s.' '.$al_time_s.' ～ '.$al_date_e.' '.$al_time_e; ?>
			</div>
		</div>
<?php endif; ?>

		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altTextLabel']; ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo nl2br($al_text); ?>
			</div>
		</div>

<?php if ($aALTheme['altFile']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材ファイル'); ?></div>
			<div class="formContent inline-box">
				<?php
					if ($fileinfo['file']):
						$sName = $fileinfo['name'];
						$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$fileinfo['file'], 'name'=>$fileinfo['name']));
						$sSize = \Clfunc_Common::FilesizeFormat($fileinfo['size'],1);
				?>
				<div>
					<?php if ($fileinfo['isimg']): ?>
					<img src="<?php echo $sFile; ?>" class="width-16em">
					<?php endif; ?>
					<p class="font-blue mt4">
						<?php echo $sName; ?><br>
						<?php echo $sSize; ?>
					</p>
				</div>
				<?php endif; ?>
			</div>
		</div>
<?php endif; ?>

<?php if ($aALTheme['altOpt1']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt1Label']; ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo nl2br($al_opt1); ?>
			</div>
		</div>
<?php endif; ?>

<?php if ($aALTheme['altOpt2']): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo $aALTheme['altOpt2Label']; ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo nl2br($al_opt2); ?>
			</div>
		</div>
<?php endif; ?>

	</div>
	<div class="button-box mt32">
		<button type="submit" class="button na cancel width-auto mt16" style="float: left;" name="back" value="1"><?php echo __('戻る'); ?></button>
		<button type="submit" class="button do" name="state" value="1"><?php echo __($sMSubmit); ?></button>
	</div>
</form>
</div>

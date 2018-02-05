<?php
$sMode = 'create';
$sMSubmit = '登録';
if ($iNO):
	$sMode = 'edit';
	$sMSubmit = '更新';
endif;

?>
<div class="info-box">
<form action="/t/material/check/<?php echo $aMCategory['mcID'].(($iNO)? DS.$iNO:''); ?>" method="post">
	<p class="mt0 text-center"><?php echo __('以下の内容で教材を'.$sMSubmit.'します。'); ?></p>
	<div class="formControl" style="min-width: 50%; margin: auto;">
		<div class="formGroup">
			<div class="formLabel"><?php echo __('タイトル'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo $m_title; ?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材ファイル'); ?></div>
			<div class="formContent inline-box">
				<?php
					if ($fileinfo['file']):
						$sName = $fileinfo['name'];
						if ($fileinfo['file'] == $aMaterial['fID']):
							$sFile = \Uri::create('getfile/s3file/:fid',array('fid'=>$fileinfo['file']));
						else:
							$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'temp','file'=>$fileinfo['file'], 'name'=>$fileinfo['name']));
						endif;
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
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材URL'); ?></div>
			<div class="formContent inline-box font-blue">
<?php
if (is_array($m_url)):
	foreach ($m_url as $i => $v):
		if (!$v) continue;
?>
<div class="" style="padding: 4px 0; border-bottom: 1px dotted silver">
<?php
		if ($clurl[$i]):
?>
	<i class="fa fa-chain"></i> <?php echo $clurl[$i]; ?><br>
<?php
		endif;
?>
	<?php echo $v; ?><br>
</div>
<?php
	endforeach;
endif;
?>
			</div>
		</div>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('コメント等'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo nl2br($m_text); ?>
			</div>
		</div>
		<?php if (!$iNO): ?>
		<div class="formGroup">
			<div class="formLabel"><?php echo __('教材の公開'); ?></div>
			<div class="formContent inline-box font-blue">
				<?php echo (($m_public)? __('すぐに公開する'):__('後で公開する')); ?>
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

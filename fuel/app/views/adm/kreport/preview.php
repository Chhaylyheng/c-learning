<?php if (!is_null($aMsg)): ?>
	<p class="error-msg"><?php echo $aMsg['error']; ?></p>
<?php endif; ?>

<form action="/adm/kreport/preview/<?php echo $aReport['no']; ?>" method="POST">

<div class="info-box questionnaire-box">

	<div class="info-box mt4 pt0">
		<h2><i class="fa fa-upload"></i> ファイルアップロード（1ファイル <?php echo CL_FILESIZE; ?>MBまで）</h2>
		<ul class="file-uploader">
<?php
	for ($i = 0; $i < 5; $i++):
		$bAlready = false;
		if (isset($aUploads[$i])) {
			$bAlready = true;
			$sName = $aUploads[$i]['name'];
			$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>(preg_match('/^_kreport_/',$aUploads[$i]['file'])? 'temp':'kreport'), 'file'=>$aUploads[$i]['file'], 'name'=>$aUploads[$i]['name']));
			$sSize = \Clfunc_Common::FilesizeFormat($aUploads[$i]['size'],1);
		}
?>
			<li class="width-20">
				<div class="input-cover">
					<i class="fa fa-plus fa-3x"></i>
					<p>ファイルを選択</p>
					<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
						<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
						<p class="remove"><i class="fa fa-times fa-2x"></i></p>
					</div>
					<div class="upload-progress"><div class="upload-progress-bar"></div></div>
				</div>
				<span style="display: none;"><input type="file" name="file-input" autocomplete="off" disabled="disabled"></span>
				<input type="hidden" name="files[]" value="<?php echo (($bAlready)? htmlspecialchars(serialize($aUploads[$i])):''); ?>">
			</li>
<?php endfor; ?>
		</ul>
	</div>



<?php foreach ($aQuery as $i => $aQ): ?>
<?php $iKrNO = $aQ['krNO']; ?>
	<div class="info-box mt4 pt0">
	<h2><i class="fa fa-chevron-right"></i> <?php echo $aQ['krText']; ?></h2>
	<hr>
	<div class="answer mt0 text-left">
	<?php if ($aQ['krStyle'] != 2): ?>
		<?php
			$aChoice = array();
			for ($i = 1; $i <= (int)$aQ['krChoiceNum']; $i++):
				$aChoice[$i] = null;
				if ($aQ['krStyle'] == 1):
					$sLClass = 'checkbox';
					$aChoice[$i] .= '<input type="checkbox" name="checkSel_'.$iKrNO.'[]" value="'.$i.'"><span>';
				else:
					$sLClass = 'radio';
					$aChoice[$i] .= '<input type="radio" name="radioSel_'.$iKrNO.'" value="'.$i.'"><span>';
				endif;
				$aChoice[$i] .= nl2br($aQ['krChoice'.$i]).'</span>';
			endfor;
			foreach ($aChoice as $sC):
				echo '<label class="radio">'.$sC.'</label>';
			endforeach;
		?>
	<?php else: ?>
		<textarea class="" name="textAns_<?php echo $iKrNO; ?>"></textarea>
	<?php endif; ?>
	</div>
	</div>
<?php endforeach; ?>
</div>
<div class="info-box questionnaire-box">
	<p class="button-box">
		<button type="submit" class="button confirm" name="state" value="save">一時保存</button>
		<button type="submit" class="button do" name="state" value="check">提出確認</button>
	</p>
</div>
</form>

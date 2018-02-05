<?php if (!is_null($aMsg)): ?>
	<p class="error-msg">入力に誤りがあります。各設問をご確認ください。</p>
<?php endif; ?>

<form action="/t/kreport/ans/<?php echo $aReport['krYear'].DS.$aReport['krPeriod'].DS.$iSub; ?>" method="POST">

<div class="info-box questionnaire-box">

	<div class="info-box mt4 pt0">
		<h2><i class="fa fa-upload"></i> ファイルアップロード</h2>
		<p class="font-default text-left">※レポートファイルまたはレポートの参考ファイルがあれば添付してください。</p>
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
					<i class="fa fa-plus fa-3x mt16"></i>
					<p>ファイルを選択</p>
					<div class="uploaded-file" style="display: <?php echo (($bAlready)? 'block':'none'); ?>;">
						<p><i class="fa fa-paperclip"></i> <a href="<?php echo (($bAlready)? $sFile:'');; ?>" class="file" target="_blank"><span class="name"><?php echo (($bAlready)? $sName:''); ?></span></a><br><span class="size"><?php echo (($bAlready)? $sSize:''); ?></span></p>
						<p class="remove"><i class="fa fa-times fa-2x"></i></p>
					</div>
					<div class="upload-progress"><div class="upload-progress-bar"></div></div>
				</div>
				<span style="display: none;"><input type="file" name="file-input" autocomplete="off"></span>
				<input type="hidden" name="files[]" value="<?php echo (($bAlready)? htmlspecialchars(serialize($aUploads[$i])):''); ?>">
			</li>
<?php endfor; ?>
		</ul>
	</div>

<?php foreach ($aQuery as $i => $aQ): ?>
<?php $iKrNO = $aQ['krNO']; ?>
	<div class="info-box mt4 pt0">
	<h2><i class="fa fa-chevron-right"></i> <?php echo $aQ['krText']; ?></h2>
	<?php if (isset($aMsg[$iKrNO])): ?>
	<p class="error-msg"><?php echo $aMsg[$iKrNO]; ?></p>
	<?php endif; ?>
	<div class="answer mt0 text-left">
	<?php if ($aQ['krStyle'] != 2): ?>
		<?php
			$aChoice = array();
			for ($i = 1; $i <= (int)$aQ['krChoiceNum']; $i++):
				$aChoice[$i] = null;
				if ($aQ['krStyle'] == 1):
					$aSel = explode('|',$aInput[$iKrNO]['select']);
					$sCheck = (array_search($i, $aSel) !== false)? ' checked':'';
					$sLCSS  = (array_search($i, $aSel) !== false)? 'background-color: #62bc64; color: #ffffff;':'background-color: #cbcbcb; color: #545454;';
					$aChoice[$i] .= '<label class="radio" style="'.$sLCSS.'"><input type="checkbox" name="checkSel_'.$iKrNO.'[]" value="'.$i.'"'.$sCheck.'><span>';
				else:
					$sCheck = ($aInput[$iKrNO]['select'] == $i)? ' checked':'';
					$sLCSS  = ($aInput[$iKrNO]['select'] == $i)? 'background-color: #62bc64; color: #ffffff;':'background-color: #cbcbcb; color: #545454;';
					$aChoice[$i] .= '<label class="radio" style="'.$sLCSS.'"><input type="radio" name="radioSel_'.$iKrNO.'" value="'.$i.'"'.$sCheck.'><span>';
				endif;
				$aChoice[$i] .= nl2br($aQ['krChoice'.$i]).'</span></label>';
			endfor;
			foreach ($aChoice as $sC):
				echo $sC;
			endforeach;
		?>
	<?php else: ?>
		<?php $sText = (isset($aInput[$iKrNO]['text']))? $aInput[$iKrNO]['text']:''; ?>
		<textarea class="" name="textAns_<?php echo $iKrNO; ?>"><?php echo $sText; ?></textarea>
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

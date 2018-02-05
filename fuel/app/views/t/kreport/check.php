<form action="/t/kreport/submit/<?php echo $aReport['krYear'].DS.$aReport['krPeriod'].DS.$iSub; ?>" method="POST">

<div class="info-box questionnaire-box">
	<h2>以下の内容で提出します。</h2>
	<hr>
	<p class="question mt16">ファイル</p>
	<div class="answer mt8">
		<?php
			if (!is_null($aUploads)):
				foreach ($aUploads as $f):
					$sName = $f['name'];
					$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>(preg_match('/^_kreport_/',$f['file'])? 'temp':'kreport'), 'file'=>$f['file'], 'name'=>$f['name']));
					$sSize = \Clfunc_Common::FilesizeFormat($f['size'],1);
		?>
			<p class="text-left mt4"><i class="fa fa-paperclip"></i> <a href="<?php echo $sFile; ?>" target="_blank"><?php echo $sName; ?>（<?php echo $sSize; ?>）</a></p>
		<?php
				endforeach;
			endif;
		?>
	</div>
	<hr>
	<?php foreach ($aQuery as $aQ): ?>
	<?php $iKrNO = $aQ['krNO']; ?>
		<p class="question mt16"><?php echo $aQ['krText']; ?></p>
<?php
	if ($aQ['krStyle'] < 2):
		if ($aQ['krStyle'] == 1):
			$aSel = explode('|',$aInput[$iKrNO]['select']);
		else:
			$aSel = array($aInput[$iKrNO]['select']);
		endif;
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQ['krChoiceNum']; $i++):
			$bSel = array_search($i,$aSel);
			$sStyle = ($bSel !== false)? 'background-color: #62bc64; color: #ffffff;':'background-color: #cbcbcb; color: #545454;';
			$sCheck = ($aQ['krStyle'])? (($bSel !== false)? 'fa-check-square-o':'fa-square-o'):(($bSel !== false)? 'fa-dot-circle-o':'fa-circle-o');
			$aChoice[$i] = '<label class="radio-prev" style="'.$sStyle.'"><span><i class="fa '.$sCheck.'"></i> '.$aQ['krChoice'.$i];
			$aChoice[$i] .= '</span></label>';
		endfor;
		print '<div class="answer text-left">';
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
		print '</div>';
	endif;
	if ($aQ['krStyle'] == 2):
		$sText = ($aInput[$iKrNO]['text'])? nl2br($aInput[$iKrNO]['text']):'（無回答）';
?>
	<p class="question mt16"><?php echo $sText; ?></p>
<?php endif; ?>
	<hr>
	<?php endforeach; ?>
	<p class="button-box">
		<button class="button na cancel width-auto mt16" style="float: left;" value="back" name="state" type="submit">回答に戻る</button>
		<button class="button do" value="check" name="state" type="submit">提出する</button>
	</p>
</div>

</form>

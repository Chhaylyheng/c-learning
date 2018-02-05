<section class="pt0">
	<div class="info-box table-box record-table admin-table">
		<table class="mb16">
		<tbody>
		<?php
			foreach ($aQuery as $i => $aQ):
				$sOdd = ($i % 2)? '':'odd';
				$iKrNO = $aQ['krNO'];
		?>
		<tr class="<?php echo $sOdd; ?>">
			<td><?php echo $aQ['krText']; ?></td>
			<td>
			<?php
				if ($aQ['krStyle'] < 2):
					$aChoice = array();
					for ($i = 1; $i <= (int)$aQ['krChoiceNum']; $i++):
						$bSel = ($aAns[$iKrNO]['krChoice'.$i])? true:false;
						$sColor = ($bSel !== false)? ' radio-chk':'';
						$sCheck = ($aQ['krStyle'])? (($bSel !== false)? 'fa-check-square-o':'fa-square-o'):(($bSel !== false)? 'fa-dot-circle-o':'fa-circle-o');
						$aChoice[$i] = '<label class="radio-prev'.$sColor.'"><i class="fa '.$sCheck.' fa-fw"></i> <span>'.$aQ['krChoice'.$i];
						$aChoice[$i] .= '</span></label>';
					endfor;
			?>
			<div class="answer">
			<?php
					foreach ($aChoice as $sC):
						echo $sC;
					endforeach;
			?>
			</div>
			<?php
				endif;
				if ($aQ['krStyle'] == 2):
					$sText = ($aAns[$iKrNO]['krText'])? nl2br($aAns[$iKrNO]['krText']):'（無回答）';
			?>
				<p class="font-blue"><?php echo $sText; ?></p>
			<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
		<tr>
			<td class="width-30">添付ファイル</td>
			<td>
			<?php
				for ($i = 1; $i <= 5; $i++):
					if ($aPut['krFile'.$i.'Name']):
						$sName = $aPut['krFile'.$i.'Name'];
						$sFile = \Uri::create('getfile/download/:dir/:file/:name',array('dir'=>'kreport', 'file'=>$aPut['krFile'.$i.'File'], 'name'=>$sName));
						$sSize = \Clfunc_Common::FilesizeFormat($aPut['krFile'.$i.'Size'],1);
			?>
				<p class="mt4"><i class="fa fa-paperclip"></i> <a href="<?php echo $sFile; ?>" target="_blank"><?php echo $sName; ?><span class="font-size-80">（<?php echo $sSize; ?>）</span></a></p>
			<?php
					endif;
				endfor;
			?>
			</td>
		</tr>
		</tbody>
		</table>
	</div>
</section>

<section class="info-box kreport-data" data="<?php echo $aReport['krYear'].'|'.$aReport['krPeriod'].'|'.$aPTeacher['ttID'].'|Admin|'.$aPut['krSub']; ?>">
	<h2>コメント</h2>
	<hr>
	<p class="comment-more-show" style="display: none;"><span>もっと見る <i class="fa fa-angle-up"></i></span></p>
	<ul class="comment-show-box" cnt="0">
	</ul>
</section>

<ul class="comment-item-template">
<li class="comment-item comment-item-other" no="" style="display: none;">
	<img style="width: 50px; height: 50px;" src="">
	<p class="name">大学 氏名</p>
	<p class="comment">コメント</p>
	<p class="time">MM/DD<br>HH:mm</p>
</li>
<li class="comment-item comment-item-mine" no="" style="display: none;">
	<p class="time">MM/DD<br>HH:mm</p>
	<p class="comment">コメント</p>
</li>
</ul>
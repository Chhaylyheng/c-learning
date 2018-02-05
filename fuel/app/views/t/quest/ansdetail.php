<?php
	$bQuick = ($aQuest['qbQuickMode'])? true:false;
	$iWidth = ((int)$aQuest['qbQueryStyle'] == 2)? 45:(((int)$aQuest['qbQueryStyle'] == 3)? 30:95);
	$sComP = __('※このコメントは学生が閲覧することはできません。');

	switch ($aQuest['qbComPublic']):
	case 1:
		$sComP = __('※このコメントは回答者本人のみが閲覧できます。');
	break;
	case 2:
		if ($aQuest['qbAnsPublic']):
			$sComP = __('※このコメントは履修学生全員が閲覧できます。');
		else:
			$sComP = __('※このコメントは回答者本人のみが閲覧できます。');
		endif;
	break;
	endswitch;

	if (preg_match('/^s.+/', $aPut['stID']) && !$bQuick):
?>
<button type="button" class="button default na width-auto VisibleToggle" data="TeachComment"><i class="fa fa-comment fa-flip-horizontal"></i><?php echo __('個別コメント入力'); ?></button>
<?php endif; ?>
<div class="info-box mt8">
<?php
	foreach ($aQuery as $aQQ):
		if (!$aQQ['qqText']):
			continue;
		endif;
		$aA = $aAns[$aQQ['qqNO']];
?>
	<h2 class="QAHeader">
		<span><?php echo __('設問.:no',array('no'=>$aQQ['qqSort'])); ?></span>
		<?php echo ($aQQ['qqRequired'])? ' <div class="font-red font-size-80" style="display: inline; vertical-align: bottom;">('.__('必須').')</div>':''; ?>
	</h2>
	<p class="ml16 font-size-120 line-height-14"><?php echo nl2br($aQQ['qqText']); ?></p>
	<?php if ($aQQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQQ['qbID'].DS.$aQQ['qqNO'].DS.$aQQ['qqImage'])): ?>
	<p><img src="<?php echo DS.CL_UPDIR.DS.$aQQ['qbID'].DS.$aQQ['qqNO'].DS.$aQQ['qqImage']; ?>" style="max-width: 100%; max-height: 240px; width: auto; height: auto;"></p>
	<?php endif; ?>

	<?php if ($aQQ['qqStyle'] != 2): ?>
	<ul class="mt16 QuestAnsChoice">
	<?php
		$aChoice = array();
		for ($i = 1; $i <= (int)$aQQ['qqChoiceNum']; $i++):
			$sColor = ($aA['qaChoice'.$i])? 'check':'default';
			$sCheck = ($aQQ['qqStyle'])? (($aA['qaChoice'.$i])? 'fa-check-square-o':'fa-square-o'):(($aA['qaChoice'.$i])? 'fa-dot-circle-o':'fa-circle-o');
			$aChoice[$i] = '<li class="width-'.$iWidth.'" style=""><label class="'.$sColor.' text-left"><p class="font-size-120"><i class="fa '.$sCheck.' fa-fw"></i>'.nl2br($aQQ['qqChoice'.$i]).'</p>';
			if ($aQQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQQ['qbID'].DS.$aQQ['qqNO'].DS.$aQQ['qqChoiceImg'.$i])):
				$aChoice[$i] .= '<img src="'.DS.CL_UPDIR.DS.$aQQ['qbID'].DS.$aQQ['qqNO'].DS.$aQQ['qqChoiceImg'.$i].'" style="max-width: 100%; max-height: 90px; width: auto; height: auto;">';
			endif;
			$aChoice[$i] .= '</label></li>';
		endfor;
		if ($aQuest['qbQuerySort']):
			krsort($aChoice);
		endif;
		foreach ($aChoice as $sC):
			echo $sC;
		endforeach;
	?>
	</ul>
	<?php endif; ?>
	<?php
		if ($aQQ['qqStyle'] == 2):
			$sText = ($aA['qaText'])? nl2br($aA['qaText']):__('（無回答）');
			$sJsKey = $aQQ['qbID'].'_'.$aQQ['qqNO'].'_'.$aA['stID'];
			switch ((int)$aA['qaPick']):
				case 1:
					$sIcon = 'icon_pick_a.png';
				break;
				case -1:
					$sIcon = 'icon_pick_c.png';
				break;
				default:
					$sIcon = 'icon_pick_b.png';
				break;
			endswitch;
	?>
	<div>
		<?php if (preg_match('/^s.+/', $aPut['stID'])): ?>
		<div class="dropdown mt8">
			<button type="button" class="ans-dropdown-toggle" id="<?php echo $sJsKey; ?>"><div><?php echo Asset::img($sIcon,array('alt'=>'','style'=>'vertical-align: top;','pick'=>(int)$aA['qaPick'])); ?></div></button>
		</div>
		<?php endif; ?>
		<p class="font-size-120 font-green mt8 ml16">
			<?php echo $sText; ?>
		</p>
	</div>
	<?php endif; ?>
	<hr>
<?php endforeach; ?>
</div>


<ul class="dropdown-list dropdown-list-pick" obj="">
	<li><a href="#" class="QuestTextPickUp text-left" obj="1"><?php echo Asset::img('icon_pick_a.png',array('alt'=>__('良回答'),'style'=>'vertical-align: top;')); ?> <?php echo __('良回答'); ?></a></li>
	<li><a href="#" class="QuestTextPickUp text-left" obj="0"><?php echo Asset::img('icon_pick_b.png',array('alt'=>__('可（通常）'),'style'=>'vertical-align: top;')); ?> <?php echo __('可（通常）'); ?></a></li>
	<li><a href="#" class="QuestTextPickUp text-left" obj="-1"><?php echo Asset::img('icon_pick_c.png',array('alt'=>__('不可'),'style'=>'vertical-align: top;')); ?> <?php echo __('不可'); ?></a></li>
</ul>

<?php if (!$bQuick): ?>
<div class="comment-write-box" id="TeachComment" style="visibility: hidden;">
<table>
<tr>
	<td>
		<button class="VisibleToggle" data="TeachComment" style="cursor: pointer;"><i class="fa fa-comment-o fa-2x fa-flip-horizontal"></i></button>
	</td>
	<td>
		<textarea class="comment-write-text" rows="1" placeholder="<?php echo __('先生コメントを入力'); ?>"><?php echo $aPut['qpComment']; ?></textarea>
		<p class="font-size-80 font-black"><?php echo $sComP; ?><?php echo __('（設定の確認/変更は<a href="/t/quest/edit/:qid">コチラ</a>から）',array('qid'=>$aQuest['qbID'])); ?></p>
	</td>
	<td><button type="button" class="button na do TeachCommentUpdate" style="min-width: 1em;" data="<?php echo $aQuest['qbID'].'_'.$aPut['stID']; ?>"><?php echo __('更新'); ?></button></td>
</tr>
</table>
</div>
<?php endif; ?>

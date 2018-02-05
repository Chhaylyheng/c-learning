<div class="width-40 QuestQueryList mb32">
<?php if (isset($iQqNO)): ?>
	<span class="QueryDefaultPanel" obj="<?php echo $aQuest['qbID'].'_'.$iQqNO; ?>"></span>
<?php endif; ?>

<div class="mt4 text-right">
<?php if (count($aQuery)): ?>
	<a href="/t/quest/preview/<?php echo $aQuest['qbID']; ?>" class="button na default text-center PreviewLink width-auto" style="padding: 6px 8px;" target="questpreview"><?php echo __('Preview'); ?></a>
<?php endif; ?>
<?php if (!$bMod): ?>
	<a href="#" class="EditPanelOpen button na default width-auto" style="padding: 6px 8px;" eNO="1"><i class="fa fa-chevron-circle-left"></i><?php echo __('設問追加'); ?></a>
<?php endif; ?>
</div>

	<?php if (count($aQuery)): ?>
	<?php foreach ($aQuery as $aQ): ?>
		<?php
			$sJsKey = $aQ['qbID'].'_'.$aQ['qqNO'];
			$sPadding = ($bMod)? '65px':'165px';
			$sRequired = ($aQ['qqRequired'])? ' <span class="font-red font-size-80">('.__('必須').')</span>':'';

			$aSort = array('','');
			if ($aQ['qqSort'] == 1):
				$aSort[0] = ' disabled="disabled"';
			endif;
			if ($aQ['qqSort'] == count($aQuery)):
				$aSort[1] = ' disabled="disabled"';
			endif;
		?>


<div class="mt8 QPanel">
	<h2 style="position: relative;">
		<a href="#" class="link-out accordion" style="padding-right: <?php echo $sPadding; ?>;">
			<div class="SUP font-blue font-size-100"><?php echo __('設問'); ?><span class="QQS"><?php echo $aQ['qqSort']; ?></span><?php echo $sRequired; ?></div>
			<span class="QQT"><?php echo $aQ['qqText']; ?></span>
		</a>
		<div style="position: absolute; top: 4px; right: 4px;">
			<button type="button" class="button na default width-auto EditPanelModify"  style="padding: 4px 3px;" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
			<?php if (!$bMod): ?>
			<button type="button" class="button na default width-auto QuestQuerySort"   style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"<?php echo $aSort[0]; ?>><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<button type="button" class="button na default width-auto QuestQuerySort"   style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"<?php echo $aSort[1]; ?>><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<button type="button" class="button na default width-auto QuestQueryDelete" style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>"><i class="fa fa-trash-o fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<?php endif; ?>
		</div>
	</h2>
	<div class="accordion-content acc-content-open" style="display: block;">
	<div class="accordion-content-inner pt8">
		<p class="mb8"><?php echo nl2br($aQ['qqText']); ?></p>
		<?php if ($aQ['qqImage'] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'])): ?>
			<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqImage'].'?'.mt_rand(); ?>" style="max-width: 200px; max-height: 150px"></p>
		<?php endif; ?>
		<?php if ($aQ['qqStyle'] != 2): ?>
			<?php $aQCL = array_fill(1, $aQ['qqChoiceNum'], ''); ?>
			<?php if ($aQuest['qbQuerySort'] == 1): ?>
				<?php krsort($aQCL); ?>
			<?php endif; ?>
			<ul class="ListQueryChoice">
			<?php foreach ($aQCL as $i => $v): ?>
				<?php
					$sIcon = ($aQ['qqStyle'] == 1)? 'fa-square-o':'fa-circle-o';
				?>
				<li><button type="button" class="button na default text-left" style="padding: 8px 8px"><i class="fa <?php echo $sIcon; ?>"></i>
				<?php echo nl2br($aQ['qqChoice'.$i]); ?>
				<?php if ($aQ['qqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i])): ?>
					<br><img src="<?php echo DS.CL_UPDIR.DS.$aQ['qbID'].DS.$aQ['qqNO'].DS.$aQ['qqChoiceImg'.$i].'?'.mt_rand(); ?>" style="max-width: 160px; max-height: 120px;">
				<?php endif; ?>
				</button></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<?php echo __('※テキスト入力形式'); ?>
		<?php endif; ?>
	</div>
	</div>
</div>
<?php if (!$bMod): ?>
	<div class="mt4 text-right"><a href="#" class="EditPanelOpen button na default width-auto" style="padding: 6px 8px;" eNO="<?php echo ($aQ['qqSort']+1); ?>"><i class="fa fa-chevron-circle-left"></i><?php echo __('設問追加'); ?></a></div>
<?php endif; ?>
<?php if (is_null($aMsg)): ?>
	<?php if ($aQuest['qbQuickMode']): ?>
	<span class="QueryDefaultPanel" obj="<?php echo $aQuest['qbID'].'_1'; ?>"></span>
	<?php else: ?>
	<span class="QueryDefaultNewPanel" value="<?php echo (count($aQuery) + 1); ?>"></span>
	<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php elseif (is_null($aMsg)): ?>
	<span class="QueryDefaultNewPanel" value="1"></span>
<?php endif; ?>


</div>

		<?php
			$aStyleName = array(
				__('択一形式'),
				__('選択形式（複数回答可）'),
				__('テキスト入力形式')
			);
			$iStyle = 0;
			$sReqChk = '';
			$sPanelDisp = 'none';
			$sChoiceDisp = 'block';
			$aMode = array('add'=>'none','edit'=>'inline');
			$iNO = 1;
			$aStyle = array('','','');
			$sReqClass = 'default';
			$aBaseImg = array('disp'=>'none','img'=>'','src'=>'','value'=>'');
			$aCs = null;
			for ($i = 1; $i <= 50; $i++):
				$aCs[$i] = array('disp'=>'none', 'text'=>'', 'imgdisp'=>'none', 'img'=>'', 'src'=>'', 'value'=>'');
			endfor;
			$iCCnt = 5;

			if (!is_null($aInput)):
				$sImgPath = DS.CL_UPDIR.DS.$aQuest['qbID'].DS.$aInput['qSort'].'_tmp'.DS;
				$sPanelDisp = ($aQuest['qbQuickMode'])? 'none':'block';
				$sChoiceDisp = ($aInput['qType'] != 2)? 'block':'none';
				if ($aInput['qNo']):
					$aMode = array('add'=>'none','edit'=>'inline');
				else:
					$aMode = array('add'=>'inline','edit'=>'none');
				endif;
				$iNO = $aInput['qSort'];
				$aStyle[$aInput['qType']] = ' selected';
				$iStyle = (int)$aInput['qType'];
				$sReqChk = (isset($aInput['qRequired']))? ' checked':'';
				$sReqClass = (isset($aInput['qRequired']))? 'confirm':'default';
				if ($aInput['qqImage']):
					$aBaseImg['disp'] = '';
					$aBaseImg['img'] = $aInput['qqImage'];
					$aBaseImg['src'] = $sImgPath.$aInput['qqImage'].'?'.mt_rand();
					$aBaseImg['value'] = $aQuest['qbID'].'_'.$aInput['qSort'].'_'.$aInput['qqImage'];
				endif;
				if (!is_null($aChoice)):
					$iCCnt = count($aChoice);
					foreach ($aChoice as $i => $v):
						$aCs[$i]['disp'] = 'block';
						$aCs[$i]['text'] = $v;
						if (isset($aImg[$i])):
							$aCs[$i]['imgdisp'] = '';
							$aCs[$i]['img'] = $aImg[$i];
							$aCs[$i]['src'] = $sImgPath.$aImg[$i];
							$aCs[$i]['value'] = $aQuest['qbID'].'_'.$aInput['qSort'].'_'.$aImg[$i];
						endif;
					endforeach;
				endif;
				if ($bMod):
					$iCCnt = $aQQ['qqChoiceNum'];
				endif;
			endif;

			for ($i = 1; $i <= $iCCnt; $i++):
				$aCs[$i]['disp'] = 'block';
			endfor;
		?>

<div class="width-60 QuestQueryForm mb32">
<div class="info-box mt8" style="display: <?php echo $sPanelDisp; ?>;" id="QueryEditPanel">
	<h2>
		<?php echo __('設問'); ?><span id="eNo"><?php echo $iNO; ?></span>
		<span class="eLabelAdd" style="display: <?php echo $aMode['add']; ?>"><?php echo __('追加'); ?></span>
		<span class="eLabelEdit" style="display: <?php echo $aMode['edit']; ?>"><?php echo __('編集'); ?></span>
	</h2>
	<hr>

	<?php if (!is_null($aMsg)): ?>
	<p class="error-box">
	<?php foreach ($aMsg as $sM): ?>
		<?php echo $sM; ?><br>
	<?php endforeach; ?>
	</p>
	<?php endif; ?>

	<form action="/t/quest/queryedit/<?php echo $aQuest['qbID']; ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="qNo" value="<?php echo (isset($aInput['qNo']))? $aInput['qNo']:''; ?>">
		<input type="hidden" name="qSort" value="<?php echo $iNO; ?>">
		<input type="hidden" value="<?php echo (CL_IMGSIZE * 1024 * 1024); ?>" name="MAX_FILE_SIZE">
		<p class="mt16"><?php echo __('回答形式'); ?></p>
		<div class="mt4">
			<?php if (!$bMod): ?>
			<select class="dropdown text-left" name="qType">
				<?php foreach ($aStyleName as $i => $v): ?>
				<option value="<?php echo $i; ?>" class="text-left"<?php echo $aStyle[$i]; ?>><?php echo $v; ?></option>
				<?php endforeach; ?>
			</select>
			<?php else: ?>
			<p class="qType font-blue">
				<?php $sStyleDisp = 'none'; ?>
				<?php foreach ($aStyleName as $i => $v): ?>
				<?php $sStyleDisp = ($i == $iStyle)? 'inline':'none'; ?>
				<span style="display: <?php echo $sStyleDisp; ?>;"><?php echo $v; ?></span>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>
		</div>
		<div class="mt16">
			<label class="button <?php echo $sReqClass; ?> na">
				<input type="checkbox" name="qRequired"<?php echo $sReqChk; ?> value="1" class="RightChoice" style="display: inline-block;" autocomplete="off"><?php echo __('必須回答'); ?>
			</label>
		</div>
		<p class="mt16"><?php echo __('設問文'); ?></p>
		<div class="mt4">
			<textarea name="qText" class="form-control" rows="5"><?php echo (isset($aInput['qText']))? $aInput['qText']:''; ?></textarea>
		</div>

		<p class="mt16"><?php echo __('設問画像（省略可）'); ?></p>
		<div class="profile-icon mt4">
			<p><img src="<?php echo $aBaseImg['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aBaseImg['disp']; ?>;" id="qImage"></p>
			<p>
				<button type="button" class="button default na QuestQueryImageDelete" value="<?php echo $aBaseImg['value']; ?>" style="display: <?php echo $aBaseImg['disp']; ?>; margin-bottom: 8px;" id="qImageDel"><i class="fa fa-trash-o"></i> <?php echo __('画像削除'); ?></button>
				<input type="file" value="" name="qImage">
				<input type="hidden" value="<?php echo $aBaseImg['img']; ?>" name="qqImage">
				<span class="font-size-80"><?php echo __('※:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が設定できます。',array('size'=>CL_IMGSIZE)); ?></span><br>
			</p>
		</div>

		<div class="QueryTypeSelect mt16" style="display: <?php echo $sChoiceDisp; ?>;">
			<p><?php echo __('選択肢'); ?></p>
			<p>
				<?php echo __('※選択肢は50件まで登録可能です。'); ?><br>
				　<span class="font-size-80"><?php echo __('既に回答があるアンケート、またはクイックアンケートの場合は選択肢を増減させることはできません。'); ?></span><br>
				<?php echo __('※各選択肢には:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が登録可能です。',array('size'=>CL_IMGSIZE)); ?><br>
				<?php echo __('※選択肢のテキストが空の場合は選択肢が無視されます。'); ?><br>
				<?php echo __('※テキストのない画像のみの選択肢は作成できません。'); ?>
			</p>
			<?php if (!$bMod): ?>
			<p class="text-right"><button type="button" class="button na default choice-add"><?php echo __('選択肢の追加'); ?></button></p>
			<?php endif; ?>
			<?php for ($i = 1; $i <= 50; $i++): ?>
			<div id="choice<?php echo $i; ?>" class="input-group choice-<?php echo $aCs[$i]['disp']; ?>" style="margin: 0.5em; display: <?php echo $aCs[$i]['disp']; ?>;">
				<p class="mt16"><?php echo __('選択肢'); ?> <?php echo $i; ?></p>
				<p class="mt4">
					<textarea name="qChoice<?php echo $i; ?>" class="form-control" rows="2" style="display: block; float: none;"><?php echo $aCs[$i]['text']; ?></textarea>
				</p>
				<div class="profile-icon mt4">
					<p><img src="<?php echo $aCs[$i]['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aCs[$i]['imgdisp']; ?>;" id="qChoiceImage<?php echo $i; ?>"></p>
					<p>
						<button type="button" class="button na default QuestQueryImageDelete" value="<?php echo $aCs[$i]['value']; ?>" style="display: <?php echo $aCs[$i]['imgdisp']; ?>; margin-bottom: 8px;" id="qChoiceImageDel<?php echo $i; ?>"><i class="fa fa-trash-o"></i> <?php echo __('画像削除'); ?></button>
						<input type="file" value="" name="qChoice<?php echo $i; ?>Image">
						<input type="hidden" value="<?php echo $aCs[$i]['img']; ?>" name="qqChoiceImage<?php echo $i; ?>">
					</p>
				</div>
			</div>
			<?php endfor; ?>
			<?php if (!$bMod): ?>
			<p class="text-right"><button type="button" class="button na default choice-add"><?php echo __('選択肢の追加'); ?></button></p>
			<?php endif; ?>
		</div>
		<hr>
		<div class="button-box">
			<button type="submit" class="button do na" name="sub_state" value="1"><?php echo __('更新'); ?></button>
		</div>
	</form>
</div>
</div>
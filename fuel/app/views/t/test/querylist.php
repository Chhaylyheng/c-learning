<div class="width-40 QuestQueryList mb32">
<?php if (isset($iTqNO) && $bMod): ?>
	<span class="QueryDefaultPanel" obj="<?php echo $aTest['tbID'].'_'.$iTqNO; ?>"></span>
<?php endif; ?>



<div class="mt4 text-right">
<?php if (count($aQuery)): ?>
	<a href="/t/test/preview/<?php echo $aTest['tbID']; ?>" class="button na default text-center PreviewLink width-auto" style="padding: 6px 8px;" target="testpreview">Preview</a>
<?php endif; ?>
<?php if (!$bMod): ?>
	<a href="#" class="EditPanelOpen button na default width-auto" style="padding: 6px 8px;" eNO="1"><i class="fa fa-chevron-circle-left"></i><?php echo __('問題追加'); ?></a>
<?php endif; ?>
</div>

	<?php if (count($aQuery)): ?>
	<?php foreach ($aQuery as $aQ): ?>
		<?php
			$sJsKey = $aQ['tbID'].'_'.$aQ['tqNO'];
			$sPadding = ($bMod)? '65px':'165px';

			$aSort = array('','');
			if ($aQ['tqSort'] == 1):
				$aSort[0] = ' disabled="disabled"';
			endif;
			if ($aQ['tqSort'] == count($aQuery)):
				$aSort[1] = ' disabled="disabled"';
			endif;
		?>


<div class="mt8 QPanel">
	<h2 style="position: relative;">
		<a href="#" class="link-out accordion" style="padding-right: <?php echo $sPadding; ?>;">
			<div class="SUP font-blue font-size-100"><?php echo __('問題'); ?><span class="QQS"><?php echo $aQ['tqSort']; ?></span>（<span class="QQSC"><?php echo $aQ['tqScore']; ?></span><?php echo __('点'); ?>）</div>
			<span class="QQT"><?php echo $aQ['tqText']; ?></span>
		</a>
		<div style="position: absolute; top: 4px; right: 4px;">
			<button type="button" class="button na default width-auto EditPanelModify" style="padding: 4px 3px;" value="<?php echo $sJsKey; ?>"><?php echo __('編集'); ?></button>
			<?php if (!$bMod): ?>
			<button type="button" class="button na default width-auto TestQuerySort"   style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>_up" autocomplete="off"<?php echo $aSort[0]; ?>><i class="fa fa-arrow-circle-o-up fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<button type="button" class="button na default width-auto TestQuerySort"   style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>_down" autocomplete="off"<?php echo $aSort[1]; ?>><i class="fa fa-arrow-circle-o-down fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<button type="button" class="button na default width-auto TestQueryDelete" style="padding: 3px 3px;" value="<?php echo $sJsKey; ?>"><i class="fa fa-trash-o fa-lg" style="margin: 0; vertical-align: top;"></i></button>
			<?php endif; ?>
		</div>
	</h2>
	<div class="accordion-content acc-content-open" style="display: block;">
	<div class="accordion-content-inner pt8">
		<p class="mb8"><?php echo nl2br($aQ['tqText']); ?></p>
		<?php if ($aQ['tqImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'])): ?>
			<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqImage'].'?'.mt_rand(); ?>" style="max-width: 200px; max-height: 150px"></p>
		<?php endif; ?>
		<?php if ($aQ['tqStyle'] != 2): ?>
			<?php
				$aR = explode('|', $aQ['tqRight1']);
			?>
			<?php $aQCL = array_fill(1, $aQ['tqChoiceNum'], ''); ?>
			<ul class="ListQueryChoice">
			<?php foreach ($aQCL as $i => $v): ?>
				<?php
					$sIcon = ($aQ['tqStyle'] == 1)? 'fa-square-o':'fa-circle-o';
					$sColor = (array_search($i, $aR) !== false)? 'confirm':'default';
				?>
				<li><button type="button" class="button na <?php echo $sColor; ?> text-left" style="padding: 8px 8px;"><i class="fa <?php echo $sIcon; ?>"></i>
				<?php echo nl2br($aQ['tqChoice'.$i]); ?>
				<?php if ($aQ['tqChoiceImg'.$i] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i])): ?>
					<br><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqChoiceImg'.$i].'?'.mt_rand(); ?>" style="max-width: 160px; max-height: 120px;">
				<?php endif; ?>
				</button></li>
			<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p>[<?php echo __('正解文字列'); ?>]</p>
			<?php for($i = 1; $i <= 5; $i++): ?>
				<?php if (isset($aQ['tqRight'.$i])): ?>
					<p class="font-green mt4 ml8" style="display: inline-block;"><?php echo $aQ['tqRight'.$i]; ?></p>
				<?php endif; ?>
			<?php endfor;?>
		<?php endif; ?>

		<?php if ($aQ['tqExplain'] || $aQ['tqExplainImage']): ?>
		<div class="mt8 pt8" style="border-top: 1px solid #cbcbcb">
			<?php if ($aQ['tqExplain']): ?>
			<p><?php echo nl2br($aQ['tqExplain']); ?></p>
			<?php endif; ?>
			<?php if ($aQ['tqExplainImage'] && file_exists(CL_UPPATH.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqExplainImage'])): ?>
			<p><img src="<?php echo DS.CL_UPDIR.DS.$aQ['tbID'].DS.$aQ['tqNO'].DS.$aQ['tqExplainImage'].'?'.mt_rand(); ?>" style="max-width: 200px; max-height: 150px"></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	</div>
</div>
<?php if (!$bMod): ?>
	<div class="mt4 text-right"><a href="#" class="EditPanelOpen button na default width-auto" style="padding: 6px 8px;" eNO="<?php echo ($aQ['tqSort']+1); ?>"><i class="fa fa-chevron-circle-left"></i><?php echo __('問題追加'); ?></a></div>
<?php endif; ?>
<?php endforeach; ?>
<?php if (is_null($aMsg) && !$bMod): ?>
	<span class="QueryDefaultNewPanel" value="<?php echo (count($aQuery) + 1); ?>"></span>
<?php endif; ?>
<?php elseif (is_null($aMsg) && !$bMod): ?>
	<span class="QueryDefaultNewPanel" value="1"></span>
<?php endif; ?>


</div>

		<?php
			$aStyleName = array(__('択一形式'),__('選択形式（複数回答可）'),__('テキスト入力形式'));
			$iStyle = 0;
			$sPanelDisp = 'none';
			$sChoiceDisp = 'block';
			$sRightDisp = 'none';
			$aMode = array('add'=>'none','edit'=>'inline');
			$iNO = 1;
			$aStyle = array('','','');
			$aBaseImg = array('disp'=>'none','img'=>'','src'=>'','value'=>'');
			$aExplainImg = array('disp'=>'none','img'=>'','src'=>'','value'=>'');
			$aCs = null;
			$aRs = null;
			for ($i = 1; $i <= 50; $i++):
				$aCs[$i] = array('disp'=>'none', 'text'=>'', 'imgdisp'=>'none', 'img'=>'', 'src'=>'', 'value'=>'', 'check'=>'', 'color'=>'');
			endfor;
			$iCCnt = 5;

			if (!is_null($aInput)):
				$sImgPath = DS.CL_UPDIR.DS.$aTest['tbID'].DS.$aInput['qSort'].'_tmp'.DS;
				$sPanelDisp = 'block';
				$sChoiceDisp = ($aInput['qType'] != 2)? 'block':'none';
				$sRightDisp = ($aInput['qType'] == 2)? 'block':'none';
				if ($aInput['qNo']):
					$aMode = array('add'=>'none','edit'=>'inline');
				else:
					$aMode = array('add'=>'inline','edit'=>'none');
				endif;
				$iNO = $aInput['qSort'];
				$aStyle[$aInput['qType']] = ' selected';
				$iStyle = (int)$aInput['qType'];
				if ($aInput['tqImage']):
					$aBaseImg['disp'] = '';
					$aBaseImg['img'] = $aInput['tqImage'];
					$aBaseImg['src'] = $sImgPath.$aInput['tqImage'].'?'.mt_rand();
					$aBaseImg['value'] = $aTest['tbID'].'_'.$aInput['qSort'].'_'.$aInput['tqImage'];
				endif;
				if ($aInput['tqExplainImage']):
					$aExplainImg['disp'] = '';
					$aExplainImg['img'] = $aInput['tqExplainImage'];
					$aExplainImg['src'] = $sImgPath.$aInput['tqExplainImage'].'?'.mt_rand();
					$aExplainImg['value'] = $aTest['tbID'].'_'.$aInput['qSort'].'_'.$aInput['tqExplainImage'];
				endif;
				if (!is_null($aChoice)):
					$iCCnt = count($aChoice);
					foreach ($aChoice as $i => $v):
						$aCs[$i]['disp'] = 'block';
						$aCs[$i]['text'] = $v;
						if (isset($aRight[$i])):
							$aCs[$i]['check'] = ' checked';
							$aCs[$i]['color'] = 'confirm';
						else:
							$aCs[$i]['check'] = '';
							$aCs[$i]['color'] = 'default';
						endif;
						if (isset($aImg[$i])):
							$aCs[$i]['imgdisp'] = '';
							$aCs[$i]['img'] = $aImg[$i];
							$aCs[$i]['src'] = $sImgPath.$aImg[$i];
							$aCs[$i]['value'] = $aTest['tbID'].'_'.$aInput['qSort'].'_'.$aImg[$i];
						endif;
					endforeach;
				endif;
				if (!is_null($aRightText)):
					foreach ($aRightText as $i => $v):
						$aRs[$i] = $v;
					endforeach;
				endif;
				if ($bMod):
					$iCCnt = $aQQ['tqChoiceNum'];
				endif;
			endif;

			for ($i = 1; $i <= $iCCnt; $i++):
				$aCs[$i]['disp'] = 'block';
			endfor;
		?>

<div class="width-60 QuestQueryForm mb32">
<div class="info-box mt8" style="display: <?php echo $sPanelDisp; ?>;" id="QueryEditPanel">
	<h2><?php echo __('問題'); ?><span id="eNo"><?php echo $iNO; ?></span>
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

	<form action="/t/test/queryedit/<?php echo $aTest['tbID']; ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="qNo" value="<?php echo (isset($aInput['qNo']))? $aInput['qNo']:''; ?>">
		<input type="hidden" name="qSort" value="<?php echo $iNO; ?>">
		<input type="hidden" value="<?php echo (CL_IMGSIZE * 1024 * 1024); ?>" name="MAX_FILE_SIZE">

		<p class="mt16"><?php echo __('配点'); ?></p>
		<div class="mt4">
			<input type="text" name="qScore" value="<?php echo (isset($aInput['qScore']))? $aInput['qScore']:''; ?>" maxlength="3" class="width-6em text-right" style="display: inline;"> 点
		</div>

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
		<p class="mt16"><?php echo __('問題文'); ?></p>
		<div class="mt4">
			<textarea name="qText" class="form-control" rows="5"><?php echo (isset($aInput['qText']))? $aInput['qText']:''; ?></textarea>
		</div>

		<p class="mt16"><?php echo __('問題画像').__('（省略可）'); ?></p>
		<div class="profile-icon mt4">
			<p><img src="<?php echo $aBaseImg['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aBaseImg['disp']; ?>;" id="qImage"></p>
			<p>
				<button type="button" class="button default na TestQueryImageDelete" value="<?php echo $aBaseImg['value']; ?>" style="display: <?php echo $aBaseImg['disp']; ?>; margin-bottom: 8px;" id="qImageDel"><i class="fa fa-trash-o"></i> <?php echo __('画像削除'); ?></button>
				<input type="file" value="" name="qImage">
				<input type="hidden" value="<?php echo $aBaseImg['img']; ?>" name="tqImage">
				<span class="font-size-80"><?php echo __('※:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が設定できます。',array('size'=>CL_IMGSIZE)); ?></span><br>
			</p>
		</div>

		<div class="QueryTypeSelect mt16" style="display: <?php echo $sChoiceDisp; ?>;">
			<p><?php echo __('選択肢'); ?></p>
			<p>
				<?php echo __('※選択肢は50件まで登録可能です。'); ?><br>
				　<span class="font-size-80"><?php echo __('既に解答がある場合は選択肢を増減させることはできません。'); ?></span><br>
				<?php echo __('※各選択肢には:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が登録可能です。',array('size'=>CL_IMGSIZE)); ?><br>
				<?php echo __('※選択肢のテキストが空の場合は選択肢が無視されます。'); ?><br>
				<?php echo __('※テキストのない画像のみの選択肢は作成できません。'); ?>
			</p>
			<?php if (!$bMod): ?>
			<p class="text-right"><button type="button" class="button na default choice-add"><?php echo __('選択肢の追加'); ?></button></p>
			<?php endif; ?>
			<?php for ($i = 1; $i <= 50; $i++): ?>
			<div id="choice<?php echo $i; ?>" class="input-group choice-<?php echo $aCs[$i]['disp']; ?>" style="margin: 0.5em; display: <?php echo $aCs[$i]['disp']; ?>;">
				<p class="mt16"><?php echo __('選択肢'); ?> <?php echo $i; ?>
					<label class="button na <?php echo $aCs[$i]['color']; ?>" style="line-height: 1; padding: 0.2em 0.5em; min-width: 1em; margin-left: 0.5em;"><input type="checkbox" name="qRight<?php echo $i; ?>"<?php echo $aCs[$i]['check']; ?> value="1" class="RightChoice" style="display: inline; vertical-align: middle; margin: 0; padding: 0; line-height: 1; width: auto; margin-right: 0.5em;"><?php echo __('正解'); ?></label>
				</p>
				<p class="mt4">
					<textarea name="qChoice<?php echo $i; ?>" class="form-control" rows="2" style="display: block; float: none;"><?php echo $aCs[$i]['text']; ?></textarea>
				</p>
				<div class="profile-icon mt4">
					<p><img src="<?php echo $aCs[$i]['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aCs[$i]['imgdisp']; ?>;" id="qChoiceImage<?php echo $i; ?>"></p>
					<p>
						<button type="button" class="button na default TestQueryImageDelete" value="<?php echo $aCs[$i]['value']; ?>" style="display: <?php echo $aCs[$i]['imgdisp']; ?>; margin-bottom: 8px;" id="qChoiceImageDel<?php echo $i; ?>"><i class="fa fa-trash-o"></i><?php echo __('画像削除'); ?></button>
						<input type="file" value="" name="qChoice<?php echo $i; ?>Image">
						<input type="hidden" value="<?php echo $aCs[$i]['img']; ?>" name="tqChoiceImage<?php echo $i; ?>">
					</p>
				</div>
			</div>
			<?php endfor; ?>
			<?php if (!$bMod): ?>
			<p class="text-right"><button type="button" class="button na default choice-add"><?php echo __('選択肢の追加'); ?></button></p>
			<?php endif; ?>
		</div>

		<div class="QueryTypeText mt16" style="display: <?php echo $sRightDisp; ?>;">
			<p><?php echo __('正解文字列'); ?></p>
			<p>
				<?php echo __('※正解文字列は5件まで登録可能です。'); ?><br>
				　<span class="font-size-80"><?php echo __('正解文字列に指定された文字列のいずれかに一致する解答が正解となります。 例：[1500][1,500][千五百]'); ?></span><br>
				<?php echo __('※英数字及び記号は自動で半角に変換されます。'); ?><br>
				<?php echo __('※半角カタカナは自動で全角に変換されます。'); ?><br>
			</p>
			<?php for ($i = 1; $i <= 5; $i++): ?>
			<p class="mt8"><?php echo __('正解'); ?> <?php echo $i; ?></p>
			<p class="mt4">
				<input type="text" name="qRightText<?php echo $i; ?>" value="<?php echo $aRs[$i]; ?>" class="TestRightText form-control">
			</p>
			<?php endfor; ?>
		</div>

		<p class="mt16"><?php echo __('解説文').__('（省略可）'); ?></p>
		<div class="mt4">
			<textarea name="qExplain" class="form-control" rows="5"><?php echo (isset($aInput['qExplain']))? $aInput['qExplain']:''; ?></textarea>
		</div>

		<p class="mt16"><?php echo __('解説画像').__('（省略可）'); ?></p>
		<div class="profile-icon mt4">
			<p><img src="<?php echo $aExplainImg['src']; ?>" style="max-width: 200px; max-height: 150px; display: <?php echo $aExplainImg['disp']; ?>;" id="qExplainImage"></p>
			<p>
				<button type="button" class="button default na TestQueryImageDelete" value="<?php echo $aExplainImg['value']; ?>" style="display: <?php echo $aExplainImg['disp']; ?>; margin-bottom: 8px;" id="qExplainImageDel"><i class="fa fa-trash-o"></i> <?php echo __('画像削除'); ?></button>
				<input type="file" value="" name="qExplainImage">
				<input type="hidden" value="<?php echo $aExplainImg['img']; ?>" name="tqExplainImage">
				<span class="font-size-80"><?php echo __('※:sizeMBまでの画像ファイル（JPG,JPEG,GIF,PNG）が設定できます。',array('size'=>CL_IMGSIZE)); ?></span><br>
			</p>
		</div>

		<hr>
		<div class="button-box">
			<button type="submit" class="button do na" name="sub_state" value="1"><?php echo __('更新'); ?></button>
		</div>
	</form>
</div>
</div>
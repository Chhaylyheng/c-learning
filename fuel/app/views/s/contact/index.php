<div class="res-field"></div>
<?php
if (!is_null($aContact)):
?>
<div class="ContactBox">
<?php
foreach ($aContact as $iP => $aThread):
	$aP = $aThread['P'];
	$sWriter = ($aP['coTeach'])? 'font-red':'font-green';
	$sDate = ClFunc_Tz::tz('Y/m/d H:i',$tz,$aP['coDate']);
	$iResNum = (isset($aThread['C']))? count($aThread['C']):0;
	$sJsKey = $iP.'_'.$iP;
	$sBodyDisp = 'none';
	$sNumDisp = ($iResNum)? 'inline-block!important':'none!important';
	$sDelDisp = ($aP['coID'] == $aStudent['stID'])? 'inline-block':'none';
	$sSubject = ($aP['coSubject'])? $aP['coSubject']:'(No subject)';

	$aRead = array('icon'=>'fa-envelope-open-o', 'color'=>'#ffffff','new'=>'');
	if ($aP['coTeach'] && !$aP['coRead']):
		$aRead = array('icon'=>'fa-envelope-o', 'color'=>'#FA8564','new'=>Asset::img('NewMark.png',array('alt'=>'New!')));
	endif;

	$sSubRead = (isset($aNew[$iP]))? 'attn-emp':'attn-info';
	$sNewIcon = (isset($aNew[$iP]))? Asset::img('NewMark.png',array('alt'=>'New!')):'';
	$aRead['new'] = (isset($aNew[$iP]))? '':$aRead['new'];
?>

<div class="co-anchor-block" id="c<?php echo $iP; ?>">
<div class="co-thread-box" style="border-left: 5px solid <?php echo $aRead['color']; ?>;">
<div class="ContactShowThread" obj="<?php echo $iP; ?>" onclick="">
<h2 class="co-thread-title">
	<span><i class="fa <?php echo $aRead['icon']; ?> mr4"></i><?php echo $sSubject; ?></span>
	<?php echo $aRead['new']; ?>
	<span class="co-thread-comnum attention <?php echo $sSubRead; ?> font-size-80" style="display: <?php echo $sNumDisp; ?>;"><?php echo $iResNum; ?></span>
	<?php echo $sNewIcon; ?>
</h2>
<div class="co-thread-details">
	<span class="co-thread-writer <?php echo $sWriter; ?>"><?php echo $aP['coName']; ?></span>
	<span class="thread-date"><?php echo $sDate; ?></span>
</div>
</div>
<div class="co-thread-body ContactDisp" style="display: <?php echo $sBodyDisp; ?>">
	<p class="co-thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aP['coBody'],480)); ?></p>
	<p class="co-thread-text-raw"><?php echo $aP['coBody']; ?></p>
</div>
<div class="co-thread-option ContactDisp" style="display: <?php echo $sBodyDisp; ?>">
<button type="button" class="button na do width-auto ContactReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('返信する'); ?></button>
<button type="button" class="button na default width-auto ContactDelete ml4" style="padding: 3px 8px;  vertical-align: middle; display: <?php echo $sDelDisp; ?>;" value="<?php echo $sJsKey; ?>"><i class="fa fa-trash-o mr4"></i><?php echo __('削除'); ?></button>
</div>
<div class="co-thread-res"></div>

<ul class="co-comment-list ContactDisp" style="display: <?php echo $sBodyDisp; ?>">
<?php
	if (isset($aThread['C'])):
		ksort($aThread['C']);
		foreach ($aThread['C'] as $iC => $aC):
			$sWriter = ($aC['coTeach'])? 'font-red':'font-green';
			$sDate = ClFunc_Tz::tz('Y/m/d H:i',$tz,$aC['coDate']);
			$sJsKey = $iP.'_'.$iC;
			$sDelDisp = ($aC['coID'] == $aStudent['stID'])? 'inline-block':'none';
			$sSubject = ($aC['coSubject'])? $aC['coSubject']:'(No subject)';

			$sNewIcon = '';
			$aRead = array('icon'=>'fa-envelope-open-o', 'color'=>'#dddddd');
			if ($aC['coID'] != $aStudent['stID'] && !$aC['coRead']):
				$aRead = array('icon'=>'fa-envelope-o', 'color'=>'#FA8564');
				$sNewIcon = Asset::img('NewMark.png',array('alt'=>'New!'));
			endif;
?>
<li class="co-anchor-block" id="c<?php echo $iC; ?>"><span class="co-tree-line"></span>
<div class="co-thread-box" style="border-left: 5px solid <?php echo $aRead['color']; ?>;">
<h2 class="co-thread-title"><i class="fa <?php echo $aRead['icon']; ?> mr4"></i><?php echo $sSubject.' '.$sNewIcon; ?></h2>
<div class="co-thread-details">
	<span class="co-thread-writer <?php echo $sWriter; ?>"><?php echo $aC['coName']; ?></span>
	<span class="thread-date"><?php echo $sDate; ?></span>
</div>
<div class="co-thread-body">
	<p class="co-thread-text"><?php echo nl2br(\Clfunc_Common::url2link($aC['coBody'],480)); ?></p>
	<p class="co-thread-text-raw"><?php echo $aC['coBody']; ?></p>
</div>
<div class="co-thread-option">
<button type="button" class="button na do width-auto ContactReplyTo ml4" style="padding: 4px 8px; vertical-align: middle;" value="<?php echo $sJsKey; ?>"><?php echo __('返信する'); ?></button>
<button type="button" class="button na default width-auto ContactDelete ml4" style="padding: 3px 8px;  vertical-align: middle; display: <?php echo $sDelDisp; ?>;" value="<?php echo $sJsKey; ?>"><i class="fa fa-trash-o mr4"></i><?php echo __('削除'); ?></button>
</div>
<div class="co-thread-res"></div>
</div>
</li>
<?php
		endforeach;
?>
</ul>
<?php
	endif;
?>
</div>
</div>
<?php
endforeach;
?>
</div>
<?php
else:
?>
<div class="info-box mt16"><p><?php echo __('連絡・相談はありません。'); ?></p></div>
<?php
endif;
?>

<form action="/s/contact/res" method="post" class="res-box width-100" style="display: none;">
	<input type="hidden" name="c_no" value="0">
	<input type="hidden" name="c_before" value="0">
	<input type="hidden" name="mode" value="input">
	<input type="hidden" name="ct" value="<?php echo $aClass['ctID']; ?>">
	<div class="res-msg-box"></div>
	<div class="formControl font-size-90 width-100" style="margin: auto;">
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('件名'); ?></div>
			<div class="formContent inline-box width-100">
				<input type="text" name="c_subject" value="" maxlength="<?php echo CL_TITLE_LENGTH; ?>" placeholder="<?php echo __('件名を入力します'); ?>" class="width-100 text-left">
			</div>
		</div>
		<div class="formGroup width-100">
			<div class="formLabel" style="width: 9em; min-width: 9em;"><?php echo __('本文'); ?><sup>*</sup></div>
			<div class="formContent inline-box">
				<textarea name="c_text" class="width-100 text-left font-size-100" rows="6"></textarea>
				<p class="note mt8"><?php echo __('※先生宛にメールが送信されます。'); ?></p>
			</div>
		</div>
	</div>
	<div class="res-button-box">
		<button type="submit" class="button do na width-auto ContactReplyToSubmit font-size-90 ToContact" style="padding: 4px 8px; display: none;"><?php echo __('連絡・相談する'); ?></button>
		<button type="submit" class="button do na width-auto ContactReplyToSubmit font-size-90 ReplyTo" style="padding: 4px 8px; display: none;"><?php echo __('返信する'); ?></button>
		<button type="button" class="button default na width-auto ContactReplyToQuote font-size-90" style="padding: 4px 8px;"><?php echo __('引用'); ?></button>
		<button type="button" class="button default na width-auto ContactToCancel font-size-90" style="padding: 4px 8px;"><?php echo __('キャンセル'); ?></button>
	</div>
</form>

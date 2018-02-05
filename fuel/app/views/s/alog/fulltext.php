<?php if (is_null($aALTheme)): ?>
<div class="mt0 info-box">
	<p><?php echo __('公開されている活動履歴テーマがありません。'); ?></p>
</div>
<?php else: ?>

<?php $disp = (!is_null($aALogList))? 'none':'block'; ?>

<div class="mt0">
	<h2><a href="#" class="link-out accordion" style="padding: 6px 0 6px 30px; background-position: 8px center;"><?php echo __('表示条件設定'); ?></a></h2>
	<div class="accordion-content acc-content-open" style="display: <?php echo $disp; ?>;">
	<div class="accordion-content-inner pt8">
<?php echo Form::open(array('action'=>'/s/alog/fulltext','method'=>'post','role'=>'form','class'=>'form-inline')) ; ?>
<div class="form-group">
	<p class="mt4">
		<?php echo __('期間'); ?>
		<input type="text" name="sd" value="<?php echo date('Y/m/d',strtotime($aY[0])); ?>" id="datepick1" class="width-10em text-center inline-block"> ～
		<input type="text" name="ed" value="<?php echo date('Y/m/d',strtotime($aY[1])); ?>" id="datepick2" class="width-10em text-center inline-block">
	</p>
	<p class="mt4">
	<?php echo __('キーワード検索'); ?>
	<input type="text" name="w" value="<?php echo $sWords; ?>" placeholder="" class="width-30em inline-block">
	</p>
	<p class="mt4">
	<?php echo __('テーマ選択'); ?>
	<select class="dropdown text-left" name="alt">
		<option value="0" class="text-left"><?php echo __('全て'); ?></option>
		<?php foreach ($aALTheme as $sAltID => $aT): ?>
		<?php $sSel = ($sAltID == $sAlt)? ' selected':''; ?>
		<option value="<?php echo $sAltID; ?>" class="text-left"<?php echo $sSel; ?>><?php echo $aT['altName']; ?></option>
		<?php endforeach; ?>
	</select>
	</p>
</div>
<div class="form-group mt8 button-box text-left">
	<button type="submit" class="button na do width-auto" style="padding: 8px;" name="sub_state" value="1"><?php echo __('表示条件設定'); ?></button>
</div>
<?php echo Form::close(); ?>
	</div>
	</div>
</div>

<?php if (!is_null($aALogList)): ?>
<div class="mt8 info-box" style="z-index: 1;">

<h2><?php echo __(':num件の記録が見つかりました。', array('num'=>count($aALogList))); ?></h2>

<?php
foreach ($aALogList as $aL):
	$aT = $aALTheme[$aL['altID']];
	$sAltID = $aL['altID'];
	$iNO = $aL['no'];

	if ($aT['altFile']):
		$sPath = '';
		$sSize = '';
		if ($aL['fID'] != ''):
			$sPath = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'e'));
			$sSize = \Clfunc_Common::FilesizeFormat($aL['fSize'],1);
			$sIcon = 'paperclip';
			$sThumb = null;
			if ($aL['fFileType'] == 2):
				$sThumb = \Uri::create('getfile/s3file/:fid/:mode',array('fid'=>$aL['fID'],'mode'=>'t'));
				$sIcon = 'film';
			endif;
			$sFile = '<p><i class="fa fa-'.$sIcon.'"></i> <a href="'.$sPath.'" target="_blank">'.Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）</a></p>';
		endif;
	endif;
?>
<ul class="ALogText">
<li class="ALTitle">
	<a href="/s/alog/edit/<?php echo $sAltID.DS.$iNO; ?>" title="<?php echo __('編集'); ?>"><i class="fa fa-pencil"></i></a>
	<span class="font-bold"><?php echo $aT['altName']; ?></span> <span class="font-size-80"><?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aL['alDate']); ?></span>
	<?php if ($aT['altTitle']): ?>
		<?php echo ($aL['alTitle'])? '<h3>'.Clfunc_Common::SearchWordsReplace($sWords,$aL['alTitle']).'</h3>':''; ?>
	<?php endif; ?>
</li>
<?php if ($aT['altRange']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aT['altRangeLabel']; ?>】</p>
	<?php if ($aL['alStart'] && $aL['alStart'] != CL_DATETIME_DEFAULT): ?>
	<?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aL['alStart']); ?> ～ <?php echo ClFunc_Tz::tz('Y/m/d H:i',$tz,$aL['alEnd']); ?>
	<?php endif; ?>
</li>
<?php endif; ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aT['altTextLabel']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alText'])); ?>
</li>
<?php if ($aT['altFile']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aT['altFileLabel']; ?>】</p>
	<?php if ($sPath): ?>
	<div class="" style="max-width: 640px;">
	<?php
			switch ($aL['fFileType']):
				case 2:	# 映像の場合
	?>
	<video class="width-100"  controls="controls" preload="none" src="<?php echo $sPath; ?>" poster="<?php echo $sThumb; ?>"></video><br>
	<?php echo Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）'; ?>
	<?php
				break;
				case 1:
	?>
	<img class="width-100" src="<?php echo $sPath; ?>" alt="<?php echo $aL['fName'].'（'.$sSize.'）'; ?>"><br>
	<?php echo Clfunc_Common::SearchWordsReplace($sWords,$aL['fName']).'（'.$sSize.'）'; ?>
	<?php
				break;
				default:
					echo $sFile;
				break;
			endswitch;
	?>
	</div>
	<?php endif; ?>
	</li>
<?php endif; ?>
<?php if ($aT['altOpt1']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aT['altOpt1Label']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt1'])); ?>
</li>
<?php endif; ?>
<?php if ($aT['altOpt2']): ?>
<li class="ALText">
	<p class="font-bold font-green">【<?php echo $aT['altOpt2Label']; ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alOpt2'])); ?>
</li>
<?php endif; ?>
<li class="ALComment">
	<p class="font-bold font-green">【<?php echo __('先生コメント'); ?>】</p>
	<?php echo nl2br(Clfunc_Common::SearchWordsReplace($sWords,$aL['alCom'])); ?>
</li>

</ul>

<?php endforeach; ?>

</div>
<?php endif; ?>

<?php endif; ?>
